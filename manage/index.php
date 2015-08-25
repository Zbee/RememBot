<?php
require("../header.php");

if (array_key_exists("loggedin", $_GET))
  $e = sprintf($err, "You are logged in.");
?>

<div id='body'>
  <div class='form'>
    <h1>Manage your lists</h1>
    <?=$e?>
    <br>
    <div class='alert'>You have <b>0</b> lists.</div>
    <br>
    <a href='create' class='btn full'>Create a list</a>
    <br>
    <a href='../help' class='btn half'>Help</a>
    <a href='../logout' class='btn half'>Log out</a>
  </div>
</div>

<?php require("../footer.php"); ?>