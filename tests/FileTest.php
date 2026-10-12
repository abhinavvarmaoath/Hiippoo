<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class FileTest extends TestCase
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
        Http::get($base . '/list_files.php?teamid=' . Fixtures::TEAM, $this->cookie);
    }

    public function testUploadedFileListedAndDownloadable(): void
    {
        $base = AppServer::baseUrl();
        $tmp = tempnam(sys_get_temp_dir(), 'up');
        file_put_contents($tmp, 'hello file contents');

        $ch = curl_init($base . '/add_file.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'uploaded_file' => new \CURLFile($tmp, 'text/plain', 'hello.txt'),
        ]);
        $upload = curl_exec($ch);
        curl_close($ch);
        unlink($tmp);
        $this->assertStringContainsString('Success', $upload);

        $list = Http::get($base . '/list_files.php?teamid=' . Fixtures::TEAM, $this->cookie);
        $this->assertStringContainsString('hello.txt', $list['body']);

        preg_match('/get_file\.php\?id=(\d+)/', $list['body'], $m);
        $this->assertNotEmpty($m, 'download link not found in file list');
        $download = Http::get($base . '/get_file.php?id=' . $m[1], $this->cookie);
        $this->assertSame('hello file contents', $download['body']);
    }
}
