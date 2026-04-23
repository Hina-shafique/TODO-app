<div>
    <form wire:submit.prevent="createTodo">
        <div>
            <label>Title</label>
            <input type="text" wire:model="title" placeholder="Enter todo title">
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div>
            <label>Description</label>
            <input type="text" wire:model="description" placeholder="Enter description">
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
        <button type="submit">Create Todo</button>
    </form>
</div>