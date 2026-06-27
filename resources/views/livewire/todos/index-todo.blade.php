<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">My Todos</h2>
            <a href="{{ route('todos.create') }}" wire:navigate
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                + New Todo
            </a>
        </div>

        @if (session()->has('message'))
            <div class="mx-6 mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
                {{ session('message') }}
            </div>
        @endif

        @if ($todos->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <p class="text-lg">No todos yet.</p>
                <p class="text-sm mt-1">Create your first one to get started!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-3 px-6">Title</th>
                            <th class="py-3 px-6">Description</th>
                            <th class="py-3 px-6">Priority</th>
                            <th class="py-3 px-6">Due Date</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($todos as $todo)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 font-medium text-gray-900">
                                    <a href="{{ route('todos.show', $todo->id) }}" wire:navigate
                                        class="hover:text-indigo-600 hover:underline transition">
                                        {{ $todo->title }}
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-gray-500 max-w-xs truncate">{{ $todo->description ?? '—' }}</td>
                                <td class="py-4 px-6">
                                    <x-priority-badge :priority="$todo->priority" />
                                </td>
                                <td class="py-4 px-6 text-gray-500">
                                    {{ $todo->due_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="py-4 px-6">
                                    <x-status-badge
                                        :status="$todo->status"
                                        wire:click="toggleStatus({{ $todo->id }})"
                                        wire:loading.attr="disabled"
                                    />
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('todos.edit', $todo->id) }}" wire:navigate
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                                            Edit
                                        </a>
                                        <button wire:click="delete({{ $todo->id }})"
                                            wire:confirm="Are you sure you want to delete this todo?"
                                            wire:loading.attr="disabled"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                                            Delete
                                        </button>
                                        <button wire:click="toggleBookmark({{ $todo->id }})"
                                            wire:loading.attr="disabled"
                                            title="{{ in_array($todo->id, $bookmarkedIds) ? 'Remove bookmark' : 'Add bookmark' }}"
                                            class="p-1 rounded hover:bg-gray-100 transition">
                                            @if(in_array($todo->id, $bookmarkedIds))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M5 3a2 2 0 00-2 2v12l7-4 7 4V5a2 2 0 00-2-2H5z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-4 7 4V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $todos->links() }}
            </div>
        @endif
    </div>
</div>
