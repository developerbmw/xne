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
        'Income and Expenses'
    ];

    public function index()
    {
        return view('reports.index', [
            'reports' => $this->reports
        ]);
    }

    public function run(Request $request)
    {
        $request->validate([
            'report' => 'required|in:' . implode(',', $this->reports),
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d'
        ]);

        $slug = str_replace(' ', '', ucwords($request->report));

        return $this->{'get' . $slug . 'Report'}(Carbon::parse($request->start_date), Carbon::parse($request->end_date));
    }

    public function getIncomeAndExpensesReport(Carbon $start, Carbon $end)
    {
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
}
