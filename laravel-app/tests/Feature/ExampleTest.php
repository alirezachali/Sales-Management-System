<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * صفحه اصلی مهمان را به لاگین و کاربر را به داشبوردش هدایت می‌کند.
     */
    public function test_the_application_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
