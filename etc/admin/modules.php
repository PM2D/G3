<?php
// This file is a part of GIII (g3.steelwap.org)
$installed = explode(',', $CFG['MODS']['installed']);
$active = explode(',', $CFG['MODS']['active']);

function drop_db_tables() {
  global $DB_STRUCT;
  $mysql = new mysql;
  $arr = array();
  preg_match_all('/CREATE TABLE `([\S]+)`/', $DB_STRUCT, $arr, PREG_SET_ORDER);
  $cnt = sizeof($arr);
  for($i=0; $i<$cnt; $i++) {
    if(!$mysql->Query('DROP TABLE IF EXISTS `'.$arr[$i][1].'`'))
      throw new Exception('Ошибка SQL запроса "DROP TABLE IF EXISTS `'.$arr[$i][1].'`": '.$mysql->error);
  }
  $mysql->Close();
}

function create_db_tables() {
  global $DB_STRUCT;
  $mysql = new mysql;
  $arr = explode(';', $DB_STRUCT);
  $cnt = sizeof($arr);
  unset($arr[$cnt]);
  $cnt--;
  for($i=0; $i<$cnt; $i++) {
    if(preg_match('/CREATE TABLE `([\S]+)`/', $arr[$i], $table)) $mysql->Query('DROP TABLE IF EXISTS `'.$table[1].'`');
    if(!$mysql->Query($arr[$i])) throw new Exception('Ошибка SQL запроса "'.$arr[$i].'": '.$mysql->error);
  }
  $mysql->Close();
}

function update_db_tables() {
  global $DB_STRUCT;
  $mysql = new mysql;
  $arr = array();
  preg_match_all('/CREATE TABLE `([\S]+)`/', $DB_STRUCT, $arr, PREG_SET_ORDER);
  $cnt = sizeof($arr);
  $CURRENT = '';
  for($i=0; $i<$cnt; $i++) {
    if(FALSE !== $mysql->Query('SHOW CREATE TABLE `'.$arr[$i][1].'`')) {
      $tmp = $mysql->FetchRow();
      $CURRENT .= $tmp[1].";\n";
    }
  }
  $sync = new dbStructUpdater;
  $arr = $sync->GetUpdates($CURRENT, $DB_STRUCT);
  $cnt = sizeof($arr);
  for($i=0; $i<$cnt; $i++) {
    if(!$mysql->Query($arr[$i])) throw new Exception('Ошибка SQL запроса "'.$arr[$i].'": '.$mysql->error);
  }
  $mysql->Close();
}

// установка модуля
if(isset($_GET['install'])) {

  $path = getvar('name');
  $module = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.info');

  try {
    include($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.php');
    if(in_array($module['name'], $installed)) throw new Exception('Модуль "'.$module['title'].'" уже установлен.');
    install();
    $installed[] = $module['name'];
    $CFG['MODS']['installed'] = implode(',', $installed);
    save_cfg();
  } catch (Exception $e) {
    raise_error('Ошибка установки: '.$e->GetMessage(), 'admin.php?mod=modules&amp;'.SID);
  }

  $tmpl->Vars['MESSAGE'] = 'Модуль "'.$module['title'].'" установлен.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=modules&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
  exit;

// активация модуля
} elseif(isset($_GET['activate'])) {

  $path = getvar('name');
  $module = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.info');

  try {
    if(in_array($module['name'], $active)) throw new Exception('Модуль "'.$module['title'].'" уже активен.');
    $active[] = $module['name'];
    $CFG['MODS']['active'] = implode(',', $active);
    save_cfg();
  } catch (Exception $e) {
    raise_error($e->GetMessage(), 'admin.php?mod=modules&amp;'.SID);
  }

  $tmpl->Vars['MESSAGE'] = 'Модуль "'.$module['title'].'" активирован.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=modules&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
  exit;

// отключение модуля
} elseif(isset($_GET['suspend'])) {

  $path = getvar('name');
  $module = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.info');

  try {
    if(!in_array($module['name'], $active)) throw new Exception('Модуль "'.$module['title'].'" уже отключен.');
    $i = array_search($module['name'], $active);
    unset($active[$i]);
    $CFG['MODS']['active'] = implode(',', $active);
    save_cfg();
  } catch (Exception $e) {
    raise_error($e->GetMessage(), 'admin.php?mod=modules&amp;'.SID);
  }

  $tmpl->Vars['MESSAGE'] = 'Модуль "'.$module['title'].'" отключен.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=modules&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
  exit;

// удаление модуля
} elseif(isset($_GET['uninstall'])) {

  $path = getvar('name');
  $module = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.info');
  if(isset($_GET['sure'])){

    try {
      include($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.php');
      uninstall();
      $i = array_search($module['name'], $installed);
      unset($installed[$i]);
      if(FALSE !== $i = array_search($module['name'], $active)) unset($active[$i]);
      $CFG['MODS']['installed'] = implode(',', $installed);
      $CFG['MODS']['active'] = implode(',', $active);
      save_cfg();
      fstools::clear_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/tmpl/');
    } catch (Exception $e) {
      raise_error('Ошибка при удалении: '.$e->GetMessage(), 'admin.php?mod=modules&amp;'.SID);
    }

    $tmpl->Vars['MESSAGE'] = 'Модуль "'.$module['title'].'" деинсталлирован.';
    $tmpl->Vars['BACK'] = 'admin.php?mod=modules&amp;'.SID;
    echo $tmpl->Parse('notice.tmpl');
    exit;

  } else {

    $tmpl->Vars['TITLE'] = 'Деинсталляция модуля';
    $tmpl->Vars['MESSAGE'] = 'Вы уверены, что хотите деинсталлировать модуль '.$module['name'].'? Это может привести к потере данных данного модуля.';
    $tmpl->Vars['YES'] = 'admin.php?mod=modules&amp;uninstall&amp;name='.$module['name'].'&amp;sure&amp;'.SID;
    $tmpl->Vars['NO'] = 'admin.php?mod=modules&amp;'.SID;
    echo $tmpl->Parse('confirm.tmpl');
    exit;

  }

// обновление модуля
} elseif(isset($_GET['update'])) {

  $path = getvar('name');
  $module = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.info');

  try {
    if(!in_array($module['name'], $installed)) throw new Exception('Модуль "'.$module['title'].'" не установлен.');
    include($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_module.php');
    if(!function_exists('update')) throw new Exception('Данная версия модуля "'.$module['title'].'" не поддерживает функцию обновления.');
    update();
    fstools::clear_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/tmpl/');
  } catch (Exception $e) {
    raise_error($e->GetMessage(), 'admin.php?mod=modules&amp;'.SID);
  }


  $tmpl->Vars['MESSAGE'] = 'Модуль "'.$module['title'].'" обновлен.';
  $tmpl->Vars['BACK'] = 'admin.php?mod=modules&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
  exit;

} elseif(isset($_GET['changelog'])) {

  $path = getvar('name');
  $tmpl->Vars['MESSAGE'] = nl2br(htmlspecialchars(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/_changelog.txt')));
  $tmpl->Vars['BACK'] = 'admin.php?mod=modules&amp;'.SID;
  echo $tmpl->Parse('notice.tmpl');
  exit;

}

// вывод всех модулей
$d = dir($_SERVER['DOCUMENT_ROOT']);
$tmpl->Vars['MODULES'] = array();
while($str = $d->read()) {
  if($str[0]!='.' && is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$str) && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$str.'/_module.info')) {
     $module = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$str.'/_module.info');
     $tmpl->Vars['MODULES'][] = array(
			'title' => $module['title'],
			'name' => $module['name'],
			'version' => $module['version'],
			'author' => isset($module['author']) ? $module['author'] : 'N/A',
			'installed' => in_array($module['name'], $installed),
			'active' => in_array($module['name'], $active),
			'changelog' => file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$str.'/_changelog.txt')
     );
  }
}
$d->close();

echo $tmpl->Parse('admin/modules.tmpl');

?>