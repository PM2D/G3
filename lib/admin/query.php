<?php
// This file is a part of AugurCMS (g3.pm2d.ru)

if(!trim($_POST['que']))
  raise_error('Пуcтoй зaпpoc?', 'admin.php?mod=mysql&amp;'.SID);

$query = stripslashes($_POST['que']);

$mysql = new mysql;
if(!$res = $mysql->Query($query))
  raise_error('Ошибка SQL: <small>'.$mysql->error.'</small>', 'admin.php?mod=mysql&amp;'.SID);

if(extension_loaded('mysqli')) {

  $numfields = $res->field_count;
  $numrows = $res->num_rows;

} else {

  $numfields = @mysql_num_fields($res);
  $numrows = @mysql_num_rows($res);

}

if($numrows) {

  $tmpl->Vars['NUMROWS'] = $numrows;
  $tmpl->Vars['NUMFIELDS'] = $numfields;
  $tmpl->Vars['FIELDS'] = array();

  if(extension_loaded('mysqli')) {
    $arr = $mysql->res->fetch_fields();
    foreach($arr as $field){
      $tmpl->Vars['FIELDS'][] = $field->name.'('.$field->length.')';
    }
  } else {
    for($i=0; $i<$numfields; $i++){
       $tmpl->Vars['FIELDS'][] = mysql_field_name($res, $i);
    }
  }

  $tmpl->Vars['ROWS'] = array();
  while($arr = $mysql->FetchRow()){
    $tmpl->Vars['ROWS'][] = $arr;
  }

  echo $tmpl->Parse('admin/sql_view.tmpl');

} else {

  $tmpl->Vars['MESSAGE'] = 'Запрос выполнен.';
  $tmpl->Vars['BACK'] = FALSE;
  echo $tmpl->Parse('notice.tmpl');

}
?>