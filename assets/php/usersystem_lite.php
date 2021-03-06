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

  public function redirect301($url) {
    if (!headers_sent()) {
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: $url");
      return true;
    }
    return false;
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
            $this->opensslRand(32, 64+strlen(strtolower(SITE_NAME)))
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
    if (!isset($_COOKIE[strtolower(SITE_NAME)]) && !$user) return "cookie";
    $blob = $user = filter_var(
      $_COOKIE[strtolower(SITE_NAME)],
      FILTER_SANITIZE_FULL_SPECIAL_CHARS
    );
    $blobSearch = $this->BLOBS->query()->where("blob", "==", $user)->execute();
    if (count($blobSearch) === 1) {
      $user = $this->USERS->query()
      ->where("id", "==", $blobSearch->value("owner"))->execute();
      if (md5($user->value("salt").substr($blob, 0, 128)) == substr($blob, -32))
        return [
          "id" => $user->value("id"),
          "username" => $user->value("username"),
          "email" => $user->value("email"),
          "salt" => $user->value("salt"),
          "ifttt_key" => $user->value("ifttt_key")
        ];
      return "tamper";
    } else {
      return "session";
    }
  }

  public function insertUserBlob ($id, $action = "session") {
    $id = intval($id);
    $action = $this->sanitize($action);
    $salt = $this->USERS->query()->where("id", "==", $id)->execute();
    if (count($salt) == 0) return false;
    $hash = $this->createSalt();
    $hash = $hash.md5($salt->value("salt").$hash);
    $data = [
      "owner" => $id,
      "action" => $action,
      "expiration" => strtotime('+30 days'),
      "blob" => $hash
    ];
    $store = new \JamesMoss\Flywheel\Document($data);
    $this->BLOBS->store($store);
    return $hash;
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
        $id = $this->USERS->query()->where("id", ">", 0)
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
    $username = $this->sanitize($username);

    $u = $this->USERS->query()->where("username", "==", $username)->execute();
    $e = $this->USERS->query()->where("email", "==", $username)->execute();

    $retr = count($u) > count($e) ? $u : $e;

    if (count($retr) === 1) {
      if (hash("sha512", $password.$retr->value("salt")) == $retr->value("password")) {
        $hash = $this->insertUserBlob($retr->value("id"));
        setcookie(
          strtolower(SITE_NAME),
          $hash,
          strtotime('+30 days'),
          "/",
          BASE_URL
        );
        return true;
      } else {
        return "password";
      }
    } else {
      return "username";
    }
  }

  public function logOut ($code, $user, $cursess = false, $all = false) {
    $code = $this->sanitize($code);
    $user = intval($user);
    $cursess = $cursess == true ? true : false;
    $all = $all == true ? true : false;

    if ($cursess === true) {
      setcookie(
        strtolower(SITE_NAME),
        null,
        strtotime('-60 days'),
        "/",
        BASE_URL
      );
    }

    if (!$all) {
      $matchedBlobs = $this->BLOBS->query()->where("blob", "==", $code)->execute();
    } else {
      $matchedBlobs = $this->BLOBS->query()->where("owner", "==", $user)->execute();
    }
    foreach ($matchedBlobs as $blob)
      $this->BLOBS->delete($blob);

    return false;
  }

}