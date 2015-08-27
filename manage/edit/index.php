<?php
$requireAcc = true;
require("../../header.php");

if (isset($_GET) && count($_GET) != 1) $USLite->redirect301("../");
$id = intval(array_search(array_values($_GET)[0], $_GET));

$list = $lists->query()->where("id", "==", $id)->execute();

if (count($list) != 1) $USLite->redirect301("../");

$list = $list->value("id");

if (array_key_exists("arn", $_POST)) {
  $data = [
    "list" => $list,
    "name" => $USLite->sanitize($_POST["arn"]),
    "contact" => strip_tags(trim($_POST["arc"])),
    "active" => 1
  ];
  $store = new \JamesMoss\Flywheel\Document($data);
  $recipients->store($store);
}

$recipientsFL = $recipients->query()->where("list", "==", $list)->execute();
?>

<div id='body'>
  <div class='form'>
    <h1>Manage the recipients of your list, <?=$session["username"]?></h1>
    <?=$e?>
    <br>
    <b>Add a recipient to this list</b>
    <br>
    <form action='' method='post' class='ignore'>
      <input type='text' name='arn' placeholder='Name' class='small'>
      <input type='text' name='arc' placeholder='Email / Phone #' class='small'>
      <button class='btn small'>Add!</button>
    </form>
    <br><br>
    <b>Current recipients: <u><?=count($recipientsFL)?></u></b>
    <br>
    <?php
    foreach ($recipientsFL as $r)
      echo "<div class='combined quarter'>"
          . "<div class='btn'>"
            . "<p>$r->name</p>"
            . "<a href='#' title='Active; Will receive next message'>A</a>"
            . "<a href='#' title='Delete this recipient'>X</a>"
          . "</div>"
        . "</div>";
    ?>
    <br><br>
    <a href='../../help' class='btn half'>Help</a>
    <a href='../' class='btn half'>Manage lists</a>
    <br><br>
    <a href='../../logout' class='btn half'>Log out</a>
  </div>
</div>

<?php require("../../footer.php"); ?>