<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class LogoutTest extends TestCase
{
    public function testLogoutEndsSession(): void
    {
        Fixtures::reset();
        $base = AppServer::baseUrl();
        $cookie = null;
        Http::post($base . '/login.php', [
            'username' => 'fixture_alice',
            'password' => 'alicepw',
        ], $cookie);

        $before = Http::get($base . '/home1.php', $cookie);
        $this->assertStringContainsString('alice@example.test', $before['body']);

        Http::get($base . '/logout.php', $cookie);

        $after = Http::get($base . '/home1.php', $cookie);
        $this->assertStringNotContainsString('alice@example.test', $after['body']);
    }
}
