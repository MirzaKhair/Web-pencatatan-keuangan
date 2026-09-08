<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $expenseCategories = $user->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $years = range(now()->year - 5, now()->year + 5);

        return view('budgets.index', compact(
            'budgets',
            'expenseCategories',
            'month',
            'year',
            'months',
            'years'
        ));
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $duplicate = $request->user()->budgets()
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($duplicate) {
            return redirect()
                ->route('budgets', ['month' => $validated['month'], 'year' => $validated['year']])
                ->with('error', 'Anggaran untuk kategori ini pada periode yang sama sudah ada.');
        }

        $request->user()->budgets()->create([
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        return redirect()
            ->route('budgets', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('status', 'Anggaran berhasil ditambahkan.');
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $duplicate = $request->user()->budgets()
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $budget->id)
            ->exists();

        if ($duplicate) {
            return redirect()
                ->route('budgets', ['month' => $validated['month'], 'year' => $validated['year']])
                ->with('error', 'Anggaran untuk kategori ini pada periode yang sama sudah ada.');
        }

        $budget->update([
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        return redirect()
            ->route('budgets', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('status', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        $month = $budget->month;
        $year = $budget->year;

        $budget->delete();

        return redirect()
            ->route('budgets', ['month' => $month, 'year' => $year])
            ->with('status', 'Anggaran berhasil dihapus.');
    }
}
