<?php
use Svgta\WebAuthn\client;
//use Svgta\WebAuthn\op\pubKeyCredParams;

require dirname(__FILE__, 2) . '/vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

//$alg = pubKeyCredParams::getAlgList();

$webauthn = new client();
$webauthn->setSessionKey('Supertototafdfdsfoofsd,lk,kl,nfsdlinbinfdsinfsdinillfsdf');
//$alg = $webauthn->pubKeyCredParams::getAlgList();

$webauthn->pubKeyCredParams->add('EDDSA');
//$webauthn->pubKeyCredParams->add('ES256');
//$webauthn->pubKeyCredParams->add('RS512');
//$webauthn->pubKeyCredParams->add('RS256');

$webauthn->rp->set(
  name: 'My wonderful project',
);
$webauthn->user->set(
  name: '@svgta'
);

$dir = sys_get_temp_dir() . '/Webauthn_test';
if(!is_dir($dir))
  mkdir($dir);
$userDir = $dir . '/users';
if(!is_dir($userDir))
  mkdir($userDir);
$userIdDir = $userDir . '/ids';
if(!is_dir($userIdDir))
  mkdir($userIdDir);
$userNameDir = $userDir . '/name';
if(!is_dir($userNameDir))
  mkdir($userNameDir);
$deviceDir = $dir . '/devices';
if(!is_dir($deviceDir))
  mkdir($deviceDir);

$userInfo = $webauthn->user->get_array();
$userIdFile = $userIdDir . '/' . $webauthn->user->get()->id . '.json';
if(!is_file($userIdFile)){
  $contents = $userInfo;
  file_put_contents($userIdFile, json_encode($contents));
}
$userNameFile = $userNameDir . '/'. base64_encode($userInfo['name']) . '.json';
if(!is_file($userNameFile)){
  $contents = [
    'ids' => [$webauthn->user->get()->id],
    'name' => $userInfo['name'],
    'icon' => isset($userInfo['icon']) ? $userInfo['icon'] : null,
    'displayName' => $userInfo['displayName'],
    'devices' => [],
  ];
}else{
  $contents = json_decode(file_get_contents($userNameFile), TRUE);
  $contents['ids'][] = $webauthn->user->get()->id;
}
file_put_contents($userNameFile, json_encode($contents));

$webauthn->userVerification->required();
$webauthn->authenticatorAttachment->cross_platform();
$webauthn->residentKey->required();
$webauthn->attestation->none();
//$webauthn->timeout('5000');
//$webauthn->extensions->add("credProps", true);
foreach($contents['devices'] as $device){
  $webauthn->excludeCredentials->add($device);
}


//$webauthn->extensions->add("payment", ['isPayment' => true]);
echo $webauthn->register()->toJson();
//echo json_encode($webauthn->pubKeyCredParams::getAlgList());
//print_r($webauthn->pubKeyCredParams::create(-1));
