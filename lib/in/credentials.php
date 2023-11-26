<?php
namespace Svgta\WebAuthn\in;
use Webauthn\PublicKeyCredentialDescriptor;

class credentials{
  protected static array $keys = [];
  public function get(): array{
    $ret = [];
    foreach(self::$keys as $key){
      array_push($ret, PublicKeyCredentialDescriptor::createFromArray($key));
    }
    return $ret;
  }

  public function add(string $id, string $type = 'public-key', array $transports = []){
    array_push(self::$keys, [
      'id' => $id,
      'type' => $type,
      'transports' => $transports
    ]);
  }
}
