<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function index()
    {
        return view('accounts.index', [
            'accounts' => Account::orderBy('type')->orderBy('name')->paginate(20)
        ]);
    }

    public function create()
    {
        return view('accounts.create', [
            'accountTypes' => Account::getTypes()
        ]);
    }

    public function store(Request $request)
    {
        $this->validateAccount($request);

        $account = new Account;
        $account->name = $request->name;
        $account->type = $request->type;
        $account->save();

        return redirect()->route('accounts.index')->with(['success' => __('Account created.')]);
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);

        $journalEntries = DB::table('journal_entries')
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->where('account_id', $account->id)
            ->select('transaction_id', 'date', 'description', 'amount')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $journalEntries->each(function($entry) {
            $entry->date = Carbon::parse($entry->date);
        });

        return view('accounts.show', [
            'account' => $account,
            'journalEntries' => $journalEntries
        ]);
    }

    public function edit($id)
    {
        return view('accounts.edit', [
            'account' => Account::findOrFail($id),
            'accountTypes' => Account::getTypes()
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validateAccount($request);

        $account = Account::findOrFail($id);
        $account->name = $request->name;
        $account->type = $request->type;
        $account->save();

        return redirect()->route('accounts.show', $account)->with(['success' => __('Account updated.')]);
    }

    public function destroy($id)
    {

    }

    private function validateAccount(Request $request)
    {
        $validTypes = implode(',', array_keys(Account::getTypes()));

        $request->validate([
            'name' => 'required|string|min:3|unique:accounts,name',
            'type' => 'required|integer|in:' . $validTypes
        ]);
    }
}
