<?php
use Svgta\WebAuthn\client;

require dirname(__FILE__, 2) . '/vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

$webauthn = new client();
$webauthn->setSessionKey('Supertototafdfdsfoofsd,lk,kl,nfsdlinbinfdsinfsdinillfsdf');

$webauthn->rp->set(
  name: 'My wonderful project',
);

$userName = '@svgta';

$dir = sys_get_temp_dir() . '/Webauthn_test';
$userDir = $dir . '/users';
$userIdDir = $userDir . '/ids';
$userNameDir = $userDir . '/name';
$deviceDir = $dir . '/devices';

$userFile = $userNameDir . '/' . base64_encode($userName) . '.json';
$userInfo = json_decode(file_get_contents($userFile));

foreach($userInfo->devices as $device){
  $fileDev = $deviceDir . '/' . $device . '.json';
  $device = json_decode(file_get_contents($fileDev));
  $webauthn->allowCredentials->add(
    id: $device->credential->id,
    type: $device->credential->publicKeyCredentialSource->type,
    transports: $device->credential->publicKeyCredentialSource->transports
  );
}

$webauthn->userVerification->discouraged();
//$webauthn->extensions->add("appId", 'https://auth.meshistoires.fr');
//$webauthn->extensions->add("payment", ['isPayment' => true]);
$ret = json_decode($webauthn->authenticate()->toJson());
$ret->expectedType = 'payment.get';
echo json_encode($ret);
