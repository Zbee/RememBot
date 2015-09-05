<?php
$requireAcc = true;
require("../../../header.php");

if (isset($_GET) && count($_GET) != 1) $USLite->redirect301("../");

$list = intval($_GET["list"]);

$list = $lists->query()->where("id", "==", $list)->execute();

if (count($list) !== 1) $USLite->redirect301("../../");
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
  <form action='' method='post'>
    <h1>
      Create a message for "<?=$list->value("name")?>" list,
      <?=$session["username"]?>
    </h1>
    <?=$e?>
    <br>
    <a href='../?<?=$list->value("id")?>'>
      &larr; Back to <?=$list->value("name")?> list
    </a>
    <br><br>
    <input type='text' name='d' class='full' placeholder='Date'>
    Format dates like:
    <a href='http://xkcd.com/1179/'><?=date("Y-m-d\THi", time())?></a>
    <br><br>
    <textarea name='b' class='full' placeholder='Message' rows='5'></textarea>
    <br>
    <button class='btn'>Create message!</button>
    <br><br><br>
    <a href='../../../help' class='btn half'>Help</a>
    <a href='../../' class='btn half'>Manage lists</a>
    <br><br>
    <a href='../../../logout' class='btn half'>Log out</a>
  </form>
</div>

<?php require("../../../footer.php"); ?>