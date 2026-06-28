<div class="max-w-4xl mx-auto p-6 space-y-6">

    @if (session()->has('message'))
        <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Team header --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h1>
                @if ($team->description)
                    <p class="text-gray-500 mt-1">{{ $team->description }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">
                    Owner: <span class="font-medium text-gray-600">{{ $team->owner->name }}</span>
                    &middot; {{ $members->count() }} {{ Str::plural('member', $members->count()) }}
                </p>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                @if ($currentUserIsAdmin)
                    <a href="{{ route('teams.edit', $team) }}" wire:navigate
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        Edit
                    </a>
                @endif

                @if ($currentUserIsOwner)
                    <button wire:click="deleteTeam"
                        wire:confirm="Delete this team? This cannot be undone."
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        Delete
                    </button>
                @else
                    <button wire:click="leaveTeam"
                        wire:confirm="Leave this team?"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition">
                        Leave
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Members --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="flex justify-between items-center p-5 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Members</h2>
            @if ($currentUserIsAdmin)
                <button wire:click="$toggle('showInviteForm')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition">
                    + Invite Member
                </button>
            @endif
        </div>

        {{-- Invite form --}}
        @if ($showInviteForm)
            <div class="p-5 bg-indigo-50 border-b border-indigo-100">
                <form wire:submit="inviteMember" class="flex items-start gap-3">
                    <div class="flex-1">
                        <input type="email"
                            wire:model="inviteEmail"
                            placeholder="Enter email address"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        @error('inviteEmail')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition whitespace-nowrap">
                        Send Invite
                    </button>
                    <button type="button" wire:click="$set('showInviteForm', false)"
                        class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">
                        Cancel
                    </button>
                </form>
            </div>
        @endif

        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-gray-500 uppercase text-xs tracking-wider">
                    <th class="py-3 px-5">Name</th>
                    <th class="py-3 px-5">Email</th>
                    <th class="py-3 px-5">Role</th>
                    <th class="py-3 px-5">Joined</th>
                    @if ($currentUserIsAdmin)
                        <th class="py-3 px-5 text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($members as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-5 font-medium text-gray-900">
                            {{ $member->name }}
                            @if ($team->isOwner($member))
                                <span class="ml-1 text-xs text-gray-400">(owner)</span>
                            @endif
                        </td>
                        <td class="py-3 px-5 text-gray-500">{{ $member->email }}</td>
                        <td class="py-3 px-5">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-semibold',
                                'bg-indigo-100 text-indigo-700' => $member->pivot->role === 'admin',
                                'bg-gray-100 text-gray-600' => $member->pivot->role === 'member',
                            ])>
                                {{ ucfirst($member->pivot->role) }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-gray-500">
                            {{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y') }}
                        </td>
                        @if ($currentUserIsAdmin)
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-center gap-2">
                                    @if (! $team->isOwner($member) && $currentUserIsOwner)
                                        @if ($member->pivot->role === 'member')
                                            <button wire:click="changeRole({{ $member->id }}, 'admin')"
                                                class="text-xs text-indigo-600 hover:underline">
                                                Make Admin
                                            </button>
                                        @else
                                            <button wire:click="changeRole({{ $member->id }}, 'member')"
                                                class="text-xs text-gray-500 hover:underline">
                                                Make Member
                                            </button>
                                        @endif
                                    @endif
                                    @if (! $team->isOwner($member))
                                        <button wire:click="removeMember({{ $member->id }})"
                                            wire:confirm="Remove {{ $member->name }} from the team?"
                                            class="text-xs text-red-500 hover:underline">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
