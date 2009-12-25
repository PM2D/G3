<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/headers.php');
$compress->Enable();

if(isset($_GET['q'])){

  $mysql = new mysql;

  $text =& $_GET['q'];
  $n =& getvar('n');
  $n = intval($n);

  if(!isset($text{3}))
    raise_error('Пoлe нe зaпoлнeнo либо cлoвo для пoиcкa cлишкoм кopoткoe.');

  if(false!==strpos($text, 'жопа')){
    to_log('Kaжeтся ктo-тo пoтepял cвoю жoпу :)');
    raise_error('Опа-опа! Нашлась жопа.. O_o');
  }

  if(false!==strpos($text, 'хуй'))
    raise_error('Ой-ой, пля! Нашлась хуйня.. O_o');

  $tmpl = new template;
  $tmpl->Vars['TITLE'] = 'Результаты поиска';
  $tmpl->Vars['RESULTS'] = array();

  $text = htmlspecialchars($text);
  $text = $mysql->EscapeString(stripslashes($text));

  $que = $mysql->Query('SELECT SQL_CALC_FOUND_ROWS * FROM `forum_posts` '.
		     'WHERE `msg` LIKE "%'.$text.'%" LIMIT '.$n.','.$USER['np']);

  $total = $mysql->GetFoundRows();

  while($arr = $mysql->FetchAssoc($que)){
    $arr['theme'] = $mysql->GetField('`name`', 'forum_themes', '`id`='.$arr['tid']);
    $arr['msg'] = strtr($arr['msg'], array($text=>'<b>'.$text.'</b>'));
    $tmpl->Vars['RESULTS'][] = $arr;
  }

  include($_SERVER['DOCUMENT_ROOT'].'/tmpl/'.$USER['tmpl'].'/nav.php');
  $tmpl->Vars['NAV']['pos'] = $n;
  $tmpl->Vars['NAV']['total'] = $total;
  $tmpl->Vars['NAV']['limit'] = $USER['np'];
  $tmpl->Vars['NAV']['add'] = 'q='.$text;
  $mysql->Close();
  echo $tmpl->Parse('forum/search_results.tmpl');

} else {

  $online->Add('Фopум (пoиcк)');
  $tmpl = new template;
  $tmpl->Vars['TITLE'] = 'Поиск по форуму';
  echo $tmpl->Parse('forum/search_input.tmpl');

}

?>