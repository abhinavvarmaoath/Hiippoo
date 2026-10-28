<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class TeamTest extends TestCase
{
    private ?string $cookie = null;
    private string $token = '';

    protected function setUp(): void
    {
        Fixtures::reset();
        $this->cookie = null;
        $this->token = Http::login(AppServer::baseUrl(), 'fixture_alice', 'alicepw', $this->cookie)['token'];
    }

    public function testCreateTeamThenListedOnDisplayTeam(): void
    {
        $base = AppServer::baseUrl();
        $created = Http::post($base . '/created.php', [
            'team' => Fixtures::TEAM,
            'csrf_token' => $this->token,
        ], $this->cookie);
        $this->assertSame(200, $created['status']);
        $this->assertStringContainsString('Team Created!', $created['body']);

        $list = Http::get($base . '/displayteam.php', $this->cookie);
        $this->assertStringContainsString('Team:' . Fixtures::TEAM, $list['body']);
    }
}
