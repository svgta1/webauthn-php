<?php
namespace Svgta\WebAuthn\op;
use Svgta\WebAuthn\in\algo;
use Svgta\Lib\Exception as Exception;
use Webauthn\PublicKeyCredentialParameters;

class pubKeyCredParams extends algo{
  private const DEFAULT_ALG = [
    ['alg' => -7, 'type' => "public-key"],
    ['alg' => -257, 'type' => "public-key"],
  ];
  public static function getAlgList(){
    $reflectionClass = new \ReflectionClass(__CLASS__);
    $constants = $reflectionClass->getConstants();
    $ret = [];
    foreach($constants as $k => $v){
      if(!preg_match('/^COSE_ALGORITHM_/', $k))
        continue;
      if(!is_int($v))
        continue;

      $k = str_replace("COSE_ALGORITHM_", '', $k);
      $ret[$k] = $v;
    }
    return $ret;
  }

  private static array $alg = [];

  public function __construct(){
  }

  public static function getObj(): array{
    return json_decode(json_encode(self::getArray()), FALSE);
  }

  public static function getArray(): array{
    if(self::$alg == [])
      return self::DEFAULT_ALG;
    return self::$alg;
  }

  public function get(): array{
    $ret = [];
    foreach(self::getArray() as $ar){
      array_push($ret, PublicKeyCredentialParameters::create(
        type: $ar['type'],
        alg: $ar['alg']
      ));
    }
    return $ret;
  }

  public function add(int|string $alg, string $type = "public-key"){
    $list = self::getAlgList();
    $key = null;
    if(is_int($alg)){
      if($key = array_search($alg, $list, true) === FALSE)
        throw new Exception('Bad alg value');
      $key = $alg;
    }else{
      if(!isset($list[$alg]))
        throw new Exception('Bad alg value');
      $key = $list[$alg];
    }
    array_push(self::$alg, ['alg' => $key, 'type' => $type]);
  }
}
