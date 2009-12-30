<?php
// This file is a part of GIII (g3.steelwap.org)

if(!isset($CFG['DDIR'])) {
  // defaults:
  $CFG['DDIR']['sort'] = 0;
  $CFG['DDIR']['rev'] = FALSE;
  $CFG['DDIR']['ico'] = 'gif,jpg,jad,jar,mid,mp3,amr,txt,zip,3gp,thm';
  $CFG['DDIR']['unzip'] = FALSE;
  $CFG['DDIR']['ffmpeg'] = FALSE;
  $CFG['DDIR']['view'] = 1;
  save_cfg();
};

if(isset($_POST['do'])) {

  $CFG['DDIR']['sort'] = intval($_POST['sort']);
  $CFG['DDIR']['rev'] = (bool)postvar('rev');
  $CFG['DDIR']['ico'] = postvar('ico');
  $CFG['DDIR']['unzip'] = (bool)postvar('unzip');
  $CFG['DDIR']['ffmpeg'] = (bool)postvar('ffmpeg');
  $CFG['DDIR']['view'] = intval($_POST['view']);
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Hacтpoйки измeнeны.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

} elseif(isset($_GET['rbld'])) {

  $mysql = new mysql;
  $mysql->Delete('dirs');
  $mysql->Delete('files');
  require($_SERVER['DOCUMENT_ROOT'].'/etc/ddir/listroot.php');
  $tmpl->Vars['MESSAGE'] = 'Таблицы загруз-центра обновлены.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $tmpl->Vars['CFG'] = $CFG['DDIR'];
  echo $tmpl->Parse('admin/ddir_options.tmpl');

}
?>