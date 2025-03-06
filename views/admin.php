<?php

use Logman\Model\Entry;

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var int $count
 * @var int $deleted
 * @var string $timestamp
 * @var string $level
 * @var string $module
 * @var string $category
 * @var string $description
 * @var list<string> $months
 * @var list<string> $levels
 * @var list<string> $modules
 * @var list<string> $categories
 * @var list<Entry> $entries
 */
?>

<h1>Logman – <?=$this->text('menu_main')?></h1>
<?if ($deleted >= 0):?>
<p class="xh_info"><?=$this->plural("message_deleted", $deleted)?></p>
<?endif?>
<form class="logman_form" method="get">
  <input type="hidden" name="selected" value="logman">
  <input type="hidden" name="admin" value="plugin_main">
  <input type="hidden" name="logman_count" value="<?=$count?>">
  <div class="logman_table_wrapper">
    <table>
      <thead>
        <tr>
          <th><?=$this->text('label_timestamp')?></th>
          <th><?=$this->text('label_level')?></th>
          <th><?=$this->text('label_module')?></th>
          <th><?=$this->text('label_category')?></th>
          <th><?=$this->text('label_description')?></th>
        </tr>
        <tr>
          <td><input type="search" name="logman_timestamp" value="<?=$this->esc($timestamp)?>" placeholder="<?=$this->text('label_until')?>" list="logman_months"></td>
          <td><input type="search" name="logman_level" value="<?=$this->esc($level)?>" placeholder="<?=$this->text('label_is')?>" list="logman_levels"></td>
          <td><input type="search" name="logman_module" value="<?=$this->esc($module)?>" placeholder="<?=$this->text('label_is')?>" list="logman_modules"></td>
          <td><input type="search" name="logman_category" value="<?=$this->esc($category)?>" placeholder="<?=$this->text('label_is')?>" list="logman_categories"></td>
          <td><input type="search" name="logman_description" value="<?=$this->esc($description)?>" placeholder="<?=$this->text('label_contains')?>"></td>
        </tr>
      </thead>
      <tbody>
<?foreach ($entries as $entry):?>
        <tr>
          <td><?=$this->esc($entry->timestamp)?></td>
          <td><?=$this->esc($entry->level)?></td>
          <td><?=$this->esc($entry->module)?></td>
          <td><?=$this->esc($entry->category)?></td>
          <td><?=$this->esc($entry->description)?></td>
        </tr>
<?endforeach?>
      </tbody>
    </table>
  </div>
  <p class="logman_buttons">
    <button name="action" value="plugin_text"><?=$this->text('label_refresh')?></button>
    <button name="action" value="delete"><?=$this->text('label_delete')?></button>
  </p>
  <datalist id="logman_months">
<?foreach ($months as $month):?>
    <option value="<?=$this->esc($month)?>"></option>
<?endforeach?>
  </datalist>
  <datalist id="logman_levels">
<?foreach ($levels as $level):?>
    <option value="<?=$this->esc($level)?>"></option>
<?endforeach?>
  </datalist>
  <datalist id="logman_modules">
<?foreach ($modules as $module):?>
    <option value="<?=$this->esc($module)?>"></option>
<?endforeach?>
  </datalist>
  <datalist id="logman_categories">
<?foreach ($categories as $category):?>
    <option value="<?=$this->esc($category)?>"></option>
<?endforeach?>
  </datalist>
</form>
