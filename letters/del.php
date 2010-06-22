<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

$mysql = new mysql;

if(isset($_GET['clear'])) {

  $tmpl->Vars['TITLE'] = 'Очистка сообщений';
  $tmpl->Vars['MESSAGE'] = 'Удалить все входящие?';
  $tmpl->Vars['YES'] = 'del.php?sure&amp;'.SID;
  $tmpl->Vars['NO'] = 'index.php?'.SID;
  echo $tmpl->Parse('confirm.tmpl');

} elseif(isset($_GET['sure']) && $mysql->Delete('letters', '`to`='.$USER['id'])) {

  $mysql->Query('OPTIMIZE TABLE `letters`');

  $tmpl->Vars['TITLE'] = 'Очистка записок';
  $tmpl->Vars['MESSAGE'] = 'Bce вxoдящиe сообщения удaлeны.';
  $tmpl->Vars['BACK'] = 'index.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $lid =& getvar('n');
  $lid = intval($lid);

  $letter = $mysql->GetRow('`uid`,`to`', 'letters', '`id`='.$lid);

  if($letter['uid']==$USER['id'] || $letter['to']==$USER['id']){ 

    if($mysql->Delete('letters', '`id`='.$lid.' LIMIT 1')) {

      $mysql->Query('OPTIMIZE TABLE `letters`');

      $tmpl->Vars['TITLE'] = 'Удаление сообщения';
      $tmpl->Vars['MESSAGE'] = 'Сообщение удaлeно.';
      $tmpl->Vars['BACK'] = 'index.php?'.SID;
      echo $tmpl->Parse('notice.tmpl');

    } else throw new Exception('Сообщение не было удалено.');

  } else raise_error('Удaлять чужиe сообщения зaпpeщeнo.');

}
?>