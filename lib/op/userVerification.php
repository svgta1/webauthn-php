<?php
namespace Svgta\WebAuthn\op;

class userVerification{
  private static string $var = 'preferred';
  public function __construct(){
  }

  public function get(): string{
    return self::$var;
  }
  public function required(){
    self::$var = 'required';
  }
  public function discouraged(){
    self::$var = 'discouraged';
  }
  public function preferred(){
    self::$var = 'preferred';
  }
}
