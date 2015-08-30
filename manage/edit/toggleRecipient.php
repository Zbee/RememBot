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

$recipient = $recipients->query()->where("id", "==", $recipient)->execute();

if (count($recipient) !== 1) $USLite->redirect301($url);
if ($recipient->value("list") !== $listI) $USLite->redirect301($url);

#Get all information
$data = [
  "id" => $recipient->value("id"),
  "list" => $recipient->value("list"),
  "name" => $recipient->value("name"),
  "contact" => $recipient->value("contact"),
  "active" => $recipient->value("active")
];

#Switch activation status
$data["active"] = $data["active"] === 1 ? 0 : 1;

#Delete the recipient
foreach ($recipient as $r)
  $recipients->delete($r);

#Re-store the recipient
$update = new \JamesMoss\Flywheel\Document($data);
$update = $recipients->store($update);

$USLite->redirect301($url);