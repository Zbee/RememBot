<?php
require("../header.php");

$lOL = $lists->query()->where("id", ">", 0)->execute();

if (array_key_exists("loggedin", $_GET))
  $e = sprintf($err, "You are logged in.");
?>

<div id='body'>
  <div class='form'>
    <h1>Manage your lists, <?=$session["username"]?></h1>
    <?=$e?>
    <br>
    <a href='create' class='btn full'>Create a list</a>
    <br>
    <div class='alert'>You have <b><?=count($lOL)?></b> lists already.</div>
    <?php
    foreach ($lOL as $list) {
      echo "<br>";
      echo "<a href='edit?$list->id' class='btn full'>Edit $list->name</a>";
    }
    ?>
    <br>
    <a href='../help' class='btn half'>Help</a>
    <a href='../logout' class='btn half'>Log out</a>
  </div>
</div>

<?php require("../footer.php"); ?>