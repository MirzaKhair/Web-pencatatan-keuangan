<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SetupInitialBalanceController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();

        if (! is_null($user->initial_balance_setup_at)) {
            return redirect()->route('dashboard');
        }

        return view('setup-initial-balance');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! is_null($user->initial_balance_setup_at)) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'initial_balance' => 'required|numeric|min:0',
            'initial_balance_date' => 'required|date|before_or_equal:today',
        ]);

        $defaultCategories = [
            ['name' => 'Makanan', 'type' => 'expense', 'icon' => 'utensils'],
            ['name' => 'Minuman', 'type' => 'expense', 'icon' => 'cup-soda'],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'bus'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'shopping-cart'],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'gamepad-2'],
            ['name' => 'Pendidikan', 'type' => 'expense', 'icon' => 'graduation-cap'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'heart-pulse'],
            ['name' => 'Tagihan', 'type' => 'expense', 'icon' => 'lightbulb'],
            ['name' => 'Lainnya', 'type' => 'expense', 'icon' => 'package'],
            ['name' => 'Gaji', 'type' => 'income', 'icon' => 'banknote'],
            ['name' => 'Bonus', 'type' => 'income', 'icon' => 'gift'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'briefcase-business'],
            ['name' => 'Uang Saku', 'type' => 'income', 'icon' => 'wallet'],
            ['name' => 'Investasi', 'type' => 'income', 'icon' => 'trending-up'],
            ['name' => 'Lainnya', 'type' => 'income', 'icon' => 'coins'],
        ];

        DB::transaction(function () use ($user, $validated, $defaultCategories) {
            $user->update([
                'initial_balance' => $validated['initial_balance'],
                'initial_balance_date' => $validated['initial_balance_date'],
                'initial_balance_setup_at' => now(),
            ]);

            foreach ($defaultCategories as $category) {
                $user->categories()->create($category);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Saldo awal berhasil disimpan dan kategori bawaan telah dibuat.');
    }
}