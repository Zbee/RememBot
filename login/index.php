<?php
require("../header.php");

if (is_array($session))
  $USLite->redirect301("../manage");

if (array_key_exists("u", $_POST)) {
  $login = $USLite->logIn($_POST["u"], $_POST["p"]);

  if ($login === true) {
    $e = sprintf($err, "Logged in, <a href='../manage'>manage lists</a> now.");
    $USLite->redirect301("../manage?loggedin");
  } elseif ($login === "password")
    $e = sprintf($err, "That password is incorrect. Did you <a onClick='forgot()' href='../forgot' id='forQuick'>forget</a> "
        . "it?");
  else
    $e = sprintf($err, "No such user. Would you like to <a onClick='register()"
      . "' href='../register' id='regQuick'>create an account</a>?");
}
?>

<div id='body'>
  <form action='' method='post' id='form'>
    <h1>Log In</h1>
    <?=$e?>
    <br>
    <input type='text' name='u' class='full' placeholder='Username / Email'
      <?=array_key_exists("u", $_POST) ? "value='"
        . $USLite->sanitize($_POST["u"]) . "'" : ""?>>
    <br>
    <input type='password' name='p' class='full' placeholder='Password' id='p'>
    <br>
    <button class='btn full'>Log in!</button>
    <br>
    <a href='../forgot' class='btn half'>Forgot?</a>
    <a href='../register' class='btn half'>Register</a>
  </form>
</div>

<script>
document.getElementById('regQuick').removeAttribute('href'); 
document.getElementById('forQuick').removeAttribute('href');
function register () {
  document.getElementById('p').value = '';
  document.getElementById('form').action = '../register';
  document.getElementById('form').method = 'get';
  document.getElementById('form').submit();
}
function forgot () {
  document.getElementById('p').value = '';
  document.getElementById('form').action = '../forgot';
  document.getElementById('form').method = 'get';
  document.getElementById('form').submit();
}
</script>

<?php require ("../footer.php"); ?>