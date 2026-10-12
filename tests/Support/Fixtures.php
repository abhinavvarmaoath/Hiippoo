<?php
namespace Tests\Support;

require_once __DIR__ . '/../../db.php';

final class Fixtures
{
    public const TEAM = 'fixture-team';

    public static function reset(): \mysqli
    {
        $conn = db_connect();
        $conn->query("DELETE FROM enc WHERE username IN ('fixture_alice','fixture_bob')");
        $conn->query("DELETE FROM team WHERE team = '" . self::TEAM . "'");
        $conn->query("DELETE FROM messages WHERE team = '" . self::TEAM . "'");
        $conn->query("DELETE FROM file WHERE team = '" . self::TEAM . "'");
        $conn->query(
            "INSERT INTO enc (username,password,email,education,university) VALUES " .
            "('fixture_alice', MD5('alicepw'), 'alice@example.test', 'BSc', 'SJSU')"
        );
        return $conn;
    }
}
