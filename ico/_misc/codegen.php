<?php 
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
?>