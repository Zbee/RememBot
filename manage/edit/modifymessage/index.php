<?php
$requireAcc = true;
require("../../../header.php");

if (isset($_GET) && count($_GET) != 1) $USLite->redirect301("../");
$id = intval(array_search(array_values($_GET)[0], $_GET));

$message = $messages->query()->where("id", "==", $id)->execute();

if (count($message) != 1) $USLite->redirect301("../../");

$list = $lists->query()->where("id", "==", $message->value("list"))->execute();

if ($list->value("owner") !== $session["id"]) $USLite->redirect301("../../");

if (array_key_exists("d", $_POST)) {
  $compare = [
    "d" => $message->value("name"),
    "b" => $message->value("contact")
  ];
  if ($compare !== $_POST) {
    $data = [
      "id" => $message->value("id"),
      "list" => $message->value("list"),
      "date" => strtotime($USLite->sanitize($_POST["d"])),
      "message" => $USLite->sanitize($_POST["b"]),
      "sent" => $message->value("sent")
    ];
    $update = new \JamesMoss\Flywheel\Document($data);
    foreach ($message as $r)
      $messages->delete($r);
    $messages->store($update);
    $message = $messages->query()->where("id", "==", $id)->execute();
    $e = sprintf($err, "This message has been updated.");
  }
}
?>

<div id='body'>
  <form action='' method='post'>
    <h1>
      Modify message scheduled for
      <?=date("Y-m-d\THi", $message->value("date"))?>, <?=$session["username"]?>
    </h1>
    <?=$e?>
    <br>
    <a href='../?<?=$list->value("id")?>'>
      &larr; Back to <?=$list->value("name")?> list
    </a>
    <br><br>
    <b>Date</b>
      <input type='text' name='d' class='full' value='<?=date("Y-m-d\THi", $message->value("date"))?>'>
    <br><br>
    <b>Message</b>
      <textarea name='b' class='full' placeholder='Message' rows='5'><?=$USLite->sanitize($message->value("message"))?></textarea>
    <br>
    <b>Sent:</b>
      <?=
      $message->value("sent") === 1
        ? "Yes <sup>(<a href='#'>resend</a>)</sup>"
        : "No"
      ?>
    <br><br>
    <button class='btn'>Save changes</button>
    <a href='../deleteMessage.php?message=<?=$message->value("id")?>&list=<?=$list->value("id")?>'>Delete message</a>
    <br><br><br>
    <a href='../../../help' class='btn half'>Help</a>
    <a href='../../' class='btn half'>Manage lists</a>
    <br><br>
    <a href='../../../logout' class='btn half'>Log out</a>
  </form>
</div>

<?php require("../../../footer.php"); ?>