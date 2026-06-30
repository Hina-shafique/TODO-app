<div class="max-w-4xl mx-auto p-6">

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Projects</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    <a href="{{ route('teams.show', $team) }}" wire:navigate class="hover:underline text-indigo-600">
                        {{ $team->name }}
                    </a>
                </p>
            </div>
            @can('create', [App\Models\Project::class, $team])
                <a href="{{ route('projects.create', $team) }}" wire:navigate
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    + New Project
                </a>
            @endcan
        </div>

        @if ($projects->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <p class="text-lg">No projects yet.</p>
                @can('create', [App\Models\Project::class, $team])
                    <p class="text-sm mt-1">Create the first project for this team!</p>
                @endcan
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($projects as $project)
                    <li class="flex items-center justify-between p-5 hover:bg-gray-50 transition">
                        <div>
                            <a href="{{ route('projects.show', [$team, $project]) }}" wire:navigate
                                class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                {{ $project->name }}
                            </a>
                            @if ($project->description)
                                <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($project->description, 80) }}</p>
                            @endif
                            @if ($project->due_date)
                                <p @class([
                                    'text-xs mt-1',
                                    'text-red-600 font-medium' => $project->isOverdue(),
                                    'text-gray-400' => ! $project->isOverdue(),
                                ])>
                                    Due {{ $project->due_date->format('M j, Y') }}
                                    @if ($project->isOverdue()) — Overdue @endif
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-semibold',
                                'bg-green-100 text-green-700' => $project->status->color() === 'green',
                                'bg-blue-100 text-blue-700' => $project->status->color() === 'blue',
                                'bg-gray-100 text-gray-600' => $project->status->color() === 'gray',
                            ])>
                                {{ $project->status->label() }}
                            </span>
                            <a href="{{ route('projects.show', [$team, $project]) }}" wire:navigate
                                class="text-sm text-indigo-600 hover:underline">
                                View →
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
