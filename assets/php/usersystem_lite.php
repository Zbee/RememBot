<?php

#UserSystem 0.1.0 Lite
class USLite {

  var $FLYWHEEL = "";
  var $USERS = "";
  var $BLOBS = "";

  public function __construct ($flywheel) {
    if (!($flywheel instanceof \JamesMoss\Flywheel\Config))
      throw new Exception (
        "UserSystem Lite requires a Flywheel config object be passed to it."
      );
    $this->FLYWHEEL = $flywheel;
    $this->USERS = new \JamesMoss\Flywheel\Repository("users", $this->FLYWHEEL);
    if (!($this->USERS instanceof \JamesMoss\Flywheel\Repository))
      throw new Exception (
        "Flywheel failed to create the user table; check that UserSystem "
          . "has write permissions."
      );
    $this->BLOBS = new \JamesMoss\Flywheel\Repository("blobs", $this->FLYWHEEL);
    if (!($this->BLOBS instanceof \JamesMoss\Flywheel\Repository))
      throw new Exception (
        "Flywheel failed to create the blobs table; check that UserSystem "
          . "has write permissions."
      );
  }

  function opensslRand($min = 0, $max = 1000) {
    $range = $max - $min;
    if ($range < 1) return $min;
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
      $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
      $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
  }

  public function createSalt () {
    return hash(
      "sha512",
      time()
      . ($str = substr(
        str_shuffle(
          str_repeat(
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
            . "`~0123456789!@$%^&*()-_+={}[]\\|:;'\"<,>."
            . bin2hex(openssl_random_pseudo_bytes(64)),
            $this->opensslRand(32, 64+strlen(SITE_NAME))
          )
        ),
        1,
        $this->opensslRand(2048, 8192)
      ))
      . ($strt = bin2hex(openssl_random_pseudo_bytes(strlen($str)/8)))
      . strlen($strt)*$this->opensslRand(4, 128)
    );
  }

  public function getIP () {
    $srcs = [
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR'
    ];
    foreach ($srcs as $key)
      if (array_key_exists($key, $_SERVER) === true)
        foreach (explode(',', $_SERVER[$key]) as $ip)
          if (filter_var($ip, FILTER_VALIDATE_IP) !== false) return $ip;
    return false;
  }

  public function sanitize ($data, $type = "s") {
    if ($type == "s") {
      $data = preg_replace_callback('/[\x{80}-\x{10FFFF}]/u', function($match) {
            list($utf8) = $match;
            return mb_convert_encoding($utf8, 'HTML-ENTITIES', 'UTF-8');
          },
        $data);
      $data = filter_var($data, FILTER_SANITIZE_STRING);
      return filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } elseif ($type == "e") {
      if (
        filter_var(
          filter_var(
            $data,
            FILTER_SANITIZE_EMAIL
          ),
          FILTER_VALIDATE_EMAIL
        )
        ===
        $data
      ) return $data;
    }
    return "FAILED";
  }

  public function session ($user = false) {
    if (!isset($_COOKIE[SITE_NAME]) && !$user) return "cookie";
    $user = filter_var(
      $_COOKIE[SITE_NAME],
      FILTER_SANITIZE_FULL_SPECIAL_CHARS
    );
    $search = $this->BLOBS->query()->where("blob", "==", $user)
      ->where("date", ">", time())->where("action", "==", "session")->execute();
    return $search;
  }

  public function addUser ($username, $password, $email, $ifttt_key) {
    $username = $this->sanitize($username, "s");
    $ifttt_key = $this->sanitize($ifttt_key, "s");
    $email = $this->sanitize($email, "e");
    $ucheck = $this->USERS->query()->where("username", "==", $username)
     ->execute();
    if (count($ucheck) === 0) {
      $echeck = $this->USERS->query()->where("email", "==", $email)->execute();
      if (count($echeck) === 0) {
        $id = $this->USERS->query()->where("id", "!=", 0)
          ->orderBy("id DESC")->limit(1, 0)->execute();
        $id = count($id === 1) ? intval($id->value("id")) + 1 : 1;
        $data = [
          "id" => $id,
          "salt" => $salt = $this->createSalt(),
          "password" => hash("sha512", $password.$salt),
          "username" => $username,
          "email" => $email,
          "ifttt_key" => $ifttt_key
        ];
        $add = new \JamesMoss\Flywheel\Document($data);
        $this->USERS->store($add);
        return true;
      } else {
        return "email";
      }
    } else {
      return "username";
    }
  }

  public function logIn ($username, $password) {
    $ip = $this->getIP();
  }

}