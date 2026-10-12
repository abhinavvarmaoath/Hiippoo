<?php
namespace Tests\Support;

final class Http
{
    public static function post(string $url, array $fields, ?string &$cookieFile = null): array
    {
        return self::request($url, 'POST', $fields, $cookieFile);
    }

    public static function get(string $url, ?string &$cookieFile = null): array
    {
        return self::request($url, 'GET', null, $cookieFile);
    }

    private static function request(string $url, string $method, ?array $fields, ?string &$cookieFile): array
    {
        if ($cookieFile === null) {
            $cookieFile = tempnam(sys_get_temp_dir(), 'cj');
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        $raw = curl_exec($ch);
        if ($raw === false) {
            throw new \RuntimeException('curl error: ' . curl_error($ch));
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $headers = substr($raw, 0, $headerSize);
        $location = null;
        if (preg_match('/^Location:\s*(.+?)\s*$/mi', $headers, $m)) {
            $location = $m[1];
        }
        return [
            'status' => $status,
            'location' => $location,
            'body' => substr($raw, $headerSize),
        ];
    }
}
