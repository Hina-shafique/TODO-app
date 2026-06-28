<?php

namespace App\Livewire\Teams;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('My Teams')]
class IndexTeam extends Component
{
    public function render()
    {
        return view('livewire.teams.index-team', [
            'teams' => Auth::user()->teams()->withPivot('role', 'joined_at')->latest('team_user.joined_at')->get(),
        ]);
    }
}
