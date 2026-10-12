<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class ChatTest extends TestCase
{
    private ?string $cookie = null;

    protected function setUp(): void
    {
        Fixtures::reset();
        $base = AppServer::baseUrl();
        $this->cookie = null;
        Http::post($base . '/login.php', [
            'username' => 'fixture_alice',
            'password' => 'alicepw',
        ], $this->cookie);
        Http::post($base . '/created.php', ['team' => Fixtures::TEAM], $this->cookie);
        Http::get($base . '/chatroom.php?teamid=' . Fixtures::TEAM, $this->cookie);
    }

    public function testPostedMessageAppearsInTeamChat(): void
    {
        $base = AppServer::baseUrl();
        $text = 'hello from test ' . bin2hex(random_bytes(3));
        Http::post($base . '/newmessage.php', ['message' => $text], $this->cookie);

        $messages = Http::get($base . '/messages.php', $this->cookie);
        $this->assertStringContainsString($text, $messages['body']);
        $this->assertStringContainsString('fixture_alice', $messages['body']);
    }
}
