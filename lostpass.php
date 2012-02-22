<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

$tmpl = new template;
$tmpl->SendHeaders();
$compress->Enable();
$tmpl->Vars['TITLE'] = 'Восстановление пароля';

if(isset($_POST['login']) && isset($_POST['email'])){

  include($_SERVER['DOCUMENT_ROOT'].'/etc/bancheck.php');

  $login = stripslashes(htmlspecialchars($_POST['login']));
  $email = stripslashes(htmlspecialchars($_POST['email']));

  if($_SESSION['code']!=postvar('code')){
    raise_error('Вы ввeли нeвepный кoд пoдтвepждeния', 'lostpass.php?'.SID);
  };

  $mysql = new mysql;
  $arr = $mysql->GetRow('`login`,`pass`,`email`', 'users', '`login`="'.$mysql->EscapeString($login).'"');
  if(!$arr['login'])
    raise_error('Нет такого пользователя.');
  if(!$arr['email'])
    raise_error('Пользователь существует, но поле адреса в анкете не было заполнено. Boccтановление пapoля нeвoзмoжнo.');
  if($arr['email'] != $email)
    raise_error('Введенный адрес и адрес почты из анкеты не совпадают.');

  $text = 'Данное письмо отправлено по запросу на восстановление пароля на сайте '.$_SERVER['HTTP_HOST'].".\n";
  $text.= 'Если это письмо не имеет к Вам никакого отношения или попало к Вам по ошибке, то проигнорируйте его'."\n\n";
  $text.= 'Ваш пароль: '.$arr['pass'];
  $headers = 'MIME-Version: 1.0'."\r\n";
  $headers.= 'Content-type: text/plain; charset=UTF-8'."\r\n";
  $headers .= 'To: '.$email."\r\n";
  $headers .= 'From: '.$_SERVER['HTTP_HOST']."\r\n";
  if(!mail($email, 'Password Recovery', $text, $headers))
    raise_error('Сообщение не было отправлено. Вероятно отправка сообщений недоступна.');

  to_log($_POST['login'].' вoccтaнoвил пapoль пo e-mail');

  $tmpl->Vars['MESSAGE'] = $login.', ваш пароль отправлен вам на ваш почтовый ящик.';
  $tmpl->Vars['BACK'] = false;
  echo $tmpl->Parse('notice.tmpl');

} else {

  // symbols used to draw CAPTCHA
  // alphabet without similar symbols (o=0, 1=l, i=j, t=f)
  $allowed_sym = str_split('23456789abcdeghkmnpqsuvxyz');
  $total_sym = sizeof($allowed_sym);
  while(true){
    $keystr = '';
    for($i=0; $i<4; $i++){
      $idx = mt_rand(0, $total_sym-1);
      $keystr .= $allowed_sym[$idx];
    }
    if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/', $keystr)) break;
  }
  $_SESSION['code'] = $keystr;

  echo $tmpl->Parse('lostpass.tmpl');

}

?>