<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    private $reports = [
        'Income and Expenses',
        'Single Account'
    ];

    public function index()
    {
        return view('reports.index', [
            'reports' => $this->reports,
            'accounts' => Account::orderBy('type')->orderBy('name')->get()
        ]);
    }

    public function run(Request $request)
    {
        $request->validate([
            'report' => 'required|in:' . implode(',', $this->reports)
        ]);

        $slug = str_replace(' ', '', ucwords($request->report));

        return $this->{'get' . $slug . 'Report'}($request);
    }

    public function getIncomeAndExpensesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d'
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        $income = DB::table('journal_entries')
            ->leftJoin('accounts', 'accounts.id', '=', 'journal_entries.account_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->where('accounts.type', [Account::TYPE_INCOME])
            ->where('transactions.date', '>=', $start)
            ->where('transactions.date', '<=', $end)
            ->groupBy('accounts.id')
            ->orderBy('accounts.name')
            ->select(
                'accounts.name',
                DB::raw('SUM(journal_entries.amount) AS total')
            )
            ->get();

        $expenses = DB::table('journal_entries')
            ->leftJoin('accounts', 'accounts.id', '=', 'journal_entries.account_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->where('accounts.type', [Account::TYPE_EXPENSE])
            ->where('transactions.date', '>=', $start)
            ->where('transactions.date', '<=', $end)
            ->groupBy('accounts.id')
            ->orderBy('accounts.name')
            ->select(
                'accounts.name',
                DB::raw('SUM(journal_entries.amount) AS total')
            )
            ->get();

        return view('reports.incomeandexpenses', [
            'start' => $start,
            'end' => $end,
            'income' => $income,
            'totalIncome' => $income->sum('total'),
            'expenses' => $expenses,
            'totalExpenses' => $expenses->sum('total')
        ]);
    }

    public function getSingleAccountReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'account' => 'required|integer|exists:accounts,id'
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $account = Account::findOrFail($request->account);

        $changesInPeriod = DB::table('journal_entries')
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->where('account_id', $account->id)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('amount');

        $changesAfterPeriod = DB::table('journal_entries')
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->where('account_id', $account->id)
            ->where('date', '>', $end)
            ->sum('amount');

        $closingBalance = $account->balance - $changesAfterPeriod;
        $openingBalance = $closingBalance - $changesInPeriod;

        $journalEntries = DB::table('journal_entries')
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->where('account_id', $account->id)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->select('transaction_id', 'date', 'description', 'amount')
            ->orderBy('date', 'desc')
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        $journalEntries->each(function($entry) {
            $entry->date = Carbon::parse($entry->date);
        });

        return view('reports.singleaccount', [
            'start' => $start,
            'end' => $end,
            'account' => $account,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'journalEntries' => $journalEntries
        ]);
    }
}
