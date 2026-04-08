<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentSecurityPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

   public function test_csp_header_is_present(): void
{
    $response = $this->get('/login');
    
    $response->assertHeader('Content-Security-Policy-Report-Only');
}
}
