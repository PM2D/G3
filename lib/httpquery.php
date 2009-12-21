<?php
// This file is a part of GIII (g3.steelwap.org)

// класс для работы с http (by DreamDragon aka PM2D) version 0.2

final class httpquery {
  private $host;
  private $uri;
  private $res;
  private $query;
  public $responseHeaders = array();
  public $responseData = NULL;
  public $responseCode = 0;
  public $sendHeaders = array();
  public $responseRaw = NULL;

  public function __construct($host, $uri) {
    $this->host = $host;
    $this->uri = $uri;
    if(!ini_get('allow_url_fopen'))
      throw new Exception('Дaннaя функция нeдocтупнa т.к. allow_url_fopen=off.');
    // открываем сокет
    $this->res = fsockopen($host, 80, $en, $es, 20);
    if($en) throw new Exception('Ошибка сокета ['.$en.']: '.$es);
  }

  public function SendQuery($method = 'GET') {
    $this->query =
	$method.' '.$this->uri.' HTTP/1.1'."\r\n".
	'Host: '.$this->host."\r\n".
	'Accept-Encoding: identity'."\r\n".
	'Connection: Close'."\r\n";
    foreach($this->sendHeaders as $name=>$value){
      $this->query .= $name.': '.$value."\r\n";
    }
    $this->query .= "\r\n";
    // отправляем запрос
    fwrite($this->res, $this->query);
  }

  public function GetResponse() {
    // считываем ответ из сокета
    while($this->res && !feof($this->res)) {
       $this->responseRaw .= fread($this->res, 1024);
    }
    if(empty($this->responseRaw)) return FALSE;
    // находим позицию окончания заголовков
    $pos = strpos($this->responseRaw, "\r\n\r\n")+2;
    // отделяем данные от заголовков
    $this->responseData = substr($this->responseRaw, $pos);
    // убираем лишние "метки" из контента
    $this->responseData = preg_replace('!\r\n[0-9a-f\s]{1,5}\r\n!', NULL, $this->responseData);
    $headers = explode("\r\n", substr($this->responseRaw, 0, $pos-4));
    $this->responseCode = substr($headers[0], strpos($headers[0], 'HTTP/1.1')+9, 3);
    $this->responseCode = intval($this->responseCode);
    $cnt = count($headers);
    for($i=0; $i<$cnt; $i++) {
      $pos = strpos($headers[$i], ':');
      $key = substr($headers[$i], 0, $pos);
      $value = substr($headers[$i], $pos+2);
      if($key) $this->responseHeaders[$key] = $value;
    }
    return TRUE;
  }

  public function Close() {
    if($this->res) fclose($this->res);
  }

  public function GetCodeInfo($code = FALSE) {
    if(FALSE === $code) $code =& $this->responseCode;
    switch($code) {
	case 200: return 'OK'; break;
	case 201: return 'Created'; break;
	case 202: return 'Accepted'; break;
	case 203: return 'Non-Authoritative Information'; break;
	case 204: return 'No Content'; break;
	case 205: return 'Reset Content'; break;
	case 206: return 'Partial Content'; break;
	case 300: return 'Multiple Choices'; break;
	case 301: return 'Moved Permanently'; break;
	case 302: return 'Moved Temporarily'; break;
	case 303: return 'See Other'; break;
	case 304: return 'Not Modified'; break;
	case 305: return 'Use Proxy'; break;
	case 400: return 'Bad Request'; break;
	case 401: return 'Unauthorized'; break;
	case 402: return 'Payment Required'; break;
	case 403: return 'Forbidden'; break;
	case 404: return 'Not Found'; break;
	case 405: return 'Method Not Allowed'; break;
	case 406: return 'Not Acceptable'; break;
	case 407: return 'Proxy Authentication Required'; break; 
	case 408: return 'Request Timeout'; break;
	case 409: return 'Conflict'; break;
	case 410: return 'Gone'; break;
	case 411: return 'Length Required'; break; 
	case 412: return 'Precondition Failed'; break;
	case 413: return 'Request Entity Too Large'; break;
	case 414: return 'Request-URI Too Long'; break;
	case 415: return 'Unsupported Media Type'; break;
	case 416: return 'Requested Range Not Satisfiable'; break; 
	case 417: return 'Expectation Failed'; break;
	case 500: return 'Internal Server Error'; break;
	case 501: return 'Not Implemented'; break;
	case 502: return 'Bad Gateway'; break;
	case 503: return 'Service Unavailable'; break;
	case 504: return 'Gateway Timeout'; break;
	case 505: return 'HTTP Version Not Supported'; break;
	default: return 'Unknown Code'; break;
    }
  }

}

?>