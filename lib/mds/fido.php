<?php
namespace Svgta\WebAuthn\mds;
use Svgta\WebAuthn\in\chkCert;
use GuzzleHttp\Client;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Webauthn\MetadataService\CertificateChain\CertificateToolbox;
use Webauthn\MetadataService\Exception\MetadataStatementLoadingException;

class fido{
  private const FIDO_MDS = "https://mds3.fidoalliance.org/";
  private const FIDO_CERT = "http://secure.globalsign.com/cacert/root-r3.crt";
  private const TMP_DIR = "Svgta_WebAuthn_MDS_FIDO";
  private const FIDO_MDS_FILENAME = "FIDO.mds";
  private const FIDO_MDS_UPDATEFILE = "nextUpdate.txt";
  private const FIDO_MDS_ENTRIES = "entries";

  private Client $client;
  private string $tmpDir;

  private static array $requestParams = [];

  public function __construct(){
    $this->client = new Client();

    $this->tmpDir = sys_get_temp_dir() . '/' . self::TMP_DIR;
    if(!is_dir($this->tmpDir))
      mkdir($this->tmpDir);
  }

  public function nextUpdate(): string{
    $file = $this->tmpDir . '/' . self::FIDO_MDS_UPDATEFILE;
    if(!is_file($file))
      throw MetadataStatementLoadingException::create(
          'nextUpdate file not found '
      );
    return \file_get_contents($file);
  }

  public function get_tmp_mds_aaguid(string $aaguid): ?string{
    $dir = $this->tmpDir . '/' . self::FIDO_MDS_ENTRIES;
    $file = $dir . '/' . $aaguid;
    if(!is_file($file))
      return null;
    return \file_get_contents($file);
  }

  public function get_tmp_mds(): \Generator{
    $dir = $this->tmpDir . '/' . self::FIDO_MDS_ENTRIES;
    $tdir = \scandir($dir);
    foreach($tdir as $f){
      $file = $dir . '/' . $f;
      if(!is_file($file))
        continue;
      yield $f => \file_get_contents($file);
    }
  }

  public function update(
    ?string $fido_url = null,
    ?string $fido_cert = null,
    array $requestParams = []
  ): void{
    self::$requestParams = $requestParams;
    if(is_null($fido_url))
      $fido_url = self::FIDO_MDS;
    if(is_null($fido_cert))
      $fido_cert = self::FIDO_CERT;

    $tmpMdsFile = $this->tmpDir . '/' . self::FIDO_MDS_FILENAME;
    $tmpUpdateFile = $this->tmpDir . '/' . self::FIDO_MDS_UPDATEFILE;

    $tmpDir = $this->tmpDir . '/' . self::FIDO_MDS_ENTRIES;
    if(!is_dir($tmpDir))
      mkdir($tmpDir);

    if(is_file($tmpUpdateFile)){
      $update = \file_get_contents($tmpUpdateFile);
      if(\strtotime($update) > time())
        return;
    }

    $tdir = \scandir($tmpDir);
    foreach($tdir as $res){
      $f = $this->tmpDir . '/' . $res;
      if(is_file($f))
        unlink($f);
    }

    $response = $this->client->request('GET', $fido_url, self::$requestParams);
    $body = $response->getBody();
    \file_put_contents($tmpMdsFile, (string) $body);

    $cert = [];
    $payload = $this->getPayload(file_get_contents($tmpMdsFile), $cert);
    $this->validateCert($cert, $fido_cert);

    $payloadAr = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
    $nextUpdate = $payloadAr['nextUpdate'];
    \file_put_contents($tmpUpdateFile, $nextUpdate);

    foreach($payloadAr['entries'] as $ent){
      if(!isset($ent['aaguid']))
        continue;

      $fileAaguid = $tmpDir . '/' . $ent['aaguid'];
      \file_put_contents($fileAaguid, json_encode($ent));
    }
  }

  private function getPayload(string $JWT, array &$rootCertificates): string{
    $jws = (new CompactSerializer())->unserialize($JWT);
    $jws->countSignatures() === 1 || throw MetadataStatementLoadingException::create(
        'Invalid response from the metadata service. Only one signature shall be present.'
    );
    $signature = $jws->getSignature(0);
    $payload = $jws->getPayload();
    $payload !== '' || throw MetadataStatementLoadingException::create(
        'Invalid response from the metadata service. The token payload is empty.'
    );
    $header = $signature->getProtectedHeader();
    array_key_exists('alg', $header) || throw MetadataStatementLoadingException::create(
        'The "alg" parameter is missing.'
    );
    array_key_exists('x5c', $header) || throw MetadataStatementLoadingException::create(
        'The "x5c" parameter is missing.'
    );
    is_array($header['x5c']) || throw MetadataStatementLoadingException::create(
        'The "x5c" parameter should be an array.'
    );
    $key = JWKFactory::createFromX5C($header['x5c']);
    $rootCertificates = $header['x5c'];

    $verifier = new JWSVerifier(new AlgorithmManager([
      new ES256(),
      new RS256()
    ]));
    $isValid = $verifier->verifyWithKey($jws, $key, 0);
    $isValid || throw MetadataStatementLoadingException::create(
        'Invalid response from the metadata service. The token signature is invalid.'
    );
    $payload = $jws->getPayload();
    $payload !== null || throw MetadataStatementLoadingException::create(
        'Invalid response from the metadata service. The payload is missing.'
    );

    return $payload;
  }

  private function validateCert(array $jwsCert, string $url_cert){
    $response = $this->client->request('GET', $url_cert, self::$requestParams);
    $body = $response->getBody();
    $fidoCert = CertificateToolbox::convertDERToPEM((string)$body);
    $jwsCert = CertificateToolbox::fixPEMStructures($jwsCert);
    chkCert::check($jwsCert, [$fidoCert]);
  }
}
