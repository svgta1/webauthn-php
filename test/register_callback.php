<?php
use Svgta\WebAuthn\client;

$json = '{"id":"BP63qesxI-gmVnrDeQFhMI8jjszzUxf2OzckDemq4byal758BVrG1xgo8qNBIBcE","rawId":"BP63qesxI-gmVnrDeQFhMI8jjszzUxf2OzckDemq4byal758BVrG1xgo8qNBIBcE","response":{"attestationObject":"o2NmbXRmcGFja2VkZ2F0dFN0bXSjY2FsZyZjc2lnWEYwRAIgcAPIOMlk38mNfQr9rUv6vqLmVdMUTqcQmDeE1hph7MUCIFSbBsb0MlR4x2VviLE8Q0sUj_61ecQwybxK99xCwLQ4Y3g1Y4FZAt0wggLZMIIBwaADAgECAgkA35LZxOLtZgowDQYJKoZIhvcNAQELBQAwLjEsMCoGA1UEAxMjWXViaWNvIFUyRiBSb290IENBIFNlcmlhbCA0NTcyMDA2MzEwIBcNMTQwODAxMDAwMDAwWhgPMjA1MDA5MDQwMDAwMDBaMG8xCzAJBgNVBAYTAlNFMRIwEAYDVQQKDAlZdWJpY28gQUIxIjAgBgNVBAsMGUF1dGhlbnRpY2F0b3IgQXR0ZXN0YXRpb24xKDAmBgNVBAMMH1l1YmljbyBVMkYgRUUgU2VyaWFsIDExNTUxMDk1OTkwWTATBgcqhkjOPQIBBggqhkjOPQMBBwNCAAQKGGxuTQpqUopEkJp6JCNocCjUxX7Mtxe6EoC4XC_B5OBhZow8IK7zM1DRlkUjiiw5C_Xf-jT_JVAvRw89QLiIo4GBMH8wEwYKKwYBBAGCxAoNAQQFBAMFBAMwIgYJKwYBBAGCxAoCBBUxLjMuNi4xLjQuMS40MTQ4Mi4xLjcwEwYLKwYBBAGC5RwCAQEEBAMCBDAwIQYLKwYBBAGC5RwBAQQEEgQQL8BXn4ETR-qxFrtajbkgKjAMBgNVHRMBAf8EAjAAMA0GCSqGSIb3DQEBCwUAA4IBAQCCrK8RMKmb0UMn0vj5sEGioEpmhSckIuV7FLC4-DtvFUVmS79VaB6vAVhyKr_O0uSsYzzsCVlWRSSw8uUX3ZcQmLmJFRfs0MVTouRzn53hPa_Q1de4rEo39PLMMO8lywBlLRnbadfaV70anB2O2H1G2A0rO9_R2e-dK2gy1K1bzXQhTOamFB0Wsuk6yyyI9go-ttX2FHGXWQk3O8Z3kCMkVxpXP2Dwe77Re5LItZ-ighC_qMYBIpMAGznv5Xv5yx46yopBMPg6-GaPc97ycRsg3JnoqATuo_dCcZe2tFGzc1wjvJsb4nTCbTv5GW-MSktxX0uVxNt7l-dZTrRlZIwcaGF1dGhEYXRhWJ-nBT8o5g_yBAWBEOgpX0kAkb8swbBYG09PKJqYzBVrUcUAAAABL8BXn4ETR-qxFrtajbkgKgAwBP63qesxI-gmVnrDeQFhMI8jjszzUxf2OzckDemq4byal758BVrG1xgo8qNBIBcEpAEBAycgBiFYIAT-t6nrMSPoJlZ6w3nuO2QgSn1Ip0qeJqF14ijtsmQcoWtjcmVkUHJvdGVjdAI","clientDataJSON":"eyJ0eXBlIjoid2ViYXV0aG4uY3JlYXRlIiwiY2hhbGxlbmdlIjoiNEhaOTFmcTFvWVdGRTROVE5fR3YtQSIsIm9yaWdpbiI6Imh0dHBzOi8vYXV0aC5hNnVxdmo2d3N4cWZuZmt4cXNnZ21ua2lheTVlYmhpbDY3aXNqbWpwcWk3aG54Z3V5cTNqcWhhZC5vbmlvbiIsImNyb3NzT3JpZ2luIjpmYWxzZX0","transports":["nfc","usb"],"publicKeyAlgorithm":-8,"publicKey":"MCowBQYDK2VwAyEABP63qesxI-gmVnrDee47ZCBKfUinSp4moXXiKO2yZBw","authenticatorData":"pwU_KOYP8gQFgRDoKV9JAJG_LMGwWBtPTyiamMwVa1HFAAAAAS_AV5-BE0fqsRa7Wo25ICoAMAT-t6nrMSPoJlZ6w3kBYTCPI47M81MX9js3JA3pquG8mpe-fAVaxtcYKPKjQSAXBKQBAQMnIAYhWCAE_rep6zEj6CZWesN57jtkIEp9SKdKniahdeIo7bJkHKFrY3JlZFByb3RlY3QC"},"type":"public-key","clientExtensionResults":{"credProps":{"rk":true}},"authenticatorAttachment":"cross-platform"}';

require dirname(__FILE__, 2) . '/vendor/autoload.php';
$webauthn = new client();
$webauthn->fido->update();
//print_r($webauthn->fido->get_tmp_mds_aaguid('12ded745-4bed-47d4-abaa-e713f51d6393'));
//print_r($webauthn->fido->nextUpdate());
//foreach( $webauthn->fido->get_tmp_mds() as $k => $v){
//  echo '<pre>'; echo $k . '=>'; print_r($v);
//}
$webauthn->setSessionKey('Supertototafdfdsfoofsd,lk,kl,nfsdlinbinfdsinfsdinillfsdf');
$webauthn->rp->set(
  name: 'My wonderful project',
);

//$aaguid = $webauthn->register()->aaguid(json: $json);
$aaguid = $webauthn->register()->aaguid();
// add extensions
// add mds
if($file = $webauthn->fido->get_tmp_mds_aaguid($aaguid))
  $webauthn->mds->load($file);
//$webauthn->mds->load($webauthn->fido->get_tmp_mds_aaguid('09591fc6-9811-48f7-8f57-b9f23df6413f'));
$val = $webauthn->register()->validate();

$dir = sys_get_temp_dir() . '/Webauthn_test';
$userDir = $dir . '/users';
$userIdDir = $userDir . '/ids';
$userNameDir = $userDir . '/name';
$deviceDir = $dir . '/devices';

$userId = $val['userHandle'];
$userIdFile = $userIdDir . '/' . $userId . '.json';
$userId = json_decode(file_get_contents($userIdFile));
$userName = $userId->name;
$userNameFile = $userNameDir . '/' . base64_encode($userName) . '.json';
$userName = json_decode(file_get_contents($userNameFile), TRUE);
$userName['devices'][] = $val['credentialId'];
file_put_contents($userNameFile, json_encode($userName));

$deviceFile = $deviceDir . '/' . $val['credentialId'] . '.json';
file_put_contents($deviceFile, json_encode($val['jsonData']));


echo json_encode([
  'validation' => $val,
  'Status' => 'Success',
  'verified' => true,
]);
