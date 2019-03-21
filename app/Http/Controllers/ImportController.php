<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use League\Csv\Reader;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function showImportForm()
    {
        return view('import');
    }

    public function runImport(Request $request)
    {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        $csv = Reader::createFromPath($request->file('file')->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        try {
            DB::transaction(function() use(&$csv) {
                $date = null;
                $description = null;
                $entries = [];

                foreach ($csv as $row) {
                    if ($row['description'] && $description) {
                        $this->createTransaction($date, $description, $entries);
                        $date = $row['date'];
                        $description = $row['description'];
                        $entries = [];
                    } else if ($description === null) {
                        $date = $row['date'];
                        $description = $row['description'];
                    }

                    $entries[] = [
                        'account' => $row['account'],
                        'debit' => $row['debit'],
                        'credit' => $row['credit']
                    ];
                }

                if ($description) {
                    $this->createTransaction($date, $description, $entries);
                }
            }, 3);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Import completed successfully.');
    }

    private function createTransaction(string $date, string $description, array $entries)
    {
        $balance = 0.0;
        $entryCount = 0;

        foreach ($entries as $entry) {
            if (!$entry['account']) {
                continue;
            } else if ($entry['debit'] && $entry['debit'] > 0.0) {
                $balance += $entry['debit'];
            } else if ($entry['credit'] && $entry['credit'] > 0.0) {
                $balance -= $entry['credit'];
            } else {
                continue;
            }

            ++$entryCount;
        }

        if ($entryCount < 2 || $balance != 0) {
            throw new \Exception('Invalid transaction ' . $date . ' ' . $description);
        }

        $transaction = new Transaction;
        $transaction->date = Carbon::parse($date);
        $transaction->description = $description;
        $transaction->save();

        foreach ($entries as $entry) {
            if (!$entry['account']) {
                continue;
            }

            $account = Account::lockForUpdate()->where('name', $entry['account'])->first();

            if (!$account) {
                throw new \Exception('Account \'' . $entry['account'] . '\' does not exist.');
            }

            if ($entry['debit'] && $entry['debit'] > 0.0) {
                $amount = ($account->isDebit() ? 1.0 : -1.0) * $entry['debit'];
            } else if ($entry['credit'] && $entry['credit'] > 0.0) {
                $amount = ($account->isCredit() ? 1.0 : -1.0) * $entry['credit'];
            } else {
                continue;
            }

            $account->balance += $amount;
            $account->save();

            $journalEntry = new JournalEntry;
            $journalEntry->transaction()->associate($transaction);
            $journalEntry->account()->associate($account);
            $journalEntry->amount = $amount;
            $journalEntry->save();
        }
    }
}
