<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $period = $request->query('period', 'all');

        $totalIncomeAll = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.type', 'income')
            ->sum('transactions.amount');

        $totalExpenseAll = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('categories.type', 'expense')
            ->sum('transactions.amount');

        $currentBalance = $user->initial_balance + $totalIncomeAll - $totalExpenseAll;

        $periodQuery = $this->buildPeriodQuery($user, $period);

        $totalIncome = (clone $periodQuery)
            ->where('categories.type', 'income')
            ->sum('transactions.amount');

        $totalExpense = (clone $periodQuery)
            ->where('categories.type', 'expense')
            ->sum('transactions.amount');

        $transactions = (clone $periodQuery)
            ->select(
                'transactions.*',
                'categories.name as category_name',
                'categories.type as category_type',
                'categories.icon as category_icon'
            )
            ->orderBy('transactions.transaction_date', 'desc')
            ->orderBy('transactions.created_at', 'desc')
            ->limit(5)
            ->get();

        $chartData = $this->getChartData($user, $period);

        $categorySummary = $this->getCategorySummary($user, $period);

        return view('dashboard', array_merge(compact(
            'currentBalance',
            'totalIncome',
            'totalExpense',
            'transactions',
            'period',
            'chartData'
        ), $categorySummary));
    }

    private function buildPeriodQuery($user, string $period)
    {
        $query = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id');

        switch ($period) {
            case 'today':
                $query->whereDate('transaction_date', today());
                break;
            case 'week':
                $query->whereBetween('transaction_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ]);
                break;
            case 'month':
                $query->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
                break;
            case 'year':
                $query->whereYear('transaction_date', now()->year);
                break;
        }

        return $query;
    }

    private function getChartData($user, string $period)
    {
        $query = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id');

        switch ($period) {
            case 'today':
                $query->selectRaw("HOUR(transactions.transaction_date) as label")
                    ->whereDate('transaction_date', today());
                break;
            case 'week':
                $query->selectRaw("DATE(transactions.transaction_date) as label")
                    ->whereBetween('transaction_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]);
                break;
            case 'month':
                $query->selectRaw("DATE(transactions.transaction_date) as label")
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
                break;
            case 'year':
                $query->selectRaw("MONTH(transactions.transaction_date) as label")
                    ->whereYear('transaction_date', now()->year);
                break;
            default:
                $query->selectRaw("DATE_FORMAT(transactions.transaction_date, '%Y-%m') as label");
        }

        return $query
            ->selectRaw("categories.type as category_type")
            ->selectRaw("SUM(transactions.amount) as total")
            ->groupBy('label', 'categories.type')
            ->orderBy('label')
            ->get();
    }

    private function getCategorySummary($user, string $period): array
    {
        $periodQuery = $this->buildPeriodQuery($user, $period);

        $incomeSummary = (clone $periodQuery)
            ->where('categories.type', 'income')
            ->select(
                'categories.name as name',
                'categories.icon as icon',
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('categories.name', 'categories.icon')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $expenseSummary = (clone $periodQuery)
            ->where('categories.type', 'expense')
            ->select(
                'categories.name as name',
                'categories.icon as icon',
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('categories.name', 'categories.icon')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return compact('incomeSummary', 'expenseSummary');
    }
}