<div>
    <form wire:submit.prevent="editTodo">
        <div>
            <label>Title</label>
            <input type="text" wire:model="title" placeholder="Enter todo title">
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Description</label>
            <textarea wire:model="description" placeholder="Enter description"></textarea>
            @error('description') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Priority</label>
            <select wire:model="priority">
                @foreach ($priorities as $case)
                    <option value="{{ $case->value }}">{{ ucfirst($case->value) }}</option>
                @endforeach
            </select>
            @error('priority') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Due Date</label>
            <input type="date" wire:model="due_date">
            @error('due_date') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Update Todo</button>
    </form>
</div>