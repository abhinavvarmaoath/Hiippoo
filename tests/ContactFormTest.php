<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class ContactFormTest extends TestCase
{
    // email.php is the contact form linked from the sidebar; emailsent.php
    // is its handler and rejects posts that lack a CSRF token. The form has
    // to carry one or every real submission is refused.
    public function testFormCarriesCsrfTokenAndHandlerRejectsPostsWithoutOne(): void
    {
        Fixtures::reset();
        $base = AppServer::baseUrl();
        $cookie = null;
        Http::login($base, 'fixture_alice', 'alicepw', $cookie);

        $form = Http::get($base . '/email.php', $cookie);
        $this->assertStringContainsString('action="emailsent.php"', $form['body']);
        $this->assertStringContainsString('name="csrf_token"', $form['body']);

        $rejected = Http::post($base . '/emailsent.php', [
            'subject' => 'subject',
            'message' => 'message',
            'email' => 'support@example.com',
        ], $cookie);
        $this->assertStringContainsString('Invalid or missing CSRF token', $rejected['body']);
    }
}
