<?php
require("header.php");

if (array_key_exists("u", $_POST)) {
  $u = $USLite->USERS->query()->where("username", "==", $_POST["u"])->execute();
  $e = $USLite->USERS->query()->where("email", "==", $_POST["u"])->execute();

  $retr = count($u) > count($e) ? $u : $e;

  if (count($retr) === 1)
    if (hash("sha512", $_POST["p"].$retr->value("salt")) == $retr->value("password")) {
      $e = sprintf($err, "Logged in, <a href='manage'>manage lists</a> now.");
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
        "rb.zbee.me"
      );
    }
    else
      $e = sprintf($err, "That password is incorrect. Did you "
        . "<a onClick='forgot()' href='forgot' id='forQuick'>forget</a> it?");
  else
    $e = sprintf($err, "No such user. Would you like to <a onClick='register()"
      . "' href='register' id='regQuick'>create an account</a>?");
}
?>

<div id='body'>
  <div class='form'>
    <h1>RememBot</h1>
    <?=$e?>
    RememBot is a system to schedule messages and have them sent to a list of
    people.
    <br><br>
    It's all done with IFTTT too, meaning you could send text message, emails,
    or anything else you can think up.
    <br><br>
    You can use it to make sure all of your friends always hear about your
    weekly, themed parties, keep your team informed about games, or even just to
    make sure you get out of an awkward family thing; all without having to
    scramble to make sure it'll happen.
    <br><br>
    <a href='register' class='btn half'>Register</a>
    <a href='login' class='btn half'>Log in</a>
    <br><br>
    <a href='help' class='btn half'>About</a>
    <br><br>
    Created by <a href='https://keybase.io/zbee' target='_blank'>Zbee
    (Ethan Henderson)</a>
  </div>
</div>

<script>
document.getElementById('regQuick').removeAttribute('href'); 
document.getElementById('forQuick').removeAttribute('href');
function register () {
  document.getElementById('p').value = '';
  document.getElementById('form').action = 'register';
  document.getElementById('form').method = 'get';
  document.getElementById('form').submit();
}
function forgot () {
  document.getElementById('p').value = '';
  document.getElementById('form').action = 'forgot';
  document.getElementById('form').method = 'get';
  document.getElementById('form').submit();
}
</script>

<?php require ("footer.php"); ?>