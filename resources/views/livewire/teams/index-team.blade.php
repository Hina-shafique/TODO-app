<div class="max-w-4xl mx-auto p-6">

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">My Teams</h2>
            <a href="{{ route('teams.create') }}" wire:navigate
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                + New Team
            </a>
        </div>

        @if ($teams->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <p class="text-lg">You are not part of any team yet.</p>
                <p class="text-sm mt-1">Create one or ask someone to invite you!</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($teams as $team)
                    <li class="flex items-center justify-between p-5 hover:bg-gray-50 transition">
                        <div>
                            <a href="{{ route('teams.show', $team) }}" wire:navigate
                                class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                {{ $team->name }}
                            </a>
                            @if ($team->description)
                                <p class="text-sm text-gray-500 mt-0.5">{{ $team->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-semibold',
                                'bg-indigo-100 text-indigo-700' => $team->pivot->role === 'admin',
                                'bg-gray-100 text-gray-600' => $team->pivot->role === 'member',
                            ])>
                                {{ ucfirst($team->pivot->role) }}
                            </span>
                            <a href="{{ route('teams.show', $team) }}" wire:navigate
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
