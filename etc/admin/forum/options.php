<?php
// This file is a part of GIII (g3.steelwap.org)
if(isset($_POST['do'])){

  $CFG['FORUM']['guests'] = (isset($_POST['guests']) && $_POST['guests']);
  $CFG['FORUM']['attcid'] = isset($_POST['attcid']) ? intval($_POST['attcid']) : FALSE;
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Настройки изменены.';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['GUESTS'] = $CFG['FORUM']['guests'];
  $tmpl->Vars['FCATS'] = array();
  if(IsModInstalled('filex')){
    $mysql = new mysql;
    $mysql->Query('SELECT `id`,`title` FROM `filex_cats`');
    while($arr = $mysql->FetchAssoc()){
      $arr['selected'] = ($arr['id']==$CFG['FORUM']['attcid']);
      $tmpl->Vars['FCATS'][] = $arr;
    }
  }

  echo $tmpl->Parse('admin/forum_options.tmpl');
}
?>