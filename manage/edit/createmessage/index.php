<?php
$requireAcc = true;
require("../../../header.php");

if (isset($_GET) && count($_GET) != 1) $USLite->redirect301("../");

$list = intval($_GET["list"]);

$list = $lists->query()->where("id", "==", $list)->execute();

if (count($list) !== 1) $USLite->redirect301("../../");
if ($list->value("owner") !== $session["id"]) $USLite->redirect301("../../");

if (array_key_exists("d", $_POST)) {
  $data = [
    "id" => $messages->query()->where("id", ">", 0)
      ->orderBy("id DESC")->limit(1, 0)->execute()->value("id") + 1,
    "list" => $list->value("id"),
    "date" => strtotime($USLite->sanitize($_POST["d"])),
    "message" => $USLite->sanitize($_POST["b"])
  ];
  $store = new \JamesMoss\Flywheel\Document($data);
  $USLite->redirect301("../" . $list->value("id") . "?messageadded");
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
    <input type='text' name='d' class='full' placeholder='Date'
      <?=array_key_exists("d", $_POST) ? "value='"
        . date("Y-m-d\THi", strtotime($USLite->sanitize($_POST["d"]))) . "'" : ""?>>
    Format dates like:
    <a target='_blank' href='http://xkcd.com/1179/'>
      <?=date("Y-m-d\THi")?>
    </a>
    <br><br>
    <textarea name='b' class='full' placeholder='Message' rows='5'><?=array_key_exists("b", $_POST) ? $USLite->sanitize($_POST["b"]) : ""?></textarea>
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