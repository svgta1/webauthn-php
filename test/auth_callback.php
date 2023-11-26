<?php
use Svgta\WebAuthn\client;

require dirname(__FILE__, 2) . '/vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

$webauthn = new client();
$webauthn->setSessionKey('Supertototafdfdsfoofsd,lk,kl,nfsdlinbinfdsinfsdinillfsdf');
$webauthn->rp->set(
  name: 'My wonderful project',
);

$response = $webauthn->authenticate()->response();

$dir = sys_get_temp_dir() . '/Webauthn_test';
$deviceDir = $dir . '/devices';
$fileDev = $deviceDir . '/' . $response['credentialId'] . '.json';
$device = file_get_contents($fileDev);

try{
  $ret = $webauthn->authenticate()->validate(
    device: $device
  );
  echo $ret;
}catch(\Throwable $t){
  print_r($t->getMessage());
  print_r($t);
}
