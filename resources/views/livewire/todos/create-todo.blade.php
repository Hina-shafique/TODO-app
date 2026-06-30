<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Create New Todo</h2>
            <a href="{{ route('todos.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Back to list
            </a>
        </div>

        <form wire:submit.prevent="createTodo" class="space-y-5">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="title"
                    wire:model="form.title"
                    autocomplete="off"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Enter todo title">
                @error('form.title')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description"
                    wire:model="form.description"
                    rows="3"
                    autocomplete="off"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Enter description (optional)"></textarea>
                @error('form.description')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                    Priority <span class="text-red-500">*</span>
                </label>
                <select id="priority" wire:model="form.priority"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach ($priorities as $case)
                        <option value="{{ $case->value }}">{{ ucfirst($case->value) }}</option>
                    @endforeach
                </select>
                @error('form.priority')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date"
                    id="due_date"
                    wire:model="form.due_date"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('form.due_date')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" wire:loading.attr="disabled"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium text-sm disabled:opacity-50 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span wire:loading.remove wire:target="createTodo">Create Todo</span>
                    <span wire:loading wire:target="createTodo">Creating...</span>
                </button>
                <a href="{{ route('todos.index') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
