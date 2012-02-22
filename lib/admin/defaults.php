<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

$mysql = new mysql;

if(isset($_POST['np']) && isset($_POST['icons']) && isset($_POST['tmpl'])){

  $np =& $_POST['np'];
  $np = intval($np);
  $icons = $mysql->EscapeString($_POST['icons']);
  $template = $mysql->EscapeString($_POST['tmpl']);
  $mysql->Update(
		 'users',
		 array('np'=>$np, 'icons'=>$icons, 'tmpl'=>$template),
		 '`id`=3 LIMIT 1'
		);
  $CFG['AS']['active'] = (isset($_POST['asactive']) && $_POST['asactive']) ? TRUE : FALSE;
  $CFG['AS']['wap'] = stripslashes($_POST['aswap']);
  $CFG['AS']['web'] = stripslashes($_POST['asweb']);
  $CFG['CORE']['gzlevel'] = intval($_POST['gzlevel']);
  save_cfg();
  $tmpl->Vars['MESSAGE'] = 'Настройки изменены';
  $tmpl->Vars['BACK'] = 'admin.php?'.SID;
  echo $tmpl->Parse('notice.tmpl');

} else {

  $row = $mysql->GetRow('`np`,`style`,`icons`,`tmpl`', 'users', '`id`=3');

  $tmpl->Vars['NP'] = $row['np'];

  $d = dir($_SERVER['DOCUMENT_ROOT'].'/ico/');
  while($str = $d->read()){
    if($str[0]!='.' && $str[0]!='_' && $str!='index.php' && $str!='set.php'){
      $tmpl->Vars['ICONS'][] = array(
					'file' => $str,
					'selected' => ($str==$row['icons']),
					'name' => strtr($str, '_', ' ')
				    );
    }
  }
  $d->Close();

  $d = dir($_SERVER['DOCUMENT_ROOT'].'/tmpl/');
  while($str = $d->read()){
    if($str[0]!='.' && $str[0]!='_' && $str!='index.php' && $str!='set.php'){
      $tmpl->Vars['TMPLS'][] = array(
					'file' => $str,
					'selected' => ($str==$row['tmpl']),
					'name' => strtr($str, '_', ' ')
				    );
    }
  }
  $d->Close();

  $tmpl->Vars['AS'] = $CFG['AS'];
  $tmpl->Vars['CORE'] = $CFG['CORE'];

  echo $tmpl->Parse('admin/defaults.tmpl');

}

?>