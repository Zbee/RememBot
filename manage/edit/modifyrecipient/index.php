<?php
$requireAcc = true;
require("../../../header.php");

if (isset($_GET) && count($_GET) != 1) $USLite->redirect301("../");
$id = intval(array_search(array_values($_GET)[0], $_GET));

$recipient = $recipients->query()->where("id", "==", $id)->execute();

if (count($recipient) != 1) $USLite->redirect301("../../");

$list = $lists->query()->where("id", "==", $recipient->value("list"))->execute();

if ($list->value("owner") !== $session["id"]) $USLite->redirect301("../../");

if (array_key_exists("n", $_POST)) {
  $compare = [
    "n" => $recipient->value("name"),
    "c" => $recipient->value("contact")
  ];
  if ($compare !== $_POST) {
    $data = [
      "id" => $recipient->value("id"),
      "list" => $recipient->value("list"),
      "name" => $USLite->sanitize($_POST["n"]),
      "contact" => $USLite->sanitize($_POST["c"]),
      "active" => $recipient->value("active")
    ];
    $update = new \JamesMoss\Flywheel\Document($data);
    foreach ($recipient as $r)
      $recipients->delete($r);
    $recipients->store($update);
    $recipient = $recipients->query()->where("id", "==", $id)->execute();
    $e = sprintf($err, "This recipient has been updated.");
  }
}
?>

<div id='body'>
  <div class='form'>
    <h1>Modify recipient "<?=$recipient->value("name")?>", <?=$session["username"]?></h1>
    <?=$e?>
    <br>
    <a href='../?<?=$list->value("id")?>'>
      &larr; Back to <?=$list->value("name")?> list
    </a>
    <br><br>
    <form action='' method='post'>
      <b>Name:</b>
        <input type='text' name='n' class='small' value='<?=$recipient->value("name")?>'>
      <br>
      <b>Contact:</b>
        <input type='text' name='c' class='small' value='<?=$recipient->value("contact")?>'>
      <br>
      <b>Active:</b> <a href='../toggleRecipient.php?<?=$recipient->value("id")?>'>
          <?=($a=$recipient->value("active")) === 1 ? "Yes" : "No"?>
        </a>
      <br><br>
      <button class='btn'>Save changes</button>
    </form>
    <br><br>
    <a href='../../../help' class='btn half'>Help</a>
    <a href='../../' class='btn half'>Manage lists</a>
    <br><br>
    <a href='../../../logout' class='btn half'>Log out</a>
  </div>
</div>

<?php require("../../../footer.php"); ?>