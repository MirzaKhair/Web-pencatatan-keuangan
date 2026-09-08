<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $allCategories = $user->categories()->orderBy('name')->get();

        $incomeCategories = $user->categories()
            ->where('type', 'income')
            ->orderBy('name')
            ->get();

        $expenseCategories = $user->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $query = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'transactions.*',
                'categories.name as category_name',
                'categories.type as category_type',
                'categories.icon as category_icon'
            );

        $categoryId = $request->input('category_id');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($categoryId) {
            $query->where('transactions.category_id', $categoryId);
        }

        if ($type) {
            $query->where('categories.type', $type);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('transactions.transaction_date', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->whereDate('transactions.transaction_date', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->whereDate('transactions.transaction_date', '<=', $dateTo);
        }

        $transactions = $query
            ->orderBy('transactions.transaction_date', 'desc')
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', compact(
            'allCategories',
            'incomeCategories',
            'expenseCategories',
            'transactions'
        ));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->transactions()->create([
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('transactions')
            ->with('status', 'Transaksi berhasil ditambahkan.');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $transaction->update([
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('transactions')
            ->with('status', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()
            ->route('transactions')
            ->with('status', 'Transaksi berhasil dihapus.');
    }
}