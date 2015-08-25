<?php
require("../header.php");

if (array_key_exists("u", $_POST)) {
  $u = $USLite->USERS->query()->where("username", "==", $_POST["u"])->execute();
  $e = $USLite->USERS->query()->where("email", "==", $_POST["u"])->execute();

  $retr = count($u) > count($e) ? $u : $e;

  if (count($retr) === 1)
    if (hash("sha512", $_POST["p"].$retr->value("salt")) == $retr->value("password")) {
      $e = sprintf($err, "Logged in, <a href='../manage'>manage lists</a> "
        . "now.");
      $hash = hash(
        "sha512",
        time().$_POST["u"].$_SERVER["REMOTE_ADDR"]
          .bin2hex(openssl_random_pseudo_bytes(64))
      );
      $hash = $hash.md5($hash.$retr->value("salt"));
      setcookie(
        SITE_NAME,
        $hash,
        strtotime('+30 days'),
        "/",
        BASE_URL
      );
      $USLite->redirect301("../manage?loggedin");
    }
    else
      $e = sprintf($err, "That password is incorrect. Did you "
        . "<a onClick='forgot()' href='../forgot' id='forQuick'>forget</a> "
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