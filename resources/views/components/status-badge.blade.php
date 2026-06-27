@props(['status'])

<button {{ $attributes->merge(['class' => 'px-3 py-1 rounded-full text-xs font-semibold transition-colors duration-200']) }}
    @class([
        'bg-green-100 text-green-800 hover:bg-green-200' => $status === \App\Enum\TodoStatus::COMPLETED,
        'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' => $status === \App\Enum\TodoStatus::IN_PROGRESS,
        'bg-red-100 text-red-800 hover:bg-red-200' => $status === \App\Enum\TodoStatus::PENDING,
    ])>
    {{ ucfirst(str_replace('_', ' ', $status->value)) }}
</button>
