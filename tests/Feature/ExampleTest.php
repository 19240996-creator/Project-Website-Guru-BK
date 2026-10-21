<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function test_logout_works_with_both_get_and_post(): void
    {
        $responseGet = $this->get('/logout');
        $responseGet->assertRedirect('/login');

        $responsePost = $this->post('/logout');
        $responsePost->assertRedirect('/login');
    }
}
