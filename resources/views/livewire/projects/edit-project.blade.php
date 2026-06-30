<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Project</h2>
            <a href="{{ route('projects.show', [$this->project->team, $this->project]) }}" wire:navigate
                class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to project
            </a>
        </div>

        <form wire:submit="updateProject" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Project Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="name"
                    wire:model="name"
                    autocomplete="off"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('name')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description"
                    wire:model="description"
                    rows="4"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                @error('description')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status"
                    wire:model="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
                @error('status')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date"
                    id="dueDate"
                    wire:model="dueDate"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('dueDate')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" wire:loading.attr="disabled"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium text-sm disabled:opacity-50 transition">
                    <span wire:loading.remove wire:target="updateProject">Save Changes</span>
                    <span wire:loading wire:target="updateProject">Saving...</span>
                </button>
                <a href="{{ route('projects.show', [$this->project->team, $this->project]) }}" wire:navigate
                    class="text-sm text-gray-500 hover:text-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
