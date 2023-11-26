<?php
namespace Svgta\WebAuthn\op;
use Webauthn\AuthenticationExtensions\AuthenticationExtension;
use Webauthn\AuthenticationExtensions\AuthenticationExtensionsClientInputs;
use Webauthn\AuthenticationExtensions\ExtensionOutputCheckerHandler;
use Webauthn\AuthenticationExtensions\ExtensionOutputChecker;

class extensions{
  private static array $extensions = [];
  private static array $object = [];

  public function __construct(){
  }

  public function getChecker(): ExtensionOutputCheckerHandler{
    $chk = ExtensionOutputCheckerHandler::create();
    foreach(self::$object as $obj)
      $chk->add($obj);

    return $chk;
  }

  public function addObject(ExtensionOutputChecker $object){
    array_push(self::$object, $object);
  }

  public function get(): AuthenticationExtensionsClientInputs{
    $ret = [];
    foreach(self::$extensions as $k => $v)
      array_push($ret, AuthenticationExtension::create($k, $v));

    return AuthenticationExtensionsClientInputs::create($ret);
  }

  public function add(string $key, $value){
    array_push(self::$extensions, [
      $key => $value
    ]);
  }
}
