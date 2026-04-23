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
                    <td class="py-4 px-4">{{ $todo->status->value }}</td>
                    <td class="py-4 px-4">
                        <button wire:click="edit({{ $todo->id }})"
                            class="bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $todo->id }})"
                            class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>