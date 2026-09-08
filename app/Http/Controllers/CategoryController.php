<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $incomeCategories = $user->categories()
            ->where('type', 'income')
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $expenseCategories = $user->categories()
            ->where('type', 'expense')
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact(
            'incomeCategories',
            'expenseCategories'
        ));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->categories()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'],
        ]);

        return redirect()
            ->route('categories')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $category->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'],
        ]);

        return redirect()
            ->route('categories')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $transactionCount = $category->transactions()->count();

        if ($transactionCount > 0) {
            return redirect()
                ->route('categories')
                ->with('error', "Kategori ini masih digunakan oleh {$transactionCount} transaksi. Tidak dapat dihapus.");
        }

        $category->delete();

        return redirect()
            ->route('categories')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
