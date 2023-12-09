<?php
namespace Svgta\WebAuthn;
use Svgta\WebAuthn\entities\rp;
use Svgta\WebAuthn\entities\user;
use Svgta\WebAuthn\op\userVerification;
use Svgta\WebAuthn\op\authenticatorAttachment;
use Svgta\WebAuthn\op\residentKey;
use Svgta\WebAuthn\op\attestation;
use Svgta\WebAuthn\op\pubKeyCredParams;
use Svgta\WebAuthn\op\extensions;
use Svgta\WebAuthn\op\excludeCredentials;
use Svgta\WebAuthn\op\allowCredentials;
use Svgta\WebAuthn\mds\fido;
use Svgta\WebAuthn\mds\mds;
use Svgta\Lib\Exception as Exception;
use Svgta\Lib\Utils;
use Svgta\Lib\Keys;
use Svgta\Lib\Session;

class client{
  public rp $rp;
  public user $user;
  public userVerification $userVerification;
  public authenticatorAttachment $authenticatorAttachment;
  public residentKey $residentKey;
  public attestation $attestation;
  public pubKeyCredParams $pubKeyCredParams;
  public extensions $extensions;
  public excludeCredentials $excludeCredentials;
  public allowCredentials $allowCredentials;
  public fido $fido;
  public mds $mds;

  private $session = null;
  private static $timeout = 0;

  const SESSION_NAME = "SvgtaWebAuthn";

  public function __construct(){
    $this->rp = new rp();
    $this->user = new user();
    Session::setSessionName(self::SESSION_NAME);
    $this->session = new Session();

    $this->userVerification = new userVerification();
    $this->authenticatorAttachment = new authenticatorAttachment();
    $this->residentKey = new residentKey();
    $this->attestation = new attestation();
    $this->pubKeyCredParams = new pubKeyCredParams();
    $this->extensions = new extensions();
    $this->excludeCredentials = new excludeCredentials();
    $this->allowCredentials = new allowCredentials();

    $this->fido = new fido();
    $this->mds = new mds();
  }

  public static function setLogLevel(int $level){
    Utils::setLogLevel($level);
  }

  public function setSessionKey(string $key){
    Session::setSessionKey($key);
  }

  public function timeout(int $time){
    self::$timeout = $time;
  }

  public function register(){
    return new register(
      rp: $this->rp,
      user: $this->user,
      session: $this->session,
      userVerification: $this->userVerification,
      authenticatorAttachment: $this->authenticatorAttachment,
      residentKey: $this->residentKey,
      attestation: $this->attestation,
      pubKeyCredParams: $this->pubKeyCredParams,
      timeout: self::$timeout,
      extensions: $this->extensions,
      excludeCredentials: $this->excludeCredentials,
      mds: $this->mds,
    );
  }

  public function authenticate(){
    return new authenticate(
      rp: $this->rp,
      session: $this->session,
      allowCredentials: $this->allowCredentials,
      extensions: $this->extensions,
      timeout: self::$timeout,
      userVerification: $this->userVerification,
      pubKeyCredParams: $this->pubKeyCredParams,
    );
  }
}
