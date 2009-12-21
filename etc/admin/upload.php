<?php
// This file is a part of GIII (g3.steelwap.org)

if(isset($_POST['gallery_max'])){

  $CFG['AVATAR']['max'] = intval($_POST['avatar_max']);
  $CFG['AVATAR']['allowed'] = strtolower($_POST['avatar_allowed']);
  if(FALSE!==strpos('php', $CFG['AVATAR']['allowed'])) exit;
  $CFG['GALLERY']['max'] = intval($_POST['gallery_max']);
  $CFG['GALLERY']['allowed'] = strtolower($_POST['gallery_allowed']);
  if(FALSE!==strpos('php', $CFG['GALLERY']['allowed'])) exit;
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Hacтpoйки измeнeны.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

} else {

  if(!isset($CFG['AVATAR']))
    $CFG['AVATAR'] = array('max'=>10240, 'allowed'=>'jpg,gif');
  $tmpl->Vars['GALLERY'] = $CFG['GALLERY'];
  $tmpl->Vars['AVATAR'] = $CFG['AVATAR'];
  echo $tmpl->Parse('admin/upload.tmpl');

}
?>