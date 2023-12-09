<?php
namespace Svgta\WebAuthn\entities;
use Webauthn\PublicKeyCredentialUserEntity;
use Svgta\Lib\Utils;

class user{
  private PublicKeyCredentialUserEntity $user;
  private string $default_id;

  public function __construct(){
    $this->default_id = Utils::genUUID();
  }

  public function get_json(){
    return json_encode($this->get_array());
  }

  public function get_array(): array{
    return $this->get()->jsonSerialize();
  }

  public function get(): PublicKeyCredentialUserEntity{
    return $this->user;
  }

  public function set(
    string $name,
    ?string $id = null,
    ?string $displayName = null,
    ?string $icon = null,
  ){
    if(is_null($id))
      $id = $this->default_id;
    if(is_null($displayName))
      $displayName = $name;
    $this->user =  PublicKeyCredentialUserEntity::create(
        name: $name,
        id: $id,
        icon: $icon,
        displayName: $displayName
    );
  }
}
