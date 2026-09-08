<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        $query = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'transactions.*',
                'categories.name as category_name',
                'categories.type as category_type',
                'categories.icon as category_icon'
            );

        if ($dateFrom && $dateTo) {
            $query->whereBetween('transactions.transaction_date', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->whereDate('transactions.transaction_date', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->whereDate('transactions.transaction_date', '<=', $dateTo);
        }

        if ($type) {
            $query->where('categories.type', $type);
        }

        if ($categoryId) {
            $query->where('transactions.category_id', $categoryId);
        }

        $summaryQuery = clone $query;

        $totalIncome = (clone $summaryQuery)
            ->where('categories.type', 'income')
            ->sum('transactions.amount');

        $totalExpense = (clone $summaryQuery)
            ->where('categories.type', 'expense')
            ->sum('transactions.amount');

        $difference = $totalIncome - $totalExpense;

        $transactions = $query
            ->orderBy('transactions.transaction_date', 'desc')
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $allCategories = $user->categories()->orderBy('name')->get();

        return view('reports.index', compact(
            'transactions',
            'allCategories',
            'totalIncome',
            'totalExpense',
            'difference',
            'dateFrom',
            'dateTo',
            'type',
            'categoryId'
        ));
    }
}
