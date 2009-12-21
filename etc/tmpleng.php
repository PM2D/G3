<?php
// This file is a part of GIII (g3.steelwap.org)
// простой шаблонизатор. а зачем нужен сложный? :)
final class Template {
  // Путь к папке с шаблонами
  private $Dir = NULL;
  // Путь к папке кэша
  private $Cache = NULL;
  public $Vars = array();
  // Имя компилируемого файла
  private $TmplFile = NULL;
  // Имя откомпилированного файла
  private $TmplCache = NULL;

  // Конструктор
  public function __construct() {
    $this->Dir = $_SERVER['DOCUMENT_ROOT'].'/tmpl/';
    $this->Cache = $_SERVER['DOCUMENT_ROOT'].'/var/cache/tmpl/';
  }

  public function SendHeaders() {
    include($this->Dir.$GLOBALS['USER']['tmpl'].'/headers.php');
  }

  public function UseNav() {
    include($this->Dir.$GLOBALS['USER']['tmpl'].'/nav.php');
  }

  // Функция генерации страницы
  public function Parse($filename) {
    // "глобальные" данные
    global $USER;
    // а вот так вот пожалуй с какой-то стороны делать не правильно, зато удобно
    $GZIP = compress::$mode;
    $ONLINE = online::$count;
    // если заголовок не был задан
    // if(!isset($this->Vars['TITLE'])) $this->Vars['TITLE'] = 'http://'.$_SERVER['HTTP_HOST'].htmlspecialchars($_SERVER['REQUEST_URI']);
    //Запоминаем имя файла с шаблонами
    $this->TmplFile = $this->Dir.$USER['tmpl'].'/'.$filename;
    //Запоминаем имя откомпилированного файла с шаблонами
    $this->TmplCache = $this->Cache.abs(crc32($this->TmplFile)).'.dat';

    // Создаем ссылки на переменные из общего массива, чтобы они были видны в шаблоне
    extract($this->Vars, EXTR_REFS);

    // Если существует уже скомпилированная версия файла, то используем ее
    if(file_exists($this->TmplCache)){
      ob_start();
      // Подключаем шаблон
      include($this->TmplCache);
      // Получаем сгенерированный текст
      return ob_get_clean();
    }

    // иначе компилируем файл заново
    // Проверка на существование шаблона
    if(!file_exists($this->TmplFile)) {
      // однако если шаблон не существует, но такой шаблон имеется в Default то берем его
      if(file_exists($this->Dir.'Default/'.$filename)){
        $this->TmplFile = $this->Dir.'Default/'.$filename;
      // иначе выведем ошибку
      } else {
        return 'Ошибка: файл '.$this->TmplFile.' не является шаблоном или не найден.';
      }
    };

    // Получаем текст шаблона
    $text = file_get_contents($this->TmplFile);
    /* Заменяем конструкцию типа: [*...*] на <?..?> */
    $text = strtr($text, array('[*='=>'<?php echo ', '[*'=>'<?php ', '*]'=>' ?>'));
    // Убираем лишние переносы строк и пробелы из шаблона
    $text = preg_replace('/[\n\r\ ]{2,}/', "\n", $text);
    //Записываем код в файл
    $f = fopen($this->TmplCache, 'w');
    fwrite($f, $text);
    fclose($f);

    // Выполняем код и возвращаем сгенерированную страницу
    ob_start();
    include($this->TmplCache);
    return ob_get_clean();
  }

  // Функция выводит все данные из общего массива на страницу.
  // Используется для отладки
  public function PrintVars() {
    echo '<pre>USER ';
    print_r($GLOBALS['USER']);
    echo "\n";
    print_r($this->Vars);
    echo '</pre>';
  }

}

?>