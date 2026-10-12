-- Hiippoo database schema.
--
-- No schema file existed anywhere in the repo; this is reverse-engineered
-- from the queries in the PHP code and verified by running the app against
-- it. Column sizes match what the code currently sends (e.g. `password` is
-- CHAR(32) because the app currently stores raw MD5 hex digests); widen
-- that if/when the password hashing migration lands.
--
-- utf8mb4 is used throughout since this is a new schema file with no
-- existing data to preserve, and it's the current default recommendation
-- for new MySQL/MariaDB schemas.

CREATE DATABASE IF NOT EXISTS hiippoo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hiippoo;

CREATE TABLE enc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    password CHAR(32) NOT NULL,
    email VARCHAR(128) NOT NULL,
    education VARCHAR(128) NOT NULL,
    university VARCHAR(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    time VARCHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    team VARCHAR(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    team VARCHAR(128) NOT NULL,
    username VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Uploaded files are stored as BLOBs directly in the database. That's a
-- known scalability ceiling (every download and backup carries the file
-- bytes through MySQL) worth revisiting if usage ever grows; not changed
-- here since nobody has asked for it and it's a storage architecture
-- decision, not a bug.
CREATE TABLE file (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    team VARCHAR(128) NOT NULL,
    name VARCHAR(255) NOT NULL,
    mime VARCHAR(128) NOT NULL,
    size INT NOT NULL,
    data LONGBLOB NOT NULL,
    created DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
