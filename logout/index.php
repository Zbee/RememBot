<?php
clearstatcache();
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require("../header.php");

$url = isset($_GET["url"]) ? $_GET["url"] : "../";
$url .= "?loggedout";

if (isset($_GET["specific"])) {
  $logout = $USLite->logOut($_GET["specific"], $session["id"]);
} elseif (isset($_GET["all"])) {
  $logout = $USLite->logOut(
    $_COOKIE[strtolower(SITE_NAME)],
    $session["id"],
    true,
    true
  );
} else {
  $logout = $USLite->logOut(
    $_COOKIE[strtolower(SITE_NAME)], $session["id"], true
  );
}

$USLite->redirect301($url);