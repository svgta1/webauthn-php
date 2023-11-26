<?php
namespace Svgta\WebAuthn\mds;
use Svgta\WebAuthn\in\chkCert;
use Webauthn\MetadataService\MetadataStatementRepository as MetadataStatementRepositoryInterface;
use Webauthn\MetadataService\StatusReportRepository;
use Webauthn\MetadataService\CertificateChain\CertificateChainValidator;
use Webauthn\MetadataService\Service\MetadataBLOBPayloadEntry;
use Webauthn\MetadataService\Exception\MetadataStatementLoadingException;
use Webauthn\MetadataService\Statement\MetadataStatement;

class mds implements StatusReportRepository, MetadataStatementRepositoryInterface, CertificateChainValidator{
  private static ?MetadataBLOBPayloadEntry $data = null;
  public static string $json;
  public static array $metadata;

  public function __construct(){
  }

  public function metadataBLOB(): string|array{
    if(is_null(self::$data))
      return 'Unknown device';
    return self::$data->jsonSerialize();
  }

  public function metadataStatement(): string|array{
    if(is_null(self::$data))
      return 'Unknown device';
    return self::$data->metadataStatement->jsonSerialize();
  }

  public function jsonToObj(): obj{
    return json_decode(self::$json, FALSE);
  }

  public function jsonToAr(): array{
    return json_decode(self::$json, TRUE);
  }

  public function load(string $json){
    self::$data = MetadataBLOBPayloadEntry::createFromArray(json_decode($json, true));
    self::$json = $json;
  }

  public function findOneByAAGUID(string $aaguid): ?MetadataStatement{
    if(is_null(self::$data))
      return null;
    if(self::$data->aaguid != $aaguid)
      return null;

    $mdata = self::$data->getMetadataStatement();
    return $mdata;
  }

  public function findStatusReportsByAAGUID(string $aaguid): array{
    if(is_null(self::$data))
      return [];
    if(self::$data->aaguid != $aaguid)
      return [];
    return self::$data->getStatusReports();
  }

  public function check(array $untrustedCertificates, array $trustedCertificates): void{
    chkCert::check($untrustedCertificates, $trustedCertificates);
  }
}
