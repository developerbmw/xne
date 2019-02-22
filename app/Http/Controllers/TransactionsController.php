<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index()
    {
        return view('transactions.index', [
            'transactions' => Transaction::orderBy('date', 'desc')->paginate(20)
        ]);
    }

    public function create()
    {
        return view('transactions.create', [
            'accounts' => Account::orderBy('type')->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $result = $this->validateTransaction($request);

        if ($result) {
            return $result;
        }

        DB::transaction(function() use(&$request) {
            $transaction = new Transaction;
            $transaction->date = $request->date;
            $transaction->description = $request->description;
            $transaction->save();

            foreach ($request->entries as $entry) {
                if (!$entry['account']) {
                    continue;
                }

                $account = Account::lockForUpdate()->findOrFail($entry['account']);

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
        }, 3);

        return redirect()->route('transactions.index')->with(['success' => __('Transaction created.')]);
    }

    public function show($id)
    {
        return view('transactions.show', [
            'transaction' => Transaction::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        return view('transactions.edit', [
            'transaction' => Transaction::findOrFail($id),
            'accounts' => Account::orderBy('type')->orderBy('name')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $result = $this->validateTransaction($request);

        if ($result) {
            return $result;
        }

        DB::transaction(function() use(&$request, $id) {
            $transaction = Transaction::lockForUpdate()->findOrFail($id);
            $transaction->date = $request->date;
            $transaction->description = $request->description;
            $transaction->save();

            $entryIds = [];

            foreach ($request->entries as $entry) {
                if (!$entry['account']) {
                    continue;
                }

                if ($entry['id']) {
                    $journalEntry = JournalEntry::lockForUpdate()->findOrFail($entry['id']);
                    $account = $journalEntry->account()->lockForUpdate()->first();
                    $account->balance -= $journalEntry->amount;
                    $account->save();
                } else {
                    $journalEntry = new JournalEntry;
                    $journalEntry->transaction()->associate($transaction);
                }

                $account = Account::lockForUpdate()->findOrFail($entry['account']);

                if ($entry['debit'] && $entry['debit'] > 0.0) {
                    $amount = ($account->isDebit() ? 1.0 : -1.0) * $entry['debit'];
                } else if ($entry['credit'] && $entry['credit'] > 0.0) {
                    $amount = ($account->isCredit() ? 1.0 : -1.0) * $entry['credit'];
                } else {
                    continue;
                }

                $account->balance += $amount;
                $account->save();

                $journalEntry->account()->associate($account);
                $journalEntry->amount = $amount;
                $journalEntry->save();
                $entryIds[] = $journalEntry->id;
            }

            $transaction->journalEntries()->whereNotIn('id', $entryIds)->get()->each(function($entry) {
                $account = $entry->account()->lockForUpdate()->first();
                $account->balance -= $entry->amount;
                $account->save();
                $entry->delete();
            });
        }, 3);

        return redirect()->route('transactions.show', $id)->with(['success' => __('Transaction updated.')]);
    }

    public function destroy($id)
    {
        DB::transaction(function() use(&$request, $id) {
            $transaction = Transaction::lockForUpdate()->findOrFail($id);

            $transaction->journalEntries()->get()->each(function($entry) {
                $account = $entry->account()->lockForUpdate()->first();
                $account->balance -= $entry->amount;
                $account->save();
                $entry->delete();
            });

            $transaction->delete();
        }, 3);

        return redirect()->route('transactions.index')->with('success', __('Transaction deleted.'));
    }

    private function validateTransaction(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'description' => 'required|string',
            'entries' => 'required|array',
            'entries.*.account' => 'integer|exists:accounts,id|nullable'
        ]);

        $balance = 0.0;
        $entryCount = 0;

        foreach ($request->entries as $entry) {
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

        $error = null;

        if ($entryCount < 2) {
            $error = __('You must enter at least two journal entries for the transaction.');
        } else if ($balance != 0) {
            $error = __('Total debits do not match total credits.');
        }

        if ($error) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        return null;
    }
}
