<?php
require("../header.php");

$u = $em = "";
if (array_key_exists("u", $_GET)) {
  $in = "value='" . $USLite->sanitize($_GET["u"]) . "'";
  if (strpos($_GET["u"], "@") !== false
    && strpos(explode("@", $_GET["u"])[1], ".") !== false)
    $em = $in;
  else
    $u = $in;
}

if (array_key_exists("u", $_POST)) {
  $add = $USLite->addUser($_POST["u"], $_POST["p"], $_POST["e"], $_POST["i"]);
  if ($add === true)
    $e = sprintf($err, "Your account has been created, <a href='../'>Log in "
      . "now</a>.");
  else {
    $e = sprintf($err, "Your account wasn't created because the " . $add
      . " is already in use.");
  }
}
?>

<div id='body'>
  <form action='' method='post' id='form'>
    <h1>Register</h1>
    <?=$e?>
    <br>
    <input type='text' name='u' class='full' placeholder='Username' id='u' <?=$u?>
      <?=array_key_exists("u", $_POST) ? "value='"
        . $USLite->sanitize($_POST["u"]) . "'" : ""?>>
    <br>
    <input type='email' name='e' class='full' placeholder='Email' id='e' <?=$em?>
      <?=array_key_exists("e", $_POST) ? "value='"
        . $USLite->sanitize($_POST["e"], "e") . "'" : ""?>>
    <br>
    <input type='email' name='ce' class='full' placeholder='Confirm Email' id='ce'
      <?=array_key_exists("ce", $_POST) ? "value='"
        . $USLite->sanitize($_POST["ce"], "e") . "'" : ""?>>
    <br>
    <input type='password' name='p' class='full' placeholder='Password' id='p'>
    <br>
    <input type='text' name='i' class='full' placeholder='Secret Key for IFTTT Maker'
      id='i' <?=array_key_exists("i", $_POST) ? "value='"
        . $USLite->sanitize($_POST["i"]) . "'" : ""?>>
    <br><br>
    <button class='btn full'>Register!</button>
    <br>
    <a href='../login' class='btn full'>Log in instead</a>
  </form>
</div>

<?php require ("../footer.php"); ?>