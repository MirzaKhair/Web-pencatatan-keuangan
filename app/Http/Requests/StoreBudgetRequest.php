<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if (! $category) {
                        return;
                    }
                    if ($category->user_id !== $this->user()->id) {
                        $fail('Kategori tidak valid.');
                    }
                    if ($category->type !== 'expense') {
                        $fail('Anggaran hanya bisa dibuat untuk kategori pengeluaran.');
                    }
                },
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2030'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'amount.required' => 'Nominal anggaran wajib diisi.',
            'amount.numeric' => 'Nominal anggaran harus berupa angka.',
            'amount.gt' => 'Nominal anggaran harus lebih dari 0.',
            'month.required' => 'Bulan wajib dipilih.',
            'month.min' => 'Bulan tidak valid.',
            'month.max' => 'Bulan tidak valid.',
            'year.required' => 'Tahun wajib diisi.',
            'year.min' => 'Tahun tidak valid.',
            'year.max' => 'Tahun tidak valid.',
        ];
    }
}
