<?php
namespace Svgta\WebAuthn;
use Svgta\WebAuthn\entities\rp;
use Svgta\WebAuthn\entities\user;
use Svgta\Lib\Session;
use Svgta\Lib\Utils;
use Svgta\Lib\Exception as Exception;
use Svgta\WebAuthn\op\userVerification;
use Svgta\WebAuthn\op\authenticatorAttachment;
use Svgta\WebAuthn\op\residentKey;
use Svgta\WebAuthn\op\attestation;
use Svgta\WebAuthn\op\pubKeyCredParams;
use Svgta\WebAuthn\op\extensions;
use Svgta\WebAuthn\op\excludeCredentials;
use Svgta\WebAuthn\op\attestationBuilder;
use Svgta\WebAuthn\mds\mds;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredential;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticationExtensions\ExtensionOutputCheckerHandler;

class register{
  private const SESSION_KEY = 'REGISTRATION_';
  private const CHALLENGE_LEN = 64;
  private const SESSION_KEY_LABEL = [
    'options' => 'OPTIONS',
  ];

  private attestationBuilder $attB;
  private ?PublicKeyCredentialCreationOptions $options = null;
  private static PublicKeyCredential $publicKeyCredential;
  private static object $authenticatorResponse;

  public function __construct(
    private readonly rp $rp,
    private readonly user $user,
    private readonly Session $session,
    private readonly userVerification $userVerification,
    private readonly authenticatorAttachment $authenticatorAttachment,
    private readonly residentKey $residentKey,
    private readonly attestation $attestation,
    private readonly pubKeyCredParams $pubKeyCredParams,
    private readonly extensions $extensions,
    private readonly excludeCredentials $excludeCredentials,
    private int $timeout,
    private readonly mds $mds
  ){
    $optionsKey = self::SESSION_KEY . self::SESSION_KEY_LABEL['options'];
    $options = $this->session->get($optionsKey);
    if(!is_null($options)){
      $this->options = PublicKeyCredentialCreationOptions::createFromString($options);
      $this->attB = new attestationBuilder(
        params: $this->options
      );
    }
  }

  public function aaguid(?string $json = null): string{
    if(is_null($this->options))
      throw new Exception('Register options not found in the session');

    if(is_null($json))
      $json = \file_get_contents("php://input");

    self::$authenticatorResponse = \json_decode($json);

    $build = $this->attB->build();
    $publicKeyCredentialLoader = PublicKeyCredentialLoader::create(
        $build['attestationObjectLoader']
    );
    self::$publicKeyCredential = $publicKeyCredentialLoader->load($json);
    if (!self::$publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
      throw new Exception('The web browser response is not well formated');
    }

    $authData = self::$publicKeyCredential->response->getAttestationObject()->authData;
    $aaguid = $authData->attestedCredentialData->getAaguid()->jsonSerialize();
    return $aaguid;
  }

  public function validate(): array{
    $extension = $this->extensions->getChecker();

    $authenticatorAttestationResponseValidator = AuthenticatorAttestationResponseValidator::create(
        attestationStatementSupportManager: $this->attB::getAttStmtSupMng(),
        extensionOutputCheckerHandler: $extension,
        publicKeyCredentialSourceRepository: null,
        tokenBindingHandler: null
    );

    $authenticatorAttestationResponseValidator->enableMetadataStatementSupport(
      metadataStatementRepository: $this->mds,
      statusReportRepository: $this->mds,
      certificateChainValidator: $this->mds
    );

    $publicKeyCredentialSource = $authenticatorAttestationResponseValidator->check(
        authenticatorAttestationResponse: self::$publicKeyCredential->response,
        publicKeyCredentialCreationOptions: $this->options,
        request: $this->rp->id()
    );

    $this->session->clear();
    $dateTime = new \DateTimeImmutable();

    $json = json_encode([
      'userHandle' => $publicKeyCredentialSource->userHandle,
      'date' => [
        'registrationTs' => $dateTime->getTimestamp(),
        'registrationDate' => $dateTime->format('Y-m-d H:i:s'),
        'lastAccesTs' => $dateTime->getTimestamp(),
        'lastAccesDate' =>  $dateTime->format('Y-m-d H:i:s'),
      ],
      'credential' => [
        'alg' => self::$authenticatorResponse->response->publicKeyAlgorithm,
        'id' => $publicKeyCredentialSource->jsonSerialize()['publicKeyCredentialId'],
        'publicKeyCredentialSource' => $publicKeyCredentialSource->jsonSerialize(),
      ],
      'info' => [
        'registration' => [
          'isUserPresent' => self::$publicKeyCredential->response->getAttestationObject()->authData->isUserPresent(),
          'isUserVerified' => self::$publicKeyCredential->response->getAttestationObject()->authData->isUserVerified(),
          'hasAttestedCredentialData' => self::$publicKeyCredential->response->getAttestationObject()->authData->hasAttestedCredentialData(),
          'signCount' => self::$publicKeyCredential->response->getAttestationObject()->authData->signCount,
        ],
        'authentication' => [

        ],
        'attestedCredentialData' => [
          'metadataBLOB' => $this->mds->metadataBLOB(),
          'metadataStatement' => $this->mds->metadataStatement(),
        ]
      ]
    ]);

    return [
      'userHandle' => $publicKeyCredentialSource->userHandle,
      'credentialId' => $publicKeyCredentialSource->jsonSerialize()['publicKeyCredentialId'],
      'jsonData' => $json
    ];
  }

  public function toJson(): string{
    if($this->timeout == 0){
      if($this->userVerification->get() === AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_DISCOURAGED)
        $this->timeout = 120000;
      else
        $this->timeout = 300000;
    }
    if($this->userVerification->get() === AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_DISCOURAGED){
      if(($this->timeout < 30000) || ($this->timeout > 180000))
        Utils::log(LOG_ALERT, 'The recommended range for timeout is : 30000 milliseconds to 180000 milliseconds');
    }else{
      if(($this->timeout < 30000) || ($this->timeout > 600000))
        Utils::log(LOG_ALERT, 'The recommended range for timeout is : 30000 milliseconds to 600000 milliseconds');
    }
    $op = PublicKeyCredentialCreationOptions::create(
      rp: $this->rp->get(),
      user: $this->user->get(),
      challenge: random_bytes(self::CHALLENGE_LEN),
      pubKeyCredParams: $this->pubKeyCredParams->get(),
      authenticatorSelection: $this->authenticatorSelection(),
      attestation: $this->attestation->get(),
      excludeCredentials: $this->excludeCredentials->get(),
      timeout: $this->timeout,
      extensions: $this->extensions->get()
    );

    $options_key = self::SESSION_KEY . self::SESSION_KEY_LABEL['options'];
    $this->session->put($options_key, json_encode($op));

    return json_encode($op);
  }

  private function authenticatorSelection(): AuthenticatorSelectionCriteria{
    return AuthenticatorSelectionCriteria::create(
      userVerification: $this->userVerification->get(),
      residentKey: $this->residentKey->get(),
      authenticatorAttachment: $this->authenticatorAttachment->get(),
      requireResidentKey: $this->residentKey->get() == AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_REQUIRED
    );
  }

}
