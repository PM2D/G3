<?php
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');

$mysql = new mysql;
$mysql->Query('
CREATE TABLE `ratings` (
  `key` int(10) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
');

if($mysql->error) raise_error('Ошибка MySQL: '.$mysql->error);

if(isset($CFG['DDIR']) && !isset($CFG['DDIR']['view'])) {
  $CFG['DDIR']['view'] = 1;
}

if(isset($CFG['FILEX']) && !isset($CFG['FILEX']['view'])) {
  $CFG['FILEX']['view'] = 0;
}

$f = fopen($_SERVER['DOCUMENT_ROOT'].'/etc/globals.conf', 'w');
if(FALSE===$f) throw new Exception('Невозможно открыть для записи файл /etc/globals.conf');

foreach($CFG as $section=>$array) {
  fwrite($f, '['.$section."]\n");
  foreach($array as $key => $value){
    if(!$value) {
      $value = 'false';
    } elseif(is_bool($value)) {
      $value = 'true';
    } elseif(!is_numeric($value)) {
      $value = '"'.$value.'"';
    }
    fwrite($f, $key.' = '.$value."\n");
  }
  fwrite($f, "\n");
}
fclose($f);

$tmpl = new template;
$tmpl->SendHeaders();
$tmpl->Vars['TITLE'] = 'Обновление';
$tmpl->Vars['MESSAGE'] = 'Файл конфигурации обновлен, таблица оценок создана.';
$tmpl->Vars['BACK'] = FALSE;
echo $tmpl->Parse('notice.tmpl');

?>