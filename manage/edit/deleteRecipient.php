<?php
$requireAcc = true;
require("../../header.php");

#Setting up variables that will be used
$recipient = intval($_GET["recipient"]);
$listI = $list = intval($_GET["list"]);
$url = "../edit/?$list";

#Checking that everything exists and is owned by the logged in user
$list = $lists->query()->where("id", "==", $list)->execute();

if (count($list) !== 1) $USLite->redirect301($url);
if ($list->value("owner") !== $session["id"]) $USLite->redirect301($url);

$recipient = $recipients->query()->where("id", "==", $recipient)
  ->limit(1, 0)->execute();

if (count($recipient) !== 1) $USLite->redirect301($url);
if ($recipient->value("list") !== $listI) $USLite->redirect301($url);

#Delete the recipient
foreach ($recipient as $r)
  $recipients->delete($r);

$USLite->redirect301($url);