<?php
require("../../header.php");

if (array_key_exists("n", $_POST)) {
  print $_POST["n"];
}
?>

<div id='body'>
  <form action='' method='post'>
    <h1>Create a list</h1>
    <?=$e?>
    <br>
    <div class='alert'>You have <b>0</b> lists.</div>
    <br>
    <input type='text' placeholder='List name' class='btn full' name='n'>
    <br>
    <button class='btn full'>Create list!</button>
    <br>
    <a href='../../help' class='btn half'>Help</a>
    <a href='../' class='btn half'>Manage lists</a>
  </form>
</div>

<?php require("../../footer.php"); ?>