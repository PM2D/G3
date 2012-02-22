<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$uid =& getvar('uid');
$uid = intval($uid);

if(1>$USER['state']) raise_error('Доступ запрещён.');

if(!$uid) raise_error('No user id?');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

function get_ban_mode($i){
  switch($i){
   case 0:
    return 'IP+UA';
   break;
   case 1:
    return 'UA';
   break;
   case 2:
    return 'IP';
   break;
   case 3:
    return 'Cookie[ID]';
   break;
   case 4:
    return 'ID';
   break;
   case 5:
    return 'Total Ban';
   break;
   default:
    return FALSE;
   break;
  }
}

if(isset($_POST['method'])){

  $tmpl->Vars['TITLE'] = 'Добавление пользователя в бан-лист';
  $method = intval($_POST['method']);
  $mysql = new mysql;
  $arr = $mysql->GetRow('`id`,`login`,`state`,`br`,`ip`', 'users', '`id`='.$uid);
  if(0<$arr['state']) raise_error('Нельзя забанить администратора или модератора.');
  switch($method){
   case 0:
    $bstr = '0|:|'.$arr['br'].' IP:'.$arr['ip']."\n";
   break;
   case 1:
    $bstr = '1|:|'.$arr['br']."\n";
   break;
   case 2:
    $bstr = '2|:|'.$arr['ip']."\n";
   break;
   case 3:
    $bstr = '3|:|'.$arr['id']."\n";
   break;
   case 4:
    $bstr = '4|:|'.$arr['id']."\n";
   break;
   case 5:
    $bstr = '5|:|---'."\n";
   break;
   default:
    return FALSE;
   break;
  }
  $file = file($_SERVER['DOCUMENT_ROOT'].'/var/ban.dat');
  $file[] = $bstr;
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/ban.dat', 'w');
  fwrite($f, implode(NULL, $file));
  fclose($f);
  $tmpl->Vars['MESSAGE'] = 'Дaнныe дoбaвлeны в бaн лиcт.';
  to_log($USER['login'].' зaбaнил '.$arr['login'].' пo '.get_ban_mode($method));
  if(3==$_POST['method'])
		$tmpl->Vars['MESSAGE'] .= '<br /><small>(!) Bыбpaн мeтoд бaнa c помощью cookies,<br />'.
		'срок действия которых истекает через 7 суток.<br />'.
		'Kaк тoлькo пoльзoвaтeль пoлучит cookie eгo дaнныe будут удaлeны из лиcтa</small>';
  $tmpl->Vars['BACK'] = false;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['TITLE'] = 'Бан пользователя';
  $tmpl->Vars['UID'] = $uid;
  for($i=0; $mode = get_ban_mode($i); $i++){
    $tmpl->Vars['METHODS'][$i] = $mode;
  }
  echo $tmpl->Parse('admin/ban.tmpl');

}

?>