<?php
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