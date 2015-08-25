<?php
require("../header.php");

$url = isset($_GET["url"]) ? $_GET["url"] : "../";
$url .= "?loggedout";

if (isset($_GET["specific"])) {
  $logout = $USLite->logOut(
    $_GET["specific"], $session["id"], false
  );
  if ($logout === true) $USLite->redirect301($url);
} elseif (isset($_GET["all"])) {
  $logout = $USLite->logOut(
    $_COOKIE[SITENAME],
    $session["id"],
    true,
    true
  );
  if ($logout === true) $USLite->redirect301($url);
} else {
  $logout = $USLite->logOut(
    $_COOKIE[SITENAME], $session["id"], true
  );
  $USLite->redirect301($url);
}