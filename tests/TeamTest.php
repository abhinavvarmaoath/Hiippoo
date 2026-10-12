<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class TeamTest extends TestCase
{
    private ?string $cookie = null;

    protected function setUp(): void
    {
        Fixtures::reset();
        $this->cookie = null;
        Http::post(AppServer::baseUrl() . '/login.php', [
            'username' => 'fixture_alice',
            'password' => 'alicepw',
        ], $this->cookie);
    }

    public function testCreateTeamThenListedOnDisplayTeam(): void
    {
        $base = AppServer::baseUrl();
        $created = Http::post($base . '/created.php', ['team' => Fixtures::TEAM], $this->cookie);
        $this->assertSame(200, $created['status']);
        $this->assertStringContainsString('Team Created!', $created['body']);

        $list = Http::get($base . '/displayteam.php', $this->cookie);
        $this->assertStringContainsString('Team:' . Fixtures::TEAM, $list['body']);
    }
}
