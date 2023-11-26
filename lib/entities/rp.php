<?php
namespace Svgta\WebAuthn\entities;
use Webauthn\PublicKeyCredentialRpEntity;

class rp{
  private PublicKeyCredentialRpEntity $rp;
  private string $default_id;

  public function __construct(){
    $this->default_id = $_SERVER['HTTP_HOST'];
  }

  public function id(): string{
    return $this->get()->id;
  }

  public function get_json(): string{
    return json_encode($this->get_array());
  }

  public function get_array(): array{
    return $this->get()->jsonSerialize();
  }
  public function get(): PublicKeyCredentialRpEntity{
    return $this->rp;
  }

  public function setRp_json(string $json){
    $this->thisRp_array(json_decode($json, TRUE));
  }

  public function setRp_array(array $ar = [
    'name' => null,
    'id' => null,
    'icon' => null
  ]){
    if(is_null($ar['id']))
      $ar['id'] = $this->default_id;
    $this->thisRp($ar['id'], $ar['name'], $ar['icon']);
  }

  public function set(
    string $name,
    ?string $id = null,
    ?string $icon = null
  ){
    if(is_null($id))
      $id = $this->default_id;
    $this->rp =  PublicKeyCredentialRpEntity::create(
        name: $name,
        id: $id,
        icon: $icon
    );
  }
}
