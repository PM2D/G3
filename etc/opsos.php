<?php
// This file is a part of GIII (g3.steelwap.org)
function GetOperator($ip){
 $ipnum = ip2long($ip);
 if(!$ipnum) return 'Error';
 $ops[]=array(
  'Мегафон',
  array(1402273792,1402277888,1402279936,1402281984,1402284032,1402286080,1402287104,3251233792,3582031776),
  array(1402275839,1402278911,1402280959,1402283007,1402285055,1402287103,1402288127,3251234815,3582031807)
 );
 $ops[]=array(
  'Билайн',
  array(1433657344,3648405504,3648406528,3648408576,3648409600,3648410624,3648411648,3648412672),
  array(1433657599,3648406527,3648407551,3648409599,3648410623,3648411647,3648412671,3648413695)
 );
 $ops[]=array(
  'БайкалВестКом',
  array(1347599104,3588519936),
  array(1347599359,3588520447)
 );
 $ops[]=array(
  'Utel',
  array(1047070464,1401450496,1401451520,1406861312,1425989632,1441366016,3272364544,3564675072,3564676832,3571187712,3641816064,3641816576),
  array(1047072255,1401450751,1401451775,1406869503,1425994239,1441371903,3272364799,3564683263,3564676863,3571253247,3641816319,3641816831)
 );
 $ops[]=array(
  'МТС',
  array(1042394624,1346950400,1347674112,1360933376,1372794624,1410457600,3258356736,3277188608,3281465344,3287259392,3559689216,3562834880,3578831872,3579259392,3579267072,3579269120,3641237504,3641237504,3645018112,3645566976,3648478720),
  array(1042394879,1346950655,1347678207,1360933887,1372794879,1410459647,3258357759,3277188863,3281465599,3287259647,3559689471,3562834943,3578832127,3579265535,3579267935,3579273215,3641241599,3641240575,3645019135,3645569023,3648479231)
 );
 $ops[]=array(
  'TELE2',
  array(1404043264,2197028864),
  array(1405091839,2197094399)
 );
 $ops[]=array(
  'МОТИВ',
  array(3650368512),
  array(3650368767)
 );
 $ops[]=array(
  'Енисейтелеком',
  array(3278955480,3278956288,3278960128),
  array(3278955515,3278956543,3278960383)
 );
 $ops[]=array(
  'НСС',
  array(1389383040,1432330240,3282161664,3282171904,3585171456),
  array(1389383167,1432334335,3282163711,3282173951,3585174271)
 );
 $ops[]=array(
  'СМАРТС',
  array(1346736128,3260286336,3645731072),
  array(1346737151,3260286463,3645731079)
 );
 $ops[]=array(
  'Татинком-Т',
  array(1481787392,3652001536),
  array(1481787647,3652001599)
 );
 $ops[]=array(
  'GSM',
  array(1052193280,1347125248,1347125248,1347126528),
  array(1052193535,1347129343,1347125759,1347127167)
 );
 $ops[]=array(
  'НТК',
  array(1358118912,1358119680),
  array(1358119423,1358123007)
 );
 $ops[]=array(
  'SkyLink',
  array(1406740480,1406741504,3564593152,3564595968,3564599808,3565248512,3565250048,3565250560,3578853376,3648215680),
  array(1406741503,1406743551,3564593407,3564596735,3564601343,3565256703,3565250559,3565254655,3578854079,3648215807)
 );
 $ops[]=array(
  'Цифровая экспансия',
  array(3651751936,3651752192,3651755008),
  array(3651752191,3651752447,3651756031)
 );
 $ops[]=array(
  'СТЕК GSM',
  array(1388851200),
  array(1388851455)
 );
 $ops[]=array(
  'ИНДИГО',
  array(3287244288),
  array(3287244543)
 );
 $ops[]=array(
  'Киевстар',
  array(3240705024),
  array(3240706047)
 );
 $ops[]=array(
  'UMC',
  array(1358905344,1358905344,1358907392,1358907648,1358907904,1358908160,1358908416,1358908672,1358908928,1358909184,1490436096),
  array(1358909439,1358905471,1358907647,1358907903,1358908159,1358908415,1358908671,1358908927,1358909183,1358909439,1490444287)
 );
 $ops[]=array(
  'life:)',
  array(3560612352,3560612864),
  array(3560612863,3560613887)
 );
 $ops[]=array(
  'WellCOM',
  array(3253698560),
  array(3253699071)
 );
 $ops[]=array(
  'KCELL',
  array(3274702592),
  array(3274702847)
 );
 $ops[]=array(
  'KARTEL',
  array(3557661440),
  array(3557661695)
 );
 $ops[]=array(
  'VELCOM',
  array(3563233536),
  array(3563233791)
 );
 $ops[]=array(
  'Прибалтика',
  array(1410273024,2197079040,3588391680,3588406784,3588407040),
  array(1410273279,2197079295,3588391935,3588407039,3588407295)
 );
 $ops[]=array(
  'Билайн UA',
  array(3253698816),
  array(3253699071)
 );
 $ops[]=array(
  'Кыргызстан',
  array(3583086592,3583086848),
  array(3583086847,3583087103)
 );
 for($i=0;$i<27;$i++){
  $IP1 = $ops[$i][1];
  $IP2 = $ops[$i][2];
  $c = sizeof($IP1);
  for($ix=0; $ix<$c; $ix++){
   if($ipnum>=$IP1[$ix] && $ipnum<=$IP2[$ix]) return $ops[$i][0];
  }
 }
 return 'He oпpeдeлeнo';
}
?>