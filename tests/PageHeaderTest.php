<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class PageHeaderTest extends TestCase
{
    // The shared header shows who is logged in. create.php, join.php and
    // email.php never set a $username of their own, so the header has to
    // get the name from the session rather than from the including page.
    public function testHeaderShowsTheLoggedInUsernameOnEveryPage(): void
    {
        Fixtures::reset();
        $base = AppServer::baseUrl();
        $cookie = null;
        Http::login($base, 'fixture_alice', 'alicepw', $cookie);

        $pages = ['home1.php', 'displayteam.php', 'displaychat.php', 'create.php', 'join.php', 'email.php', 'games.php'];
        foreach ($pages as $page) {
            $response = Http::get($base . '/' . $page, $cookie);
            $this->assertStringContainsString(
                '<span class="username">fixture_alice</span>',
                $response['body'],
                $page . ' should show the logged-in username in its header'
            );
        }
    }
}
