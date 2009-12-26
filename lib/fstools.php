<?php

final class fstools {

  // создание папки без ошибок, если она уже есть или ее создание невозможно
  static public function make_dir($path, $chmod = 0777){
    // если папка уже есть
    if(is_dir($path)){
      // просто пробуем сhmod
      @chmod($path, $chmod);
      return TRUE;
    }
    // если создание невозможно
    if(!is_writable(dirname($path))) return FALSE;
    // попытка создания
    return mkdir($path, $chmod);
  }

  // создание файла, в общем-то и не нужно, но пусть будет :)
  static public function make_file($path){
    return touch($path);
  }

  // удаляет файл или папку, если она вообще есть
  static public function remove_if_exists($path){
    // если файла/папки и так нет, возвращаем TRUE
    if(!file_exists($path)) return TRUE;
    // если удаление невозможно, возвращаем FALSE
    if(!is_writable($path)) return FALSE;
    // если не папка, то просто удаляем файл
    if(!is_dir($path)){
      return unlink($path);
    // а если папка, то очищаем ее рекурсивно и удаляем
    } else {
      if(substr($path, -1)!='/') $path.='/';
      return fstools::_deldir($path);
    }
  }

  // просто очищает папку не удяляя ее
  static public function clear_dir($path){
    if(!is_dir($path) || !is_writable($path)){
      return FALSE;
    }
    $d = dir($path);
    while($name = $d->read()){
      if($name[0]!='.'){
        if(is_dir($path.$name)){
          if(!fstools::_deldir($path.$name.'/')) return FALSE;
        } else {
          if(!unlink($path.$name)) return FALSE;
        }
      }
    }
    $d->close();
    return TRUE;
  }

  // получение размера папки
  static public function get_dir_size($path){
    if(!is_dir($path)) return 0;
    if(substr($path, -1)!='/') $path.='/';
    return fstools::_dir2size($path);
  }

  // отдача файла с возможностью докачки. алгоритм не мой - в сети нашел и исправил
  static public function download_file($filename, $name = 'default', $mimetype = 'application/octet-stream'){
    if(!file_exists($filename)){
      header('HTTP/1.1 404 Not Found', TRUE, 404);
      throw new Exception('Файл '.$filename.' не найден.');
      exit;
    };
    $filesize = filesize($filename);
    $from = $to = 0;
    $cr = NULL;
    if(isset($_SERVER['HTTP_RANGE'])) {
      $range = substr($_SERVER['HTTP_RANGE'], strpos($_SERVER['HTTP_RANGE'], '=') + 1);
      $from = strtok($range, '-');
      $to = strtok('/');
      if(0<$to) {
        $to++;
        $to -= $from;
      }
      header('HTTP/1.1 206 Partial Content', TRUE, 206);
      $cr = $to ? $to.'/'.($to+1) : ($filesize-1).'/'.($filesize);
      $cr = 'Content-Range: bytes '.$from.'-'.$cr;
    } else {
      header('HTTP/1.1 200 Ok', TRUE, 200);
    }
    $etag = md5($filename);
    $etag = substr($etag, 0, 8).'-'.substr($etag, 8, 7).'-'.substr($etag, 15, 8);
    header('ETag: "'.$etag.'"');
    header('Accept-Ranges: bytes');
    header('Content-Length: '.($filesize - ($to + $from)));
    if($cr) header($cr);
    header('Connection: close');
    header('Content-Type: '.$mimetype);
    header('Last-Modified: '.gmdate('r', filemtime($filename)));
    $f = fopen($filename, 'r');
    header('Content-Disposition: ; filename="'.$name.'";');
    if($from) fseek($f, $from, SEEK_SET);
    if(!isset($to) or empty($to)) {
      $size = $filesize-$from;
    } else {
      $size = $to;
    }
    $downloaded = 0;
    while(!feof($f) and !connection_status() and ($downloaded<$size)) {
      echo fread($f, 512000);
      $downloaded += 512000;
      flush();
    }
    fclose($f);
  }

  static public function save_ini($path, array $data) {
    $f = fopen($path, 'w');
    if(FALSE===$f) throw new Exception('Невозможно открыть для записи файл '.$path);
    foreach($data as $key1=>$value1) {
      if(is_array($value1)) {
        fwrite($f, '['.$key1."]\n");
        foreach($value as $key2 => $value2) {
          if(!$value2) {
            $value2 = 'false';
          } elseif(is_bool($value2)) {
            $value2 = 'true';
          } elseif(!is_numeric($value2)) {
            $value2 = '"'.$value2.'"';
          }
          fwrite($f, $key2.'='.$value2."\n");
        }
        fwrite($f, "\n");
      } else {
        if(!$value1) {
          $value1 = 'false';
        } elseif(is_bool($value1)) {
          $value1 = 'true';
        } elseif(!is_numeric($value1)) {
          $value1 = '"'.$value1.'"';
        }
        fwrite($f, $key1.'='.$value1."\n");
      }
    }
    fclose($f);
  }

  static private function _dir2size($dir){
    $size = 0;
    $d = dir($dir);
    while($name = $d->read()){
      if($name[0]!='.'){
        if(is_dir($dir.$name)){
          $size += fstools::dir2size($dir.$name.'/');
        } else {
          $size += filesize($dir.$name);
        }
      }
    }
    $d->close();
    return $size;
  }

  static private function _deldir($dir){
    $d = dir($dir);
    if(!$d) return FALSE;
    while($name = $d->read()){
      if($name[0]!='.'){
        if(is_dir($dir.$name) && fstools::_deldir($dir.$name.'/')){
          return FALSE;
        } elseif(!unlink($dir.$name)) {
          return FALSE;
        }
      }
    }
    $d->close();
    return rmdir($dir);
  }

}

?>