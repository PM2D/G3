<?php
// This file is a part of AugurCMS (g3.pm2d.ru)
// класс для оценок чего-либо v0.1 by DreamDragon aka PM2D
class rating extends mysql {
  // установка ключа (id оценки) и шкалы
  public function SetKey($key, $scale = 5) {
    global $USER;
    $USER['ratekey'] = abs(crc32($key));
    $USER['ratescale'] = intval($scale);
  }
  // получение массива содержащего среднюю оценку и количество оценок
  public function Get() {
    global $USER;
    if(!isset($USER['ratekey'])) throw new Exception('/lib/ratings: ключ не был задан');
    return $this->GetRow('COUNT(*) as `cnt`, AVG(`value`) as `avg`', 'ratings', '`key`='.$USER['ratekey']);
  }
  // получение средней оценки
  public function GetAverage() {
    $arr = $this->Get();
    return $arr['avg'];
  }
  // получение количества оценок по значению оценки
  public function GetByValue($value) {
    global $USER;
    if(!isset($USER['ratekey'])) throw new Exception('/lib/ratings: ключ не был задан');
    return $this->GetField('COUNT(*) as `cnt`', 'ratings', '`key`='.$USER['ratekey'].' AND `value`='.$value);
  }
  // есть ли возможность оценки
  public function IsRateable() {
    global $USER;
    if(!isset($USER['ratekey'])) throw new Exception('/lib/ratings: ключ не был задан');
    if(3 == $USER['id']) return FALSE;
    return !(bool)$this->GetField('`key`', 'ratings', '`key`='.$USER['ratekey'].' AND `uid`='.$USER['id']);
  }
  // сама оценка чего-либо
  public function Rate($value) {
    global $USER;
    if(!isset($USER['ratekey'])) throw new Exception('/lib/ratings: ключ не был задан');
    if($this->GetField('`key`', 'ratings', '`key`='.$USER['ratekey'].' AND `uid`='.$USER['id'])) return FALSE;
    if(0 < $USER['ratescale']) {
      if(1>$value) $value = 1;
      if($USER['ratescale'] < $value) $value = $USER['ratescale'];
    }
    $in['key'] = $USER['ratekey'];
    $in['uid'] = $USER['id'];
    $in['value'] = $value;
    $this->Insert('ratings', $in);
    return TRUE;
  }
  // удаление оценки (вызывать при удалении оцениваемого объекта)
  public function Remove($key) {
    $this->Delete('ratings', '`key`='.abs(crc32($key)));
  }

}

?>