<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutMiddleware;

    #[Test]
    public function it_displays_login_page_for_guests()
    {
        $response = $this->get(route('view.login'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/Login')
            );
    }

    #[Test]
    public function it_authenticates_user_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
            ->post(route('view.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
            '_token' => 'test-token',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_validates_required_fields_on_login()
    {
        $response = $this->post(route('view.login'), []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    #[Test]
    public function it_validates_email_format_on_login()
    {
        $response = $this->post(route('view.login'), [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function it_displays_registration_page_for_guests()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/Register')
            );
    }

    #[Test]
    public function it_validates_required_fields_on_registration()
    {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'password'
        ]);
        $this->assertGuest();
    }

    #[Test]
    public function it_validates_email_format_on_registration()
    {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function it_validates_unique_email_on_registration()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function it_logs_out_authenticated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post(route('logout'), ['_token' => 'test-token']);

        $response->assertRedirect(route('view.login'));
        $this->assertGuest();
    }

    #[Test]
    public function it_redirects_guests_trying_to_logout()
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('view.login'));
    }
}
