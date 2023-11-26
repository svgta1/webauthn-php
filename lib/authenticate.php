<?php
namespace Svgta\WebAuthn;
use Svgta\WebAuthn\entities\rp;
use Svgta\OidcLib\OidcSession;
use Svgta\OidcLib\OidcUtils;
use Svgta\OidcLib\OidcException as Exception;
use Svgta\WebAuthn\op\allowCredentials;
use Svgta\WebAuthn\op\extensions;
use Svgta\WebAuthn\op\userVerification;
use Svgta\WebAuthn\op\attestationBuilder;
use Svgta\WebAuthn\op\pubKeyCredParams;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredential;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;

class authenticate{
  private const SESSION_KEY = 'AUTHENTICATION_';
  private const CHALLENGE_LEN = 64;
  private const SESSION_KEY_LABEL = [
    'options' => 'OPTIONS',
  ];

  private attestationBuilder $attB;
  private ?PublicKeyCredentialRequestOptions $options = null;
  private static PublicKeyCredential $publicKeyCredential;

  public function __construct(
    private readonly rp $rp,
    private readonly OidcSession $session,
    private readonly allowCredentials $allowCredentials,
    private readonly extensions $extensions,
    private int $timeout,
    private readonly userVerification $userVerification,
    private pubKeyCredParams $pubKeyCredParams,
  ){
    $optionsKey = self::SESSION_KEY . self::SESSION_KEY_LABEL['options'];
    $options = $this->session->get($optionsKey);
    if(!is_null($options)){
      $this->options = PublicKeyCredentialRequestOptions::createFromString($options);
      $this->attB = new attestationBuilder(
        params: $this->options
      );
    }
  }

  public function response(?string $json = null): array{
    if(is_null($this->options))
      throw new Exception('Register options not found in the session');

    if(is_null($json))
      $json = \file_get_contents("php://input");

    $build = $this->attB->build();
    $publicKeyCredentialLoader = PublicKeyCredentialLoader::create(
        $build['attestationObjectLoader']
    );
    self::$publicKeyCredential = $publicKeyCredentialLoader->load($json);
    if (!self::$publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
      throw new Exception('The web browser response is not well formated');
    }

    return [
      'userHandle' => self::$publicKeyCredential->response->getUserHandle(),
      'credentialId' => self::$publicKeyCredential->id,
      'credentialType' => self::$publicKeyCredential->type
    ];
  }

  public function validate(string $device): string{
    $device = json_decode($device, TRUE);
    $credential = $device['credential'];
    $extension = $this->extensions->getChecker();
    $publicKeyCredentialSource = $credential['publicKeyCredentialSource'];
    $this->pubKeyCredParams->add($credential['alg'], $credential['publicKeyCredentialSource']['type']);

    $authenticatorAssertionResponseValidator = AuthenticatorAssertionResponseValidator::create(
        publicKeyCredentialSourceRepository: null,
        tokenBindingHandler: null,
        extensionOutputCheckerHandler: $extension,
        algorithmManager: $this->attB::coseAlgoManager(params: $this->options)
    );

    $publicKeyCredentialSource = PublicKeyCredentialSource::createFromArray($publicKeyCredentialSource);
    $publicKeyCredentialSource = $authenticatorAssertionResponseValidator->check(
        credentialId: $publicKeyCredentialSource,
        authenticatorAssertionResponse: self::$publicKeyCredential->response,
        publicKeyCredentialRequestOptions: $this->options,
        request: $this->rp->id(),
        userHandle: $device['userHandle']
    );

    $dateTime = new \DateTimeImmutable();
    $json = [
      'userHandle' => $publicKeyCredentialSource->userHandle,
      'date' => [
        'registrationTs' => $device['date']['registrationTs'],
        'registrationDate' => $device['date']['registrationDate'],
        'lastAccesTs' => $dateTime->getTimestamp(),
        'lastAccesDate' =>  $dateTime->format('Y-m-d H:i:s'),
      ],
      'credential' => [
        'alg' => $device['credential']['alg'],
        'id' => $publicKeyCredentialSource->jsonSerialize()['publicKeyCredentialId'],
        'publicKeyCredentialSource' => $publicKeyCredentialSource->jsonSerialize(),
      ],
      'info' => [
        'registration' => [
          'isUserPresent' => $device['info']['registration']['isUserPresent'],
          'isUserVerified' => $device['info']['registration']['isUserVerified'],
          'hasAttestedCredentialData' => $device['info']['registration']['hasAttestedCredentialData'],
          'signCount' => $device['info']['registration']['signCount'],
        ],
        'authentication' => [
          'isUserPresent' => self::$publicKeyCredential->response->authenticatorData->isUserPresent(),
          'isUserVerified' => self::$publicKeyCredential->response->authenticatorData->isUserVerified(),
          'hasAttestedCredentialData' => self::$publicKeyCredential->response->authenticatorData->hasAttestedCredentialData(),
          'signCount' => self::$publicKeyCredential->response->authenticatorData->signCount,
        ],
        'attestedCredentialData' => [
          'metadataBLOB' => $device['info']['attestedCredentialData']['metadataBLOB'],
          'metadataStatement' => $device['info']['attestedCredentialData']['metadataStatement'],
        ]
      ]
    ];
    return json_encode($json);
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
        OidcUtils::log(LOG_ALERT, 'The recommended range for timeout is : 30000 milliseconds to 180000 milliseconds');
    }else{
      if(($this->timeout < 30000) || ($this->timeout > 600000))
        OidcUtils::log(LOG_ALERT, 'The recommended range for timeout is : 30000 milliseconds to 600000 milliseconds');
    }

    $op = PublicKeyCredentialRequestOptions::create(
        challenge: random_bytes(self::CHALLENGE_LEN),
        userVerification: $this->userVerification->get(),
        extensions: $this->extensions->get(),
        rpId: $this->rp->id(),
        allowCredentials: $this->allowCredentials->get(),
        timeout: $this->timeout
    );

    $options_key = self::SESSION_KEY . self::SESSION_KEY_LABEL['options'];
    $this->session->put($options_key, json_encode($op));

    return json_encode($op, JSON_PRETTY_PRINT);
  }
}
