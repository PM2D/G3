<?php
// This file is a part of GIII (g3.steelwap.org)
if(isset($_POST['do'])){

  $CFG['CHAT']['guests'] = (isset($_POST['guests']) && $_POST['guests']);
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Настройки изменены.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['GUESTS'] = $CFG['CHAT']['guests'];
  echo $tmpl->Parse('admin/chat_options.tmpl');

}
?>