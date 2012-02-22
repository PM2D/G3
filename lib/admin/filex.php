<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
if(isset($_POST['do'])) {

  $CFG['FILEX']['guests'] = (isset($_POST['guests']) && $_POST['guests']);
  $CFG['FILEX']['view'] = intval($_POST['view']);
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Настройки изменены.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['GUESTS'] = $CFG['FILEX']['guests'];
  $tmpl->Vars['VIEW'] = $CFG['FILEX']['view'];
  echo $tmpl->Parse('admin/filex_options.tmpl');

}
?>