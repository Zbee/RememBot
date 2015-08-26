<?php
$requireAcc = true;
require("../../header.php");

if (array_key_exists("n", $_POST)) {
  $id = $lists->query()->where("id", "!=", 0)
    ->orderBy("id DESC")->limit(1, 0)->execute();
  $id = count($id === 1) ? intval($id->value("id")) + 1 : 1;
  $data = [
    "id" => $id,
    "owner" => $session["id"],
    "name" => $USLite->sanitize($_POST["n"])
  ];
  $store = new \JamesMoss\Flywheel\Document($data);
  $lists->store($store);
  $USLite->redirect301("../edit?$id&created");
  $e = sprintf($err, "Your list has been created, "
    . "<a href='../edit?$id&created'>add recipients to it now</a>.");
}
?>

<div id='body'>
  <form action='' method='post'>
    <h1>Create a list, <?=$session["username"]?></h1>
    <?=$e?>
    <br>
    <input type='text' placeholder='List name' class='btn full' name='n'>
    <br>
    <button class='btn full'>Create list!</button>
    <br><br>
    <a href='../../help' class='btn half'>Help</a>
    <a href='../' class='btn half'>Manage lists</a>
    <br><br>
    <a href='../../logout' class='btn half'>Log out</a>
  </form>
</div>

<?php require("../../footer.php"); ?>