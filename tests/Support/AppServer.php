<?php
namespace Tests\Support;

final class AppServer
{
    private static $process;
    private static $port;

    public static function start(): string
    {
        if (self::$process !== null) {
            return self::baseUrl();
        }
        self::$port = 8100 + random_int(0, 400);
        $cmd = sprintf(
            'php -S 127.0.0.1:%d -t %s',
            self::$port,
            escapeshellarg(dirname(__DIR__, 2))
        );
        self::$process = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        for ($i = 0; $i < 50; $i++) {
            $conn = @fsockopen('127.0.0.1', self::$port);
            if ($conn) {
                fclose($conn);
                return self::baseUrl();
            }
            usleep(100000);
        }
        throw new \RuntimeException('app server did not start on port ' . self::$port);
    }

    public static function baseUrl(): string
    {
        return 'http://127.0.0.1:' . self::$port;
    }

    public static function stop(): void
    {
        if (self::$process !== null) {
            proc_terminate(self::$process);
            proc_close(self::$process);
            self::$process = null;
        }
    }
}
