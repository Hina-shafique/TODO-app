<div class="max-w-3xl mx-auto p-6">

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">
                        <a href="{{ route('teams.show', $team) }}" wire:navigate class="hover:underline text-indigo-600">
                            {{ $team->name }}
                        </a>
                        /
                        <a href="{{ route('projects.index', $team) }}" wire:navigate class="hover:underline text-indigo-600">
                            Projects
                        </a>
                    </p>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                </div>

                @if ($currentUserIsAdmin)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('projects.edit', [$team, $project]) }}" wire:navigate
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium transition">
                            Edit
                        </a>
                        <button wire:click="deleteProject"
                            wire:confirm="Are you sure you want to delete this project? This cannot be undone."
                            class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-md text-sm font-medium transition">
                            Delete
                        </button>
                    </div>
                @endif
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <span @class([
                    'px-3 py-1 rounded-full text-sm font-semibold',
                    'bg-green-100 text-green-700' => $project->status->color() === 'green',
                    'bg-blue-100 text-blue-700' => $project->status->color() === 'blue',
                    'bg-gray-100 text-gray-600' => $project->status->color() === 'gray',
                ])>
                    {{ $project->status->label() }}
                </span>

                @if ($project->due_date)
                    <span @class([
                        'text-sm',
                        'text-red-600 font-medium' => $project->isOverdue(),
                        'text-gray-500' => ! $project->isOverdue(),
                    ])>
                        Due {{ $project->due_date->format('M j, Y') }}
                        @if ($project->isOverdue()) — Overdue @endif
                    </span>
                @endif
            </div>
        </div>

        @if ($project->description)
            <div class="p-6 border-b border-gray-100">
                <p class="text-gray-700 whitespace-pre-line">{{ $project->description }}</p>
            </div>
        @endif

        @if ($currentUserIsAdmin)
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Update Status</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach (App\Enum\ProjectStatus::cases() as $status)
                        <button
                            wire:click="updateStatus('{{ $status->value }}')"
                            @class([
                                'px-3 py-1.5 rounded-md text-sm font-medium transition border',
                                'bg-green-600 text-white border-green-600' => $project->status === $status && $status->color() === 'green',
                                'bg-blue-600 text-white border-blue-600' => $project->status === $status && $status->color() === 'blue',
                                'bg-gray-600 text-white border-gray-600' => $project->status === $status && $status->color() === 'gray',
                                'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' => $project->status !== $status,
                            ])>
                            {{ $status->label() }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="p-6 text-xs text-gray-400">
            Created {{ $project->created_at->diffForHumans() }}
            @if ($project->updated_at != $project->created_at)
                · Updated {{ $project->updated_at->diffForHumans() }}
            @endif
        </div>
    </div>
</div>
