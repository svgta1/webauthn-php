<?php
namespace Svgta\WebAuthn\in;
use Webauthn\MetadataService\Exception\MetadataStatementLoadingException;

class chkCert{
  public static function check(array $untrustedCertificates, array $trustedCertificates): void{
    $unCertInfo = [];
    foreach($untrustedCertificates as $k => $unCert){
      $r = self::certInfo($unCert);
      $unCertInfo[$k] = $r;
    }

    $trCertInfo = [];
    if(count($trustedCertificates) == 0)
      throw MetadataStatementLoadingException::create('No trusted certificate done');

    foreach($trustedCertificates as $trCert){
      $r = self::certInfo($trCert);
      $trCertInfo[$k] = $r;
    }

    self::checkCert($unCertInfo, $trCertInfo);
  }

  private static function checkCert(array $cert, array $trCertAr): void{
    $certsList = array_merge($cert, $trCertAr);
    $ts = new \DateTimeImmutable();
    foreach($certsList as $cert){
      if($cert['valid_from'] > $ts)
        throw MetadataStatementLoadingException::create('Valid from time is in futur ' . implode(', ', $cert['subject']));
      if($cert['valid_to'] < $ts)
        throw MetadataStatementLoadingException::create('Certificate expired ' . implode(', ', $cert['subject']));
    }

    for($i = 0; $i < count($certsList) - 1; $i++){
      if(implode(', ', $certsList[$i]['issuer']) !== implode(', ',  $certsList[$i+1]['subject']))
        throw MetadataStatementLoadingException::create('No valid issuer for ' . implode(', ', $certsList[$i]['subject']));
      if(\openssl_x509_verify($certsList[$i]['cert'], $certsList[$i+1]['public_key']) !== 1)
        throw MetadataStatementLoadingException::create('No valid signature for ' . implode(', ', $certsList[$i]['subject']));
    }

    $ca = end($certsList);
    if(!$ca['isCa'])
      throw MetadataStatementLoadingException::create('Last certificate is not a CA ' . implode(', ', $ca['subject']));
  }

  private static function certInfo(string $cert): array{
    $certInfo = \openssl_x509_parse($cert);
    if(!isset($certInfo['extentions']) && !isset($certInfo['extensions']['basicConstraints'])){
      $isCa = false;
    }else{
      $isCa = false;
      $constraints = explode(',', $certInfo['extensions']['basicConstraints']);
      foreach($constraints as $c){
        if(trim($c) == "CA:TRUE"){
          $isCa = true;
          break;
        }
      }
    }

    $date = new \DateTimeImmutable();
    $dateTo = $date->setTimestamp((integer) $certInfo['validTo_time_t']);
    $dateFrom = $date->setTimestamp((integer) $certInfo['validFrom_time_t']);

    return [
      'subject' => $certInfo['subject'],
      'issuer' => $certInfo['issuer'],
      'isCa' => $isCa,
      'valid_from' => $dateFrom,
      'valid_to' => $dateTo,
      'public_key' => \openssl_pkey_get_public($cert),
      'cert' => $cert,
    ];
  }
}
