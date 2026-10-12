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
        $username = 'fixture_alice';
        $hash = password_hash('alicepw', PASSWORD_ARGON2ID);
        $email = 'alice@example.test';
        $education = 'BSc';
        $university = 'SJSU';
        $stmt = $conn->prepare(
            "INSERT INTO enc (username,password,email,education,university) VALUES (?,?,?,?,?)"
        );
        $stmt->bind_param('sssss', $username, $hash, $email, $education, $university);
        $stmt->execute();
        return $conn;
    }
}
