<?php
require("header.php");

if (array_key_exists("loggedout", $_GET))
  $e = sprintf($err, "You are logged out.") . "<br>";
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
    (Ethan Henderson)</a>, open source on
    <a href='https://github.com/zbee/remembot' target='_blank'>GitHub</a>
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