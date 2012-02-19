<?php
// This file is a part of GIII (g3.steelwap.org)
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();

if(2>$USER['state']) raise_error('Доступ запрещён.');

$mod =& getvar('mod');

function save_cfg() {
  fstools::save_ini($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', $GLOBALS['CFG']);
}

switch($mod) {

 // --- Configs ---
 case 'modules':
  $tmpl->Vars['TITLE'] = 'Модули';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/modules.php');
 break;

 case 'defs':
  $tmpl->Vars['TITLE'] = 'Умолчания';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/defaults.php');
 break;

 case 'upload':
  $tmpl->Vars['TITLE'] = 'Настройки загрузки';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/upload.php');
 break;

 case 'smiles':
  $tmpl->Vars['TITLE'] = 'Управление смайлами';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/smiles.php');
 break;

 case 'cache':
  $tmpl->Vars['TITLE'] = 'Управление кэшем';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/cache.php');
 break;

 // --- Info ---
 case 'refs':
  $tmpl->Vars['TITLE'] = 'Просмотр рефералов';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/referals.php');
 break;

 case 'log':
  $tmpl->Vars['TITLE'] = 'Просмотр лога';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/viewlog.php');
 break;

 case 'phpinfo':
  $tmpl->Vars['TITLE'] = 'Просмотр конфигурации сервера';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/phpinfo.php');
 break;

 // --- Ddir ---
 case 'ddir':
  $tmpl->Vars['TITLE'] = 'Настройки загруз-центра';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/ddir.php');
 break;

 // --- Filex ---
 case 'filex':
  $tmpl->Vars['TITLE'] = 'Настройки обменника';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/filex.php');
 break;

 // --- Forum ---
 case 'forum':
  $tmpl->Vars['TITLE'] = 'Управление разделами форума';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/forums.php');
 break;

 case 'forumopts':
  $tmpl->Vars['TITLE'] = 'Настройки форума';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/options.php');
 break;

 case 'newf':
  $tmpl->Vars['TITLE'] = 'Создание нового форума';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/newf.php');
 break;

 case 'delf':
  $tmpl->Vars['TITLE'] = 'Удаление форума';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/delf.php');
 break;

 case 'editf':
  $tmpl->Vars['TITLE'] = 'Редактирование форума';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/editf.php');
 break;

 case 'movef':
  $tmpl->Vars['TITLE'] = 'Перемещение форума';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/forum/movef.php');
 break;

 // --- Users ---
 case 'users':
  $tmpl->Vars['TITLE'] = 'Удаление пользователей';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/users.php');
 break;

 case 'deluser':
  $tmpl->Vars['TITLE'] = 'Удаление пользователя';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/deluser.php');
 break;

 // --- RSSReader ---
 case 'rssr':
  $tmpl->Vars['TITLE'] = 'Настройки rss-граббера';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/rssr.php');
 break;

 // --- Utilites ---
 case 'mysql':
  $tmpl->Vars['TITLE'] = 'SQL запрос БД';
  echo $tmpl->Parse('admin/sql.tmpl');
 break;

 case 'query':
  $tmpl->Vars['TITLE'] = 'Результат SQL запроса';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/query.php');
 break;

 case 'backup':
  $tmpl->Vars['TITLE'] = 'Резервное копирование';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/backup/backup.php');
 break;

 case 'export':
  $tmpl->Vars['TITLE'] = 'Создание резервной копии';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/backup/export.php');
 break;

 case 'import':
  $tmpl->Vars['TITLE'] = 'Восстановление резервной копии';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/backup/import.php');
 break;

 case 'update':
  $tmpl->Vars['TITLE'] = 'Проверка обновлений';
  $http = new httpquery('g3.pm2d.ru', '/last.php');
  // возможно пригодится для будущей статистики
  $http->sendHeaders['User-Host'] = $_SERVER['HTTP_HOST'];
  $http->sendHeaders['User-Version'] = CMS_VERSION;
  // отправляем запрос, получаем ответ, закрываем сокет
  $http->SendQuery('GET');
  $http->GetResponse();
  $http->Close();
  if(!$http->responseHeaders['Last-Version']) raise_error('Boзможно сервер в данный момент недоступен.');
  $tmpl->Vars['MESSAGE'] = 'Ваша текущая версия: '.CMS_VERSION.'<br />Последняя доступная версия: '.$http->responseHeaders['Last-Version'];
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');
 break;

 // --- Gbook ---
 case 'cleargb':
  $tmpl->Vars['TITLE'] = 'Очистка гостевой';
  $mysql = new mysql;
  $mysql->Delete('gbook');
  $tmpl->Vars['MESSAGE'] = 'Гостевая очищена.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');
 break;

 // --- Chat ---
 case 'chatopts':
  $tmpl->Vars['TITLE'] = 'Настройки чата';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/chat/options.php');
 break;

 case 'newroom':
  $tmpl->Vars['TITLE'] = 'Создание комнаты';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/chat/new.php');
 break;

 case 'editroom':
  $tmpl->Vars['TITLE'] = 'Переименовывание комнаты';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/chat/edit.php');
 break;

 case 'delroom':
  $tmpl->Vars['TITLE'] = 'Удаление комнаты';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/chat/del.php');
 break;

 case 'clearch':
  $tmpl->Vars['TITLE'] = 'Очистка чата';
  $mysql = new mysql;
  $mysql->Delete('chat');
  $tmpl->Vars['MESSAGE'] = 'Чат очищен.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');
 break;

 case 'privch':
  $tmpl->Vars['TITLE'] = 'Приват чата';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/chat/privch.php');
 break;

 // --- News ---
 case 'newsopts':
  $tmpl->Vars['TITLE'] = 'Настройки новостей';
  include($_SERVER['DOCUMENT_ROOT'].'/etc/admin/news.php');
 break;

 // --- * ---
 default:
  $tmpl->Vars['TITLE'] = 'Админка';
  $online->Add('...');
  $tmpl->Vars['DDIR'] = IsModInstalled('ddir');
  $tmpl->Vars['FILEX'] = IsModInstalled('filex');
  $tmpl->Vars['FORUM'] = IsModInstalled('forum');
  $tmpl->Vars['NEWS'] = IsModInstalled('news');
  $tmpl->Vars['GBOOK'] = IsModInstalled('gbook');
  $tmpl->Vars['CHAT'] = IsModInstalled('chat');
  $tmpl->Vars['RSSR'] = IsModInstalled('rssr');  
  echo $tmpl->Parse('admin/index.tmpl');
 break;

}

?>
