<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if (isset($_GET['clear'])) {
  $mysql = new mysql;
  $mysql->Delete('news');
  $mysql->Delete('news_comms');
  $tmpl->Vars['TITLE'] = 'Очистка новостей';
  $tmpl->Vars['MESSAGE'] = 'Произведена операция очистки новостей.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');
  exit;
}

if (isset($_POST['do'])) {

  $CFG['NEWS']['guests'] = (isset($_POST['guests']) && $_POST['guests']);
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Настройки изменены.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['GUESTS'] = $CFG['NEWS']['guests'];
  echo $tmpl->Parse('admin/news_options.tmpl');

}
?>