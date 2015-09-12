<?php
$requireAcc = true;
require("../../header.php");

if (isset($_GET) && count($_GET) != 1) $USLite->redirect301("../");
$id = intval(array_search(array_values($_GET)[0], $_GET));

$listO = $list = $lists->query()->where("id", "==", $id)->execute();

if (count($list) != 1) $USLite->redirect301("../");

if ($list->value("owner") !== $session["id"]) $USLite->redirect301("../");

$list = $list->value("id");

if (array_key_exists("arn", $_POST)) {
  $data = [
    "id" => $recipients->query()->where("id", ">", 0)
      ->orderBy("id DESC")->limit(1, 0)->execute()->value("id") + 1,
    "list" => $list,
    "name" => $USLite->sanitize($_POST["arn"]),
    "contact" => strip_tags(trim($_POST["arc"])),
    "active" => 1
  ];
  $store = new \JamesMoss\Flywheel\Document($data);
  $recipients->store($store);
  $e = sprintf($err, "Recipient has been added.");
}

$recipientsFL = $recipients->query()->where("list", "==", $list)
  ->orderBy("name ASC")->execute();

$messagesFL = $messages->query()->where("list", "==", $list)
  ->orderBy("date ASC")->execute();
?>

<div id='body'>
  <div class='form'>
    <h1>Manage the recipients of your list "<?=$listO->value("name")?>",
      <?=$session["username"]?></h1>
    <?=$e?>
    <br>
    <div class='alert'>
      Your IFTTT recipe must have an event name of:
      RememBot_<?=
      strtoupper(
        substr(
          hash("sha512", $listO->value("name").$session["salt"]),
          0,
          5
        )
      )
      ?>
    </div>
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
            . "<a href='modifyrecipient?$r->id' "
              . "title='Modify this recipient; contact: $r->contact'>"
            . "$r->name</a>"
            . "<a href='toggleRecipient.php?list=$list&recipient=$r->id' "
              . "title='" . ($r->active === 1
                  ? "Active; Will receive next message"
                  : "Inactive; Will not receive next message")
              . "'>"
              . ($r->active === 1 ? "A" : "i") . "</a>"
            . "<a href='deleteRecipient.php?list=$list&recipient=$r->id' "
              . "title='Delete this recipient'>&times;</a>"
          . "</div>"
        . "</div> ";
    ?>
    <br><br>
    <b>Scheduled messages: <u><?=count($messagesFL)?></u></b>
    <br>
    <?php
    $hidden = "";
    foreach ($messagesFL as $m)
      if ($m->sent === 0)
        echo "<div class='combined quarter'>"
          . "<div class='btn'><a href='modifymessage?$m->id'>"
          . date("Y-m-d\THi", $m->date)
          . "</a></div></div>";
      else
        $hidden .= "<div class='combined quarter'>"
          . "<div class='btn'><a href='modifymessage?$m->id'>"
          . date("Y-m-d\THi", $m->date)
          . "</a></div></div>";
      echo "<div class='hidden' id='hidden'>$hidden</div>";
    ?>
    <br>
    <a href='createmessage?list=<?=$list?>' class='btn small quarter'>
      Create a message
    </a>
    <a href='#' onClick='claps.show("hidden"), claps.show("hiddenb"), claps.hide("hiddens")' class='btn small quarter' id='hiddens'>
      Show sent messages
    </a>
    <a href='#' onClick='claps.hide("hidden"), claps.hide("hiddenb"), claps.show("hiddens")' class='btn small quarter hidden' id='hiddenb'>
      Hide sent messages
    </a>
    <br><br>
    <a href='../../help' class='btn half'>Help</a>
    <a href='../' class='btn half'>Manage lists</a>
    <br><br>
    <a href='../../logout' class='btn half'>Log out</a>
  </div>
</div>

<?php require("../../footer.php"); ?>