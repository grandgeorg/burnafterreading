<?php

declare(strict_types=1);

namespace Bar;

use SQLite3;

class Limit
{
  protected SQLite3 $db;
  protected int $limit;
  protected int $time;

  public function __construct(string $dbfile, int $limit = 3, int $time = 600)
  {
    $this->db = new SQLite3($dbfile);
    $this->limit = $limit;
    $this->time = $time;
  }

  public function add(): void
  {
    $now = time();
    $this->removeOld($now);
    $ip = $this->getIp();
    $stmt = $this->db->prepare('SELECT * FROM `hits` WHERE `ip` = :ip');
    $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row) {
      $stmt = $this->db->prepare('UPDATE `hits` SET `time` = :time, counter = :counter WHERE `ip` = :ip');
      $stmt->bindValue(':time', $now, SQLITE3_INTEGER);
      $stmt->bindValue(':counter', $row['counter'] + 1, SQLITE3_INTEGER);
      $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
      $stmt->execute();
    } else {
      $stmt = $this->db->prepare('INSERT INTO `hits` (`ip`, `time`, `counter`) VALUES (:ip, :time, 1)');
      $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
      $stmt->bindValue(':time', $now, SQLITE3_INTEGER);
      $stmt->execute();
    }
  }

  public function removeOld(int $now): void
  {
    $stmt = $this->db->prepare('DELETE FROM `hits` WHERE `time` < :time');
    $stmt->bindValue(':time', $now - $this->time, SQLITE3_INTEGER);
    $stmt->execute();
  }

  public function check(): bool
  {
    $now = time();
    $this->removeOld($now);
    $ip = $this->getIp();
    $stmt = $this->db->prepare('SELECT * FROM `hits` WHERE `ip` = :ip');
    $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row) {
      return $row['counter'] > $this->limit;
    }
    return false;
  }

  public function getIp(): string
  {
    // we use REMOTE_ADDR here only, because if the app is behind a reverse proxy, the correct client IP address should be set in REMOTE_ADDR by the reverse proxy (e.g. using mod_remoteip in Apache), and we don't want to rely on HTTP headers like X-Forwarded-For which can be easily spoofed by the client and would allow an attacker to bypass the brute-force protection by simply setting a fake X-Forwarded-For header with a different IP address for each request.
    return $_SERVER['REMOTE_ADDR'];
  }


}
