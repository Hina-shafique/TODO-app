<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tests\TestCase;

class EnsureInActiveUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_logs_out_inactive_user()
    {
        // 1. Create an inactive user
        $user = User::factory()->create(['is_active' => false]);

        // 2. Mock the Auth facade so the middleware sees a logged-in user
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        // 3. EXPECT the logout and session destruction
        Auth::shouldReceive('logout')->once();

        // 4. Set up the request with a session
        $request = Request::create('/dashboard', 'GET');
        $session = \Mockery::mock(\Illuminate\Session\Store::class);

        // Simulate an expired check (6 minutes ago)
        $session->shouldReceive('get')->with('last_activity_check')->andReturn(now()->subMinutes(6)->timestamp);

        // These are called when the user is inactive
        $session->shouldReceive('invalidate')->once();
        $session->shouldReceive('regenerateToken')->once();

        $request->setLaravelSession($session);

        $middleware = new EnsureUserIsActive();

        // 5. Execute
        $response = $middleware->handle($request, function () {
            return response('next');
        });

        // 6. Assertions
        $this->assertEquals(302, $response->getStatusCode());
        // Use a partial string check if you don't want to rely on the full URL generator
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }
}