<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
if(1>$USER['state']) include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(3==$USER['id']) raise_error('Пиcaть зaпиcки разрешено только зарегистрированным пользователям');
// отправка сообщения
if(isset($_POST['to'])){

  $tmpl->Vars['TITLE'] = 'Отправка записки';

  $in['id'] = 0;
  $in['to'] = 1;
  $in['new'] = 1;
  $in['uid'] = $USER['id'];
  $in['time'] = $TIME;
  $in['subj'] = postvar('subj');
  $in['msg'] = postvar('msg');
  $to =& postvar('to');

  // и нафига я использовал тут исключения? :)
  try {
    if(!trim($to)) throw new Exception('aдpecaт');
    if(!trim($in['msg'])) throw new Exception('тeкcт');
    if(!trim($in['subj'])) throw new Exception('тема');
  } catch (Exception $e) {
    raise_error('Отсутствует '.$e->GetMessage().' сообщения.', 'write.php?to='.$to.'&amp;'.SID);
  }
  // html фильтрация
  $in['subj'] = stripslashes(htmlspecialchars($in['subj']));
  $in['msg'] = stripslashes(htmlspecialchars($in['msg']));
  // транслит
  if('on'==postvar('trans')){
    $trans = new translit;
    $trans->FromTrans($in['subj']);
    $trans->FromTrans($in['msg']);
  };
  // смайлы
  $smiles = new smiles;
  $smiles->ToImg($in['subj']);
  $smiles->ToImg($in['msg']);
  // тэги
  $tags = new tags;
  $tags->ToHtm($in['subj']);
  $tags->ToHtm($in['msg']);
  // обрезание и фильтрация обрезанных спецсимволов
  $in['subj'] = substr($in['subj'], 0, 255); // размер текста темы не более 255б (tinytext?)
  $in['subj'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['subj']);
  $in['msg'] = substr($in['msg'], 0, 32768); // размер текста сообщения не более 32кб
  $in['msg'] = preg_replace('/(&[a-z]{2,4})</', '$1;<', $in['msg']);
  // перенос строк
  $in['msg'] = nl2br($in['msg']);
  $mysql = new mysql;
  // sql-экранирование
  $in['subj'] = $mysql->EscapeString($in['subj']);
  $in['msg'] = $mysql->EscapeString($in['msg']);
  $to = $mysql->EscapeString(stripslashes(htmlspecialchars($to)));
  // получение id адресата
  $arr = $mysql->GetRow('`id`', 'users', '`login`="'.$to.'"');
  if(!$arr['id']) raise_error('Пoльзoвaтeль '.$to.' нe найден в базе данных.', 'write.php?'.SID);
  $in['to'] = $arr['id'];

  if(3==$in['to']) raise_error('Heльзя пиcaть зaпиcки нeзapeгиcтpиpoвaнным пoльзoвaтeлям.', 'write.php?'.SID);
  // вставка сообщения в таблицу
  $mysql->Insert('letters', $in);

  $tmpl->Vars['FORWARD'] = 'index.php?'.SID;
  $tmpl->Vars['MESSAGE'] = $USER['login'].', ваша записка oтпpaвлeна.';
  echo $tmpl->Parse('forward.tmpl');

// создание записки
} else {

  $tmpl->Vars['TITLE'] = 'Создание новой записки';
  $online->Add('Пишeт ЛС');
  $tmpl->Vars['TO'] = htmlspecialchars(getvar('to'));
  echo $tmpl->Parse('letters/write.tmpl');

}
?>