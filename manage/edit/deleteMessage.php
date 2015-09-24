<?php
$requireAcc = true;
require("../../header.php");

#Setting up variables that will be used
$message = intval($_GET["message"]);
$listI = $list = intval($_GET["list"]);
$url = "../edit/?$list";

#Checking that everything exists and is owned by the logged in user
$list = $lists->query()->where("id", "==", $list)->execute();

if (count($list) !== 1) $USLite->redirect301($url);
if ($list->value("owner") !== $session["id"]) $USLite->redirect301($url);

$message = $messages->query()->where("id", "==", $message)
  ->limit(1, 0)->execute();

if (count($message) !== 1) $USLite->redirect301($url);
if ($message->value("list") !== $listI) $USLite->redirect301($url);

#Delete the message
foreach ($message as $r)
  $messages->delete($r);

$USLite->redirect301($url);