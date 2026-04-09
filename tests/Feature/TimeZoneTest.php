<?php 

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class TimeZoneTest extends TestCase
{
    use RefreshDatabase;
    public function test_timezone_set_globally():void
    {
        $user = User::factory()->create([
            'timezone' => 'Asia/Karachi',
        ]);

        $this->actingAs($user)->get('/');

        $this->assertSame('Asia/Karachi', date_default_timezone_get());

    }

}