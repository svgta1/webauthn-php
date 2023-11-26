<?php
namespace Svgta\WebAuthn\op;
use Svgta\WebAuthn\in\algo;
use Svgta\WebAuthn\op\pubKeyCredParams;
use Svgta\OidcLib\OidcException as Exception;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\AttestationStatement\AndroidSafetyNetAttestationStatementSupport;
use Webauthn\AttestationStatement\AndroidKeyAttestationStatementSupport;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\FidoU2FAttestationStatementSupport;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AttestationStatement\PackedAttestationStatementSupport;
use Webauthn\AttestationStatement\TPMAttestationStatementSupport;
use Webauthn\AttestationStatement\AppleAttestationStatementSupport;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Symfony\Component\Clock\NativeClock;
use Cose\Algorithm\Manager;

class attestationBuilder{
  private static array $buildAr = [];
  private static Manager $alg;

  public function __construct(
    private readonly PublicKeyCredentialCreationOptions|PublicKeyCredentialRequestOptions $params
  ){
  }

  public static function coseAlgoManager($params): Manager{
    $self = new self(params: $params);
    $self->build();
    return self::$alg;
  }

  public static function getAttObjLoader(){
    return self::$buildAr['attestationObjectLoader'];
  }

  public static function getAttStmtSupMng(){
    return self::$buildAr['attestationStatementSupportManager'];
  }

  public function build(): array{
    $attStmt = $this->attStmt();
    self::$alg = $this->algManager();
    $attStmt->add(PackedAttestationStatementSupport::create(self::$alg));
    $attestationObjectLoader = AttestationObjectLoader::create($attStmt);
    self::$buildAr = [
      'attestationObjectLoader' => $attestationObjectLoader,
      'attestationStatementSupportManager' => $attStmt,
    ];
    return self::$buildAr;
  }

  private function algManager(): Manager{
    $coseAlgorithmManager = Manager::create();
    $pubKeys = isset($this->params->pubKeyCredParams) ? $this->params->pubKeyCredParams : pubKeyCredParams::getObj();
    foreach($pubKeys as $key){
      if($key->type !== 'public-key')
        continue;
      $alg = algo::create($key->alg);
      $coseAlgorithmManager->add($alg);
    }
    return $coseAlgorithmManager;
  }

  private function attStmt(): attestationStatementSupportManager{
    $clock = new NativeClock();
    $attestationStatementSupportManager = AttestationStatementSupportManager::create();
    $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());
    $attestationStatementSupportManager->add(FidoU2FAttestationStatementSupport::create());
    $attestationStatementSupportManager->add(AppleAttestationStatementSupport::create());
    $androidSafetyNetAttestationStatementSupport = AndroidSafetyNetAttestationStatementSupport::create();
    $attestationStatementSupportManager->add($androidSafetyNetAttestationStatementSupport);
    $attestationStatementSupportManager->add(AndroidKeyAttestationStatementSupport::create());
    $attestationStatementSupportManager->add(TPMAttestationStatementSupport::create($clock));

    return $attestationStatementSupportManager;
  }
}
