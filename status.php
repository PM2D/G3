<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
require($_SERVER['DOCUMENT_ROOT'].'/lib/main.php');

if(isset($_POST['status'])){

  $USER['status'] = intval($_POST['status']);
  $USER['sdescr'] = stripslashes(htmlspecialchars(postvar('sdescr')));
  $USER['sdescr'] = substr($USER['sdescr'], 0, 128);
  if(isset($_POST['forward'])) {
    $location = htmlspecialchars($_POST['forward']);
  } elseif(isset($_SERVER['HTTP_REFERER'])) {
    $location = htmlspecialchars($_SERVER['HTTP_REFERER']);
  } else {
    $location = '/?'.SID;
  }
  setcookie('status', $USER['status'], $TIME+2592000, '/auth.php', $_SERVER['HTTP_HOST']);
  setcookie('sdescr', $USER['sdescr'], $TIME+2592000, '/auth.php', $_SERVER['HTTP_HOST']);
  Header('Location: '.$location, true, 302);

} else {

  $tmpl = new template;
  $tmpl->SendHeaders();
  $compress->Enable();
  $tmpl->Vars['TITLE'] = 'Онлайн-статус';
  echo $tmpl->Parse('status.tmpl');

}

?>