<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class SignupLoginTest extends TestCase
{
    protected function setUp(): void
    {
        Fixtures::reset();
    }

    public function testSignupThenLoginReachesHome(): void
    {
        $base = AppServer::baseUrl();
        $username = 'fixture_new_' . bin2hex(random_bytes(4));

        $cookie = null;
        $token = Http::csrfToken($base . '/signup_form.php', $cookie);
        $signup = Http::post($base . '/signup.php', [
            'username' => $username,
            'password' => 'newpw',
            'email' => 'new@example.test',
            'education' => 'BA',
            'university' => 'Test U',
            'submit' => '1',
            'csrf_token' => $token,
        ], $cookie);
        $this->assertSame('http://www.hiippoo.com/login_form.php', $signup['location']);

        $login = Http::login($base, $username, 'newpw', $cookie);
        $this->assertSame('home1.php', $login['location']);

        $home = Http::get($base . '/home1.php', $cookie);
        $this->assertStringContainsString("Welcome to HIIPPOO,     {$username}", $home['body']);
        $this->assertStringContainsString('new@example.test', $home['body']);
    }

    public function testLoginWithWrongPasswordDoesNotReachHome(): void
    {
        $base = AppServer::baseUrl();
        $cookie = null;
        $login = Http::login($base, 'fixture_alice', 'not-the-right-password', $cookie);
        $this->assertSame('signup_form.php', $login['location']);
    }

    public function testLoginWithCorrectPasswordReachesHome(): void
    {
        $base = AppServer::baseUrl();
        $cookie = null;
        $login = Http::login($base, 'fixture_alice', 'alicepw', $cookie);
        $this->assertSame('home1.php', $login['location']);
    }
}
