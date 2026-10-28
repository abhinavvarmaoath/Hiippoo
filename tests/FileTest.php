<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Support\AppServer;
use Tests\Support\Fixtures;
use Tests\Support\Http;

final class FileTest extends TestCase
{
    private ?string $cookie = null;
    private string $token = '';

    protected function setUp(): void
    {
        Fixtures::reset();
        $base = AppServer::baseUrl();
        $this->cookie = null;
        $this->token = Http::login($base, 'fixture_alice', 'alicepw', $this->cookie)['token'];
        Http::post($base . '/created.php', [
            'team' => Fixtures::TEAM,
            'csrf_token' => $this->token,
        ], $this->cookie);
        Http::get($base . '/list_files.php?teamid=' . Fixtures::TEAM, $this->cookie);
    }

    // Extra fields go ahead of the file, which is the order PHP needs for
    // MAX_FILE_SIZE to apply to it.
    private function upload(string $name, string $contents, array $extraFields = []): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'up');
        file_put_contents($tmp, $contents);

        $ch = curl_init(AppServer::baseUrl() . '/add_file.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['csrf_token' => $this->token] + $extraFields + [
            'uploaded_file' => new \CURLFile($tmp, 'text/plain', $name),
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        unlink($tmp);
        return $response;
    }

    public function testUploadedFileListedAndDownloadable(): void
    {
        $base = AppServer::baseUrl();
        $upload = $this->upload('hello.txt', 'hello file contents');
        $this->assertStringContainsString('Success', $upload);

        $list = Http::get($base . '/list_files.php?teamid=' . Fixtures::TEAM, $this->cookie);
        $this->assertStringContainsString('hello.txt', $list['body']);

        preg_match('/get_file\.php\?id=(\d+)/', $list['body'], $m);
        $this->assertNotEmpty($m, 'download link not found in file list');
        $download = Http::get($base . '/get_file.php?id=' . $m[1], $this->cookie);
        $this->assertSame('hello file contents', $download['body']);
    }

    public function testFailedUploadReportsTheErrorAndStillFinishesThePage(): void
    {
        $response = $this->upload('too-big.txt', 'longer than the ten byte limit', ['MAX_FILE_SIZE' => '10']);

        $this->assertStringContainsString('Error code: 2', $response);
        // This closing link is only reached if the script didn't fatal first.
        $this->assertStringContainsString('Click <a href="displayteam.php">here</a> Team', $response);
    }
}
