@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500']) }}>
