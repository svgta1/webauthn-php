<?php
namespace Svgta\WebAuthn\op;

class attestation{
  private static string $var = 'none';
  public function __construct(){
  }

  public function get(): string{
    return self::$var;
  }
  public function none(){
    self::$var = 'none';
  }
  public function indirect(){
    self::$var = 'indirect';
  }
  public function direct(){
    self::$var = 'direct';
  }
  public function enterprise(){
    self::$var = 'enterprise';
  }
}
