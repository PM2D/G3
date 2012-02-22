<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

isset($_GET['uid']) ? $uid = intval($_GET['uid']) : $uid =& $_SESSION['U']['id'];

$mysql = new mysql;
$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if (!isset($_GET['edit'])) {

  $UD = $mysql->GetRow('*', 'users', '`id`='.$uid);
  if(!$UD) raise_error('Heт тaкoгo пoльзoвaтeля');

  $tmpl->Vars['TITLE'] = 'Профиль '.$UD['login'];
  $tmpl->Vars['EDIT'] = FALSE;
  $tmpl->Vars['UD'] = $UD;
  $tmpl->Vars['UD']['references'] = $mysql->GetField('COUNT(*)', 'references','`uid`='.$uid);
  $score = $UD['posts'] + round(($TIME-$UD['regdat'])/86400); // очки
  $score += $tmpl->Vars['UD']['references'];
  if ($UD['name']) $score += 10;
  if (0 < $UD['sex']) $score += 10;
  switch ($tmpl->Vars['UD']['sex']) {
   case 0: $tmpl->Vars['UD']['sex'] = 'Неизвестен'; break;
   case 1: $tmpl->Vars['UD']['sex'] = 'Мужской'; break;
   case 2: $tmpl->Vars['UD']['sex'] = 'Женский'; break;
   case 3: $tmpl->Vars['UD']['sex'] = 'Средний'; break;
   default: break;
  }
  if ($UD['bday'] && $UD['bmonth']) {
    $score += 10;
    // расчет возраста (некорректно работает если возраст более 100. долгожители есть?)
    $now = explode('|', date('j|n|Y', $TIME));
    $tmpl->Vars['UD']['age'] = date('y', mktime(0, 0, 0, $now[1]-$UD['bmonth'], $now[0]-$UD['bday'], $now[2]-$UD['byear']));
    // расчет знака зодиака (скелет алгоритма был найден на просторах рунета)
    $sign = array('Козерог', 'Водолей', 'Рыбы', 'Овен', 'Телец', 'Близнецы', 'Рак', 'Лев', 'Дева', 'Весы', 'Скорпион', 'Стрелец');
    $signstart = array(1=>21, 2=>20, 3=>21, 4=>21, 5=>21, 6=>21, 7=>22, 8=>23, 9=>24, 10=>24, 11=>24, 12=>22);
    $tmpl->Vars['UD']['zodiac'] = ($UD['bday'] < $signstart[$UD['bmonth']]) ? $sign[$UD['bmonth']-1] : $sign[$UD['bmonth']%12];
  } else {
    $tmpl->Vars['UD']['age'] = 'N/A';
    $tmpl->Vars['UD']['zodiac'] = 'N/A';
  }
  $tmpl->Vars['UD']['site'] = $UD['site'] ? explode(' ', $UD['site']) : FALSE;
  if (FALSE !== $tmpl->Vars['UD']['site']) $score += 10;
  $tmpl->Vars['UD']['regdat'] = format_date($UD['regdat']);
  $tmpl->Vars['UD']['last'] = format_date($UD['last']);
  $tmpl->Vars['HAVEALBUM'] = IsModInstalled('gallery') ? file_exists($_SERVER['DOCUMENT_ROOT'].'/gallery/files/'.$UD['id']) : FALSE;
  if (FALSE !== $tmpl->Vars['HAVEALBUM']) $score += 10;
  $tmpl->Vars['HAVEBLOG'] = IsModInstalled('blogs') ? $mysql->IsExists('blogs', '`owner`='.$UD['id']) : FALSE;
  if (FALSE !== $tmpl->Vars['HAVEBLOG']) $score += 10;
  if ($UD['about']) $score += 10;
  if ($UD['icq']) $score += 10;
  if ($UD['email']) $score += 10;
  if ($UD['from']) $score += 10;
  if ($UD['jabber']) $score += 20; // поддержим jabber :)
  $tmpl->Vars['UD']['score'] = $score;
  $tmpl->Vars['HAVEFILES'] = IsModInstalled('filex');
  $tmpl->Vars['STATUS'] = $online->GetStatus($UD['id']);
  $tmpl->Vars['SDESCR'] = $online->GetSDescr($UD['id']);

  $online->Add('Cмoтpит пpoфиль');

} else {

  if (isset($_POST['name']) && 3!=$USER['id']) {

    $upd['name'] = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['name'])));
    $upd['from'] = $mysql->EscapeString(stripslashes(htmlspecialchars($_POST['from'])));
    $upd['sex'] = intval($_POST['sex']);
    $upd['email'] = $mysql->EscapeString(htmlspecialchars($_POST['mail']));
    $upd['site'] = $mysql->EscapeString(htmlspecialchars($_POST['site']));
    $upd['icq'] = preg_match('/^[0-9]{4,9}$/', $_POST['icq']) ? $_POST['icq'] : 0;
    $upd['jabber'] = $mysql->EscapeString(htmlspecialchars($_POST['jabber']));
    $upd['bday'] = intval($_POST['bday']);
    $upd['bmonth'] = intval($_POST['bmonth']);
    $upd['byear'] = intval($_POST['byear']);
    $_POST['about'] = trim($_POST['about']);
    $_POST['about'] = stripslashes(htmlspecialchars($_POST['about']));
    $_POST['about'] = nl2br($_POST['about']);
    $upd['about'] = $mysql->EscapeString($_POST['about']);

    if ($mysql->Update('users', $upd, '`id`='.$USER['id'].' LIMIT 1')) {
      $tmpl->Vars['TITLE'] = 'Обновление профиля';
      $tmpl->Vars['MESSAGE'] = 'Профиль обновлен.<br />[<a href="info.php?'.SID.'">посмотреть</a>]';
      $tmpl->Vars['BACK'] = 'info.php?uid='.$USER['id'].'&amp;edit&amp;'.SID;
      echo $tmpl->Parse('notice.tmpl');
      exit;
    }

  } else {

    $tmpl = new template;
    $tmpl->Vars['TITLE'] = 'Редактирование профиля';
    $tmpl->Vars['EDIT'] = TRUE;
    $tmpl->Vars['UD'] = $mysql->GetRow('*', 'users', '`id`='.$USER['id']);
    if (!$tmpl->Vars['UD']['icq']) $tmpl->Vars['UD']['icq'] = NULL;

    $online->Add('Peдaктиpуeт пpoфиль');

  }
}

echo $tmpl->Parse('info.tmpl');

?>