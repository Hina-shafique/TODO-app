@props(['priority'])

<span {{ $attributes->merge(['class' => 'px-2 py-1 rounded-full text-xs font-semibold']) }}
    @class([
        'bg-red-100 text-red-700' => $priority->value === 'high',
        'bg-yellow-100 text-yellow-700' => $priority->value === 'medium',
        'bg-gray-100 text-gray-600' => $priority->value === 'low',
    ])>
    {{ ucfirst($priority->value) }}
</span>
