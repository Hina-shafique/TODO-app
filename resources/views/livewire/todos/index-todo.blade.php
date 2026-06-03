<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between">
        <h2 class="text-2xl font-bold mb-4 ">Todo List</h2>
        <a href="{{ route('todos.create') }}" class="bg-green-500 text-black px-4 py-2 rounded">Create New Todo</a>
    </div>
    <table class="w-full text-center border-collapse">
        <thead>
            <tr class="border-b dark:border-gray-700 text-gray-600 dark:text-gray-400 uppercase text-xs">
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Description</th>
                <th class="py-3 px-4">Priority</th>
                <th class="py-3 px-4">Due Date</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todos as $todo)
                    <tr>
                        <td class="py-4 px-4">{{ $todo->title }}</td>
                        <td class="py-4 px-4">{{ $todo->description }}</td>
                        <td class="py-4 px-4">{{ $todo->priority->value }}</td>
                        <td class="py-4 px-4">{{ $todo->due_date }}</td>
                        <td class="py-4 px-4">
                            <button wire:click="toggleStatus({{ $todo->id }})"
                                class="px-3 py-1 rounded-full text-xs font-semibold transition-colors duration-200 
                                    {{ $todo->status === \App\Enum\TodoStatus::COMPLETED ? 'bg-green-100 text-green-800 hover:bg-green-200' :
                                    ($todo->status === \App\Enum\TodoStatus::IN_PROGRESS ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200') }}">
                                    {{ ucfirst(str_replace('_', ' ', $todo->status->value)) }}
                            </button>
                        </td>
                        <td class="py-6 px-4 flex space-x-2 justify-center">
                            <button wire:click="edit({{ $todo->id }})"
                                class="bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                            <button wire:click="delete({{ $todo->id }})"
                                class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                            <button wire:click="toggleBookmark({{ $todo->id }})" wire:loading.attr="disabled"
                                class="px-2 py-1 rounded flex items-center justify-center"
                                aria-label="{{ in_array($todo->id, $bookmarkedIds) ? 'Remove bookmark' : 'Add bookmark' }}" title="{{ in_array($todo->id, $bookmarkedIds) ? 'Remove bookmark' : 'Add bookmark' }}">
                                @if(in_array($todo->id, $bookmarkedIds))
                                    <!-- filled bookmark -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M5 3a2 2 0 00-2 2v12l7-4 7 4V5a2 2 0 00-2-2H5z" />
                                    </svg>
                                @else
                                    <!-- outline bookmark -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-4 7 4V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" />
                                    </svg>
                                @endif
                            </button>
                        </td>
                    </tr>
            @endforeach
        </tbody>
    </table>
</div>