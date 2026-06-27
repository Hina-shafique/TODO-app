<div class="max-w-3xl mx-auto p-6">

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">

        {{-- Header --}}
        <div class="flex items-start justify-between p-6 border-b border-gray-200">
            <div class="flex-1 min-w-0 pr-4">
                <h1 class="text-2xl font-bold text-gray-900 break-words">{{ $todo->title }}</h1>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <x-status-badge :status="$todo->status" class="cursor-default pointer-events-none" />
                    <x-priority-badge :priority="$todo->priority" />
                    @if ($todo->due_date && $todo->due_date->isPast() && $todo->status !== \App\Enum\TodoStatus::COMPLETED)
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                            Overdue
                        </span>
                    @endif
                </div>
            </div>

            {{-- Bookmark button --}}
            <button wire:click="toggleBookmark"
                wire:loading.attr="disabled"
                title="{{ $isBookmarked ? 'Remove bookmark' : 'Add bookmark' }}"
                class="flex-shrink-0 p-2 rounded-full hover:bg-gray-100 transition">
                @if ($isBookmarked)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 3a2 2 0 00-2 2v12l7-4 7 4V5a2 2 0 00-2-2H5z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-4 7 4V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" />
                    </svg>
                @endif
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6">

            {{-- Description --}}
            <div>
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Description</h2>
                <p class="text-gray-700 whitespace-pre-wrap">
                    {{ $todo->description ?? '—' }}
                </p>
            </div>

            {{-- Meta grid --}}
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
                <div>
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Due Date</h2>
                    <p class="text-gray-800 text-sm">
                        {{ $todo->due_date?->format('M d, Y') ?? '—' }}
                    </p>
                </div>

                <div>
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Created</h2>
                    <p class="text-gray-800 text-sm">{{ $todo->created_at->format('M d, Y') }}</p>
                </div>

                @if ($todo->status === \App\Enum\TodoStatus::COMPLETED && $todo->completed_at)
                    <div>
                        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Completed</h2>
                        <p class="text-gray-800 text-sm">{{ $todo->completed_at->format('M d, Y H:i') }}</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- Footer actions --}}
        <div class="flex items-center justify-between p-6 border-t border-gray-200 bg-gray-50">
            <a href="{{ route('todos.index') }}" wire:navigate
                class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Back to todos
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('todos.edit', $todo->id) }}" wire:navigate
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    Edit
                </a>
                <button wire:click="delete"
                    wire:confirm="Are you sure you want to delete this todo?"
                    wire:loading.attr="disabled"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    Delete
                </button>
            </div>
        </div>

    </div>
</div>
