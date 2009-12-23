<?php
require($_SERVER['DOCUMENT_ROOT'].'/etc/main.php');
// получение и обработка данных (ключ, оценка, куда возвращаться)
$key = isset($_POST['key']) ? $_POST['key'] : getvar('key');
$rate = isset($_POST['rate']) ? intval($_POST['rate']) : intval(getvar('rate'));
$back = isset($_POST['back']) ? $_POST['back'] : getvar('back');
// если не было указано куда возвращаться пробуем реферер иначе возвращаем на главную
if(empty($back)) {
  $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
}
// не пускать гостей
if(3 == $USER['id']) raise_error('Извините, но незарегистрированные пользователи не могут учавствовать в оценках.', $back);
// если оценки нет, то добавляем ее
$rating = new rating;
if(!empty($key)) $rating->SetKey($key);
if(TRUE === $rating->Rate($rate)) {
  Header('Location: '.$back, TRUE, 302);
} else {
  raise_error('Извините, но вы уже оценивали данный предмет.', htmlspecialchars($back));
}
?>