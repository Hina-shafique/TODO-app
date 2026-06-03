<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between">
        <h2 class="text-2xl font-bold mb-4">{{ $user->name }}'s Bookmarks</h2>
    </div>

    <table class="w-full text-center border-collapse">
        <thead>
            <tr class="border-b text-gray-600 uppercase text-xs">
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Description</th>
                <th class="py-3 px-4">Priority</th>
                <th class="py-3 px-4">Due Date</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookmarked as $todo)
                <tr>
                    <td class="py-4 px-4">{{ $todo->title }}</td>
                    <td class="py-4 px-4">{{ $todo->description }}</td>
                    <td class="py-4 px-4">{{ $todo->priority->value }}</td>
                    <td class="py-4 px-4">{{ $todo->due_date }}</td>
                    <td class="py-4 px-4">{{ ucfirst(str_replace('_', ' ', $todo->status->value)) }}</td>
                    <td class="py-6 px-4 flex space-x-2 justify-center">
                        <a href="{{ route('todos.edit', $todo->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $bookmarked->links() }}
    </div>
</div>
