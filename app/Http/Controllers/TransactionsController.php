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

                if ($entry['debit'] && $entry['debit'] > 0) {
                    $amount = $entry['debit'];
                } else {
                    $amount = -1 * $entry['credit'];
                }

                if (!$account->isDebit()) {
                    $amount *= -1;
                }

                $account->balance += $amount;
                $account->save();

                $journalEntry = new JournalEntry;
                $journalEntry->transaction()->associate($transaction);
                $journalEntry->account()->associate($account);
                $journalEntry->amount = $amount;
                $journalEntry->save();
            }
        });

        return redirect()->route('transactions.index')->with(['success' => __('Transaction created.')]);
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }

    public function validateTransaction(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'description' => 'required|string',
            'entries' => 'required|array',
            'entries.*.account' => 'integer|exists:accounts,id|nullable'
        ]);

        $balance = 0.0;

        foreach ($request->entries as $entry) {
            if (!$entry['account']) {
                continue;
            }

            if ($entry['debit'] && $entry['debit'] > 0) {
                $balance += $entry['debit'];
            } else if ($entry['credit']) {
                $balance -= $entry['credit'];
            }
        }

        if ($balance != 0) {
            return redirect()->back()->withInput()->with('error', 'Total debits do not match total credits.');
        }

        return null;
    }
}
