<?php
namespace Svgta\WebAuthn\op;

class authenticatorAttachment{
  private static ?string $var = null;
  public function __construct(){
  }

  public function get(): ?string{
    return self::$var;
  }
  public function all(){
    self::$var = null;
  }
  public function cross_platform(){
    self::$var = 'cross-platform';
  }
  public function platform(){
    self::$var = 'platform';
  }
}
