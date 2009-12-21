<?php
// просто и легко используем browscap, если файл browscap.ini задан в настройках php :)
if(ini_get('browscap')) {
  $browser = get_browser();
  if(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) || $browser->ismobiledevice){
    $USER['tmpl'] = $CFG['AS']['wap'];
  } else {
    $USER['tmpl'] = $CFG['AS']['web'];
  }
// однако далеко не всегда хостер его задает :( поэтому используем свой "костыль" :)
} else {

  function is_mobile() {
    if(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) || isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) || isset($_SERVER['UA-pixels'])){
      return TRUE;
    }
    if(stristr($_SERVER['HTTP_USER_AGENT'], 'windows') && !stristr($_SERVER['HTTP_USER_AGENT'], 'windows ce')){
      return FALSE;
    }
    if(strpos($_SERVER['HTTP_ACCEPT'],'text/vnd.wap.wml') || strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml')){
      return TRUE;
    }
    $arr = array('acs-', 'alav', 'alca', 'amoi', 'audi', 'aste', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco',
		 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits',
		 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'opwv', 'palm', 'pana', 'pant', 'pdxg', 'phil', 'play', 'pluc', 'port',
		 'prox', 'qtek', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony',
		 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'treo', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr',
		 'webc', 'winw', 'winw', 'xda-');
    if(in_array(substr($_SERVER['HTTP_USER_AGENT'], 0, 4), $arr)){
      return TRUE;
    }
    return FALSE;
  }

  if(is_mobile()) {
    $USER['tmpl'] = $CFG['AS']['wap'];
  } else {
    $USER['tmpl'] = $CFG['AS']['web'];
  }

}

?>