# Callback

## To Start

You will receive a json file from the web browser. It's need to be controlled before accepting the registration.

The json file must have this structure :&#x20;

```json
{
    "id": "BP63qesxI-...",
    "rawId": "BP63qesxI-...",
    "response": {
        "attestationObject": "o2NmbXRmcGFja2VkZ2F0dFN0bXSj...",
        "clientDataJSON": "eyJ0eXBlIjoid2ViYXV0aG4uY3J...",
        "transports": ["nfc", "usb"],
        "publicKeyAlgorithm": -8,
        "publicKey": "MCowBQYDK2VwAyE...",
        "authenticatorData": "pwU_KOYP8gQFgRDoKV9JAJG_LMGwWBtPT..."
    },
    "type": "public-key",
    "authenticatorAttachment": "cross-platform"
};
```

The id is the _id_ generated by the authenticator (`crendential.id`).

First of all, instantiate the client and the _RP_ has defined [previously ](create-params.md#relying-party):&#x20;

```php
<?php
use Svgta\WebAuthn\client;
require dirname(__FILE__, 2) . '/vendor/autoload.php';
$webauthn = new client();
$webauthn->rp->set(
  name: 'My wonderful project',
);
```

## Perform the validation

### Do the Validation

This exemple is a base of use :&#x20;

```php
$aaguid = $webauthn->register()->aaguid();
$validation = $webauthn->register()->validate(); //return a json string
```

### Get the response of the authenticator

You will receive an array :&#x20;

* `userHandle` : the user.id
* `credentialId` : the id given by the authentificator
* `jsonData` :  the datas of the process in a json string.

Example of a result of `jsonData` :&#x20;

```json
{
    "userHandle": "86948860-...",
    "date": {
        "registrationTs": 1701002021,
        "registrationDate": "2023-11-26 13:33:41",
        "lastAccesTs": 1701002021,
        "lastAccesDate": "2023-11-26 13:33:41"
    },
    "credential": {
        "alg": -8,
        "id": "M5XWWYD4e6M...",
        "publicKeyCredentialSource": {
            "publicKeyCredentialId": "M5XWWYD4e6M...",
            "type": "public-key",
            "transports": ["nfc", "usb"],
            "attestationType": "basic",
            "trustPath": {
                "type": "Webauthn\\TrustPath\\CertificateTrustPath",
                "x5c": ["-----BEGIN CERTIFICATE-----\n...\n-----END CERTIFICATE-----\n"]
            },
            "aaguid": "2fc0579f-8113-47ea-b116-bb5a8db9202a",
            "credentialPublicKey": "pAEBAycgBiFY...",
            "userHandle": "ODY5NDg...",
            "counter": 4,
            "otherUI": null
        }
    },
    "info": {
        "registration": {
            "isUserPresent": true,
            "isUserVerified": true,
            "hasAttestedCredentialData": true,
            "signCount": 4
        },
        "authentication": [],
        "attestedCredentialData": {
            "metadataBLOB": {
                "aaguid": "2fc0579f-...",
                "attestationCertificateKeyIdentifiers": [],
                "statusReports": [{
                        "status": "FIDO_CERTIFIED_L1",
                        "effectiveDate": "2020-05-12",
                        "certificationDescriptor": "YubiKey ...",
                        "certificateNumber": "FIDO20020190826002",
                        "certificationPolicyVersion": "1.1.1",
                        "certificationRequirementsVersion": "1.3"
                    }, {
                        "status": "FIDO_CERTIFIED",
                        "effectiveDate": "2020-05-12"
                    }
                ],
                "timeOfLastStatusChange": "2020-05-12"
            },
            "metadataStatement": {
                "legalHeader": "Submission of this statement and retrieval and use of this statement indicates acceptance of the appropriate agreement located at https:\/\/fidoalliance.org\/metadata\/metadata-legal-terms\/.",
                "aaguid": "2fc0579f-...",
                "attestationCertificateKeyIdentifiers": [],
                "description": "YubiKey ...",
                "alternativeDescriptions": [],
                "authenticatorVersion": 328706,
                "protocolFamily": "fido2",
                "schema": 3,
                "upv": [{
                        "major": 1,
                        "minor": 0
                    }
                ],
                "authenticationAlgorithms": ["secp256r1_ecdsa_sha256_raw", "ed25519_eddsa_sha512_raw"],
                "publicKeyAlgAndEncodings": ["cose"],
                "attestationTypes": ["basic_full"],
                "userVerificationDetails": [[{
                            "userVerificationMethod": "passcode_external"
                        }, {
                            "userVerificationMethod": "presence_internal",
                            "caDesc": {
                                "base": 64,
                                "minLength": 4,
                                "maxRetries": 8,
                                "blockSlowdown": 0
                            }
                        }
                    ], [{
                            "userVerificationMethod": "passcode_external",
                            "caDesc": {
                                "base": 64,
                                "minLength": 4,
                                "maxRetries": 8,
                                "blockSlowdown": 0
                            }
                        }
                    ], [{
                            "userVerificationMethod": "presence_internal"
                        }
                    ], [{
                            "userVerificationMethod": "none"
                        }
                    ]],
                "keyProtection": ["hardware", "secure_element"],
                "matcherProtection": ["on_chip"],
                "cryptoStrength": 128,
                "attachmentHint": ["external", "wired", "wireless", "nfc"],
                "tcDisplay": [],
                "tcDisplayPNGCharacteristics": [],
                "attestationRootCertificates": ["-----BEGIN CERTIFICATE-----\n...\n-----END CERTIFICATE-----"],
                "ecdaaTrustAnchors": [],
                "icon": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEU...",
                "authenticatorGetInfo": ["basic_full"],
                "supportedExtensions": []
            }
        }
    }
}

```

Without attestation, it's someting like that :&#x20;

```json
{
    "userHandle": "8dc2279f-...",
    "date": {
        "registrationTs": 1701002775,
        "registrationDate": "2023-11-26 13:46:15",
        "lastAccesTs": 1701002775,
        "lastAccesDate": "2023-11-26 13:46:15"
    },
    "credential": {
        "alg": -8,
        "id": "O1kSf7QDZGY...",
        "publicKeyCredentialSource": {
            "publicKeyCredentialId": "O1kSf7QDZGYUc...",
            "type": "public-key",
            "transports": [],
            "attestationType": "none",
            "trustPath": {
                "type": "Webauthn\\TrustPath\\EmptyTrustPath"
            },
            "aaguid": "00000000-0000-0000-0000-000000000000",
            "credentialPublicKey": "pAEBAycgBiFYI...",
            "userHandle": "OGRjMjI3OWYt...",
            "counter": 3,
            "otherUI": null
        }
    },
    "info": {
        "registration": {
            "isUserPresent": true,
            "isUserVerified": true,
            "hasAttestedCredentialData": true,
            "signCount": 3
        },
        "authentication": [],
        "attestedCredentialData": {
            "metadataBLOB": "Unknown device",
            "metadataStatement": "Unknown device"
        }
    }
}

```

Explanation :&#x20;

* `date` : give the date of the registration of the device. The update date is the same as the create date.
* `userHandle` : is the user.id given to make the registration
* `credential` :&#x20;
  * `alg` : the algorithm used by the device to sign the datas. The same algorithm will be used to sign datas for the authentication process
  * `id` : the key.id of the device
  * `publicKeyCredentialSource` : the datas to do the authentication process
* `info` : informations about the user and the device. You can use them to do more verifications or, for example, using the icon of the device in your app.

### What to save in your DataBase

For the authentication process, you will need to give back the jsonData string.&#x20;

You may be need to index in your dataBase for tje authentication process :&#x20;

* `userHandle`
* `credentialId`



{% hint style="info" %}
In the example, you don't give the json response of the web browser. The library take it from `php://input`.

You can give the json like that :&#x20;

<mark style="color:blue;">`$aaguid`</mark>` ``= $webauthn->register()->aaguid(`<mark style="color:red;">`$jsonString`</mark>`);`
{% endhint %}

{% hint style="danger" %}
You will have an exception if you asked in the parameters to have an [attestation](create-params.md#attestation) without MDS json given. You need to give a MDS (MetaData Service) to verify the attestation.&#x20;
{% endhint %}

## MetaData Service

### Basic usage

MDS is needed to verify attestation. The library accept the value in a json string.

Example :&#x20;

```json
{
	"aaguid": "...",
	"metadataStatement": {
		"legalHeader": "Submission ...",
		"aaguid": "...",
		"description": "Pone ...",
		"authenticatorVersion": 1,
		"protocolFamily": "fido2",
		"schema": 3,
		"upv": [{
				"major": 1,
				"minor": 0
			}
		],
		"authenticationAlgorithms": [
			"secp256r1_ecdsa_sha256_raw"
		],
		"publicKeyAlgAndEncodings": [
			"ecc_x962_raw",
			"cose"
		],
		"attestationTypes": [
			"basic_full"
		],
		"userVerificationDetails": [
			[{
					"userVerificationMethod": "passcode_internal"
				}
			],
			[{
					"userVerificationMethod": "fingerprint_internal"
				}
			]
		],
		"keyProtection": [
			"hardware",
			"secure_element"
		],
		"matcherProtection": [
			"software"
		],
		"cryptoStrength": 128,
		"attachmentHint": [
			"nfc",
			"bluetooth"
		],
		"tcDisplay": [],
		"attestationRootCertificates": [
			"MIIBwTCCAWegAwIBAgIUM9zX0yKQj8xg..."
		],
		"icon": "data:image/png;base64,iVBORw0KGgoAAAA...",
		"authenticatorGetInfo": {
			"versions": [
				"FIDO_2_0"
			],
			"extensions": [
				"hmac-secret"
			],
			"aaguid": "09591fc6981148f78f57b9f23df6413f",
			"options": {
				"plat": false,
				"rk": true,
				"clientPin": false,
				"up": true,
				"uv": true
			},
			"transports": [
				"ble",
				"nfc"
			],
			"firmwareVersion": 1
		}
	},
	"statusReports": [{
			"status": "NOT_FIDO_CERTIFIED",
			"effectiveDate": "2022-11-10"
		}
	],
	"timeOfLastStatusChange": "2022-11-10"
}
```

To validate with MDS :&#x20;

```php
$aaguid = $webauthn->register()->aaguid();
$webauthn->mds->load($jsonMDS);
```

### Using MDS from FIDO Alliance

The FIDO Alliance offer for free an API to the MDS3 BLOB ([link to the webpage](https://fidoalliance.org/metadata/)). It's a JWT. You can use this library to get all the datas.

{% hint style="danger" %}
**You must not get the MDS3 BLOB from Fido Alliance every time**. You need to save the datas in your database and call the update when needed. The JWT give the date of the next update : please, use it.
{% endhint %}

The library save the MDS3 BLOB in your temp dir and parse in it the datas.

```php
$webauthn->fido->update();

//Get the next update date :
$date = $webauthn->fido->nextUpdate(); //2023-12-01 -> save it in your database

//Get all MDS in json string format

foreach( $webauthn->fido->get_tmp_mds() as $aaguid => $json){
    //process the save in your dataBase with :
    // key : $aaguid (string)
    // value : $json (string)
}
```

{% hint style="info" %}
The <mark style="color:green;">`update(`</mark>

<mark style="color:green;">`?string $fido_url = null,`</mark>&#x20;

<mark style="color:green;">`?string $fido_cert = null,`</mark>

<mark style="color:green;">`array $requestParams = []`</mark>

<mark style="color:green;">`)`</mark> method can take 3 parameters :&#x20;

* fido\_url : default value -> [https://mds3.fidoalliance.org/](https://mds3.fidoalliance.org/)
* fido\_cert : default value -> [http://secure.globalsign.com/cacert/root-r3.crt](http://secure.globalsign.com/cacert/root-r3.crt)
* requestParams : to add params to the request to get the datas like a proxy. Based on Guzzle, you can see the possibilities [here](https://docs.guzzlephp.org/en/stable/request-options.html).

The fido\_cert is used to verify the JWT obtain bye the fido\_url. If theses urls change in futur, the call must be :&#x20;

```php
$webauthn->fido->update(
    fido_url: "https://newUrlFromFidoAlliance",
    fido_cert: "http://newRootCaUrl"
);

// using a proxy 

$webauthn->fido->update(requestParams: ['proxy' => 'http://localhost:8125']);
//OR
$webauthn->fido->update(requestParams: [
    'proxy' => [
        'http'  => 'http://localhost:8125', // Use this proxy with "http"
        'https' => 'http://localhost:9124', // Use this proxy with "https",
        'no' => ['.mit.edu', 'foo.com']    // Don't use a proxy with these
    ]
]);
```
{% endhint %}

&#x20;If you need to validate attestation with a MDS from FIDO Alliance :&#x20;

* From your database

```php
$aaguid = $webauthn->register()->aaguid();
//...
// Process to get the json string from your dataBase using the $aaguid key
//...
$webauthn->mds->load($jsonString);
$ret = $webauthn->register()->validate(); //return a json string
```

* From your server temp dir after getting the MDS3 Blob

```php
$aaguid = $webauthn->register()->aaguid();
if($mdsJson = $webauthn->fido->get_tmp_mds_aaguid($aaguid))
  $webauthn->mds->load($mdsJson);
$ret = $webauthn->register()->validate();
```

