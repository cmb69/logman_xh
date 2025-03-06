<?php

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var int $count
 */
?>

<h1>Logman – <?=$this->text('label_delete')?></h1>
<form method="post">
  <p><?=$this->plural('message_confirm_delete', $count)?></p>
  <p class="logman_buttons"><button name="logman_do"><?=$this->text('label_delete')?></button></p>
</form>
