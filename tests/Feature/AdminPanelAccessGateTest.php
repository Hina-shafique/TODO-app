<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Tests\TestCase;

class AdminPanelAccessGateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_access_filament_gate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $this->assertTrue(Gate::forUser($admin)->allows('accessAdmin'));
        $this->assertFalse(Gate::forUser($member)->allows('accessAdmin'));
    }
}
