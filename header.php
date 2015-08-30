<?php
define("ASSETS_PATH", "/var/www/remembot/assets/");
define("SITE_NAME", "RememBot");
define("BASE_URL", "rb.zbee.me");

require("vendor/autoload.php");
$flywheel = $config = new \JamesMoss\Flywheel\Config(ASSETS_PATH . "db");

require("assets/php/usersystem_lite.php");
$USLite = new USLite ($flywheel);

$lists = new \JamesMoss\Flywheel\Repository("lists", $flywheel);
$recipients = new \JamesMoss\Flywheel\Repository("recipients", $flywheel);
$messages = new \JamesMoss\Flywheel\Repository("messages", $flywheel);

$session = $USLite->session();

if (!isset($requireAcc)) $requireAcc = false;

if ($requireAcc === true)
  if (!is_array($session))
    $USLite->redirect301("/");

$err = "<br><div class='alert'>%s</div>";
$e = "";
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Text/Mailing lists with scheduled messages">
    <meta name="author" content="Zbee (Ethan Henderson)">

    <title>RememBot</title>

    <link rel='stylesheet' href='/assets/css/style.css' type='text/css'>
  </head>
  <body>