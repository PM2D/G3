<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
$mysql = new mysql;

// открываем zip архив для создания
$zip = new ZipArchive;
$zip->open($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.date('Y-m-d-H-i').'.zip', ZipArchive::CREATE);

// количество запросов на одну комманду вставки
$lim = 16;

// получаем список всех таблиц в бд
$mysql->Query('SHOW TABLES');
while($row = $mysql->FetchRow()) {
  $tables[] = $row[0];
}

foreach($tables as $table) {
  // открываем файл для записи
  $f = fopen($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.$table.'.sql', 'w');
  // получаем общее количество строк в таблице
  $rows = $mysql->GetField('COUNT(*)', $table, '1');
  // получаем и записываем в файл структуру таблицы
  $mysql->Query('SHOW CREATE TABLE `'.$table.'`') or raise_error($mysql->error);
  $struct = $mysql->FetchRow();
  fwrite($f, 'DROP TABLE IF EXISTS `'.$table.'`;'."\n".$struct[1].";\n");
  // считываем все данные из таблицы разбивая по $lim запросов на комманду
  $mysql->Query('SELECT * FROM `'.$table.'`') or raise_error($mysql->error);
  for($i=0; $i<$rows; $i+=$lim){
    $c = 0;
    $str = 'INSERT INTO `'.$table.'` VALUES';
    while($row = $mysql->FetchRow()) {
      $str .= "\n(";
      $cnt = count($row);
      for($j=0; $j<$cnt; $j++) {
        $str .= "'".$mysql->EscapeString($row[$j])."',";
      }
      $str = substr($str, 0, -1);
      $str .= "),";
      $c++;
      if($lim==$c) break;
    }
    $str = substr($str, 0, -1);
    fwrite($f, $str.";\n");
  }
  //chmod($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.$table.'.sql', 0666);
  fclose($f);
  // добавляем файл в zip архив
  $zip->addFile($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.$table.'.sql', '/'.$table.'.sql');
}
// закрываем zip-архив (сохраняем изменения)
$zip->close();

// удаляем из папки все sql-файлы
$d = dir($_SERVER['DOCUMENT_ROOT'].'/var/backup');
while($str = $d->read()){
 if(substr($str, -3)=='sql'){
   unlink($_SERVER['DOCUMENT_ROOT'].'/var/backup/'.$str);
 }
}
$d->close();

$tmpl->Vars['MESSAGE'] = 'Содержимое всех тaблиц в БД экспopтиpoвaно<br />'.perf().'ceк';
$tmpl->Vars['BACK'] = 'admin.php?mod=backup&amp;'.SID;
echo $tmpl->Parse('notice.tmpl');

?>