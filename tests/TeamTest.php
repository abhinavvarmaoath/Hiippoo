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

    private function createTeam(): array
    {
        return Http::post(AppServer::baseUrl() . '/created.php', [
            'team' => Fixtures::TEAM,
            'csrf_token' => $this->token,
        ], $this->cookie);
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

    public function testCreatingAnExistingTeamIsRejected(): void
    {
        $this->createTeam();
        $again = $this->createTeam();
        $this->assertStringContainsString('already exists', $again['body']);
        $this->assertStringNotContainsString('Team Created!', $again['body']);
    }

    public function testAnotherUserCanJoinAnExistingTeam(): void
    {
        $base = AppServer::baseUrl();
        $this->createTeam();

        $bobCookie = null;
        $bobToken = Http::login($base, 'fixture_bob', 'bobpw', $bobCookie)['token'];
        $joined = Http::post($base . '/joined.php', [
            'team' => Fixtures::TEAM,
            'csrf_token' => $bobToken,
        ], $bobCookie);
        $this->assertStringContainsString('Congratulations on Joining the Team!', $joined['body']);

        $list = Http::get($base . '/displayteam.php', $bobCookie);
        $this->assertStringContainsString('Team:' . Fixtures::TEAM, $list['body']);
    }

    public function testJoiningATeamYouAreAlreadyInIsRejected(): void
    {
        $this->createTeam();
        $joined = Http::post(AppServer::baseUrl() . '/joined.php', [
            'team' => Fixtures::TEAM,
            'csrf_token' => $this->token,
        ], $this->cookie);
        $this->assertStringContainsString('already a member', $joined['body']);
    }

    public function testJoiningANonexistentTeamIsRejected(): void
    {
        $base = AppServer::baseUrl();
        $joined = Http::post($base . '/joined.php', [
            'team' => 'fixture-no-such-team',
            'csrf_token' => $this->token,
        ], $this->cookie);
        $this->assertStringContainsString('No team named', $joined['body']);

        $list = Http::get($base . '/displayteam.php', $this->cookie);
        $this->assertStringNotContainsString('fixture-no-such-team', $list['body']);
    }

    public function testCreatingATeamWithoutCsrfTokenIsRejected(): void
    {
        $base = AppServer::baseUrl();
        $created = Http::post($base . '/created.php', ['team' => Fixtures::TEAM], $this->cookie);
        $this->assertStringContainsString('Invalid or missing CSRF token', $created['body']);

        $list = Http::get($base . '/displayteam.php', $this->cookie);
        $this->assertStringNotContainsString('Team:' . Fixtures::TEAM, $list['body']);
    }
}
