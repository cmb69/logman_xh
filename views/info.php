<?php

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var string $version
 * @var list<object{class:string,key:string,arg:string,result:string}> $checks
 */
?>

<h1>Logman <?=$this->esc($version)?></h1>
<div class="logman_syscheck">
  <h2><?=$this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="<?=$this->esc($check->class)?>"><?=$this->text($check->key, $check->arg)?><?=$this->text($check->result)?></p>
<?endforeach?>
</div>
