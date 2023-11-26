<?php
namespace Svgta\WebAuthn\in;
use Svgta\OidcLib\OidcException as Exception;
use Cose\Algorithms;

class algo{
  //ECDSA
  final public const COSE_ALGORITHM_ES256 = -7;
  final public const COSE_ALGORITHM_ES256K = -46;
  final public const COSE_ALGORITHM_ES384 = -35;
  final public const COSE_ALGORITHM_ES512 = -36;

  //EDDSA
  final public const COSE_ALGORITHM_EDDSA = -8;
  final public const COSE_ALGORITHM_ED256 = -260;
  final public const COSE_ALGORITHM_ED512 = -261;

  //RSA
  final public const COSE_ALGORITHM_PS256 = -37;
  final public const COSE_ALGORITHM_PS384 = -38;
  final public const COSE_ALGORITHM_PS512 = -39;
  final public const COSE_ALGORITHM_RS256 = -257;
  final public const COSE_ALGORITHM_RS384 = -258;
  final public const COSE_ALGORITHM_RS512 = -259;
  final public const COSE_ALGORITHM_RS1 = -65535;

  private const COSE_ALGORITHM_SIGNING_MAP = [
    self::COSE_ALGORITHM_ES256 => 'ECDSA\\ES256',
    self::COSE_ALGORITHM_ES256K => 'ECDSA\\ES256K',
    self::COSE_ALGORITHM_ES384 => 'ECDSA\\ES384',
    self::COSE_ALGORITHM_ES512 => 'ECDSA\\ES512',

    self::COSE_ALGORITHM_EDDSA => 'EdDSA\\Ed25519',
    self::COSE_ALGORITHM_ED256 => 'EdDSA\\Ed256',
    self::COSE_ALGORITHM_ED512 => 'EdDSA\\Ed512',

    self::COSE_ALGORITHM_PS256 => 'RSA\\PS256',
    self::COSE_ALGORITHM_PS384 => 'RSA\\PS384',
    self::COSE_ALGORITHM_PS512 => 'RSA\\PS512',
    self::COSE_ALGORITHM_RS256 => 'RSA\\RS256',
    self::COSE_ALGORITHM_RS384 => 'RSA\\RS384',
    self::COSE_ALGORITHM_RS512 => 'RSA\\RS512',
    self::COSE_ALGORITHM_RS1 => 'RSA\\RS1',
  ];

  public static function create(int $alg){
    if(!isset(self::COSE_ALGORITHM_SIGNING_MAP[$alg]))
      throw new Exception('Algo ' . $alg . ' not supported');
    $c = '\\Cose\\Algorithm\\Signature\\' . self::COSE_ALGORITHM_SIGNING_MAP[$alg];
    return $c::create();
  }
}
