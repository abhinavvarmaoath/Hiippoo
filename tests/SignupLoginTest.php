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

        $signup = Http::post($base . '/signup.php', [
            'username' => $username,
            'password' => 'newpw',
            'email' => 'new@example.test',
            'education' => 'BA',
            'university' => 'Test U',
            'submit' => '1',
        ]);
        $this->assertSame('http://www.hiippoo.com/login.html', $signup['location']);

        $cookie = null;
        $login = Http::post($base . '/login.php', [
            'username' => $username,
            'password' => 'newpw',
        ], $cookie);
        $this->assertSame('home1.php', $login['location']);

        $home = Http::get($base . '/home1.php', $cookie);
        $this->assertStringContainsString("Welcome to HIIPPOO,     {$username}", $home['body']);
        $this->assertStringContainsString('new@example.test', $home['body']);
    }

    public function testLoginWithWrongPasswordDoesNotReachHome(): void
    {
        $base = AppServer::baseUrl();
        $cookie = null;
        $login = Http::post($base . '/login.php', [
            'username' => 'fixture_alice',
            'password' => 'not-the-right-password',
        ], $cookie);
        $this->assertSame('signup.html', $login['location']);
    }

    public function testLoginWithCorrectPasswordReachesHome(): void
    {
        $base = AppServer::baseUrl();
        $cookie = null;
        $login = Http::post($base . '/login.php', [
            'username' => 'fixture_alice',
            'password' => 'alicepw',
        ], $cookie);
        $this->assertSame('home1.php', $login['location']);
    }
}
