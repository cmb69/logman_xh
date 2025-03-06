<?php

use Logman\Model\Entry;

if (!isset($this)) {http_response_code(403); exit;}

/** @var list<Entry> $entries */
?>

<h1>Logman – <?=$this->text('menu_main')?></h1>
<table>
  <tr>
    <th><?=$this->text('label_timestamp')?></th>
    <th><?=$this->text('label_level')?></th>
    <th><?=$this->text('label_module')?></th>
    <th><?=$this->text('label_category')?></th>
    <th><?=$this->text('label_description')?></th>
  </tr>
<?foreach ($entries as $entry):?>
  <tr>
    <td><?=$this->esc($entry->timestamp)?></td>
    <td><?=$this->esc($entry->level)?></td>
    <td><?=$this->esc($entry->module)?></td>
    <td><?=$this->esc($entry->category)?></td>
    <td><?=$this->esc($entry->description)?></td>
  </tr>
<?endforeach?>
</table>
