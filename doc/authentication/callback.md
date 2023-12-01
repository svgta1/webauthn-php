# Callback

## To Start

You will receive a json file from the web browser. It's need to be controlled before accepting the authentication.

The json file must have this structure :&#x20;

```json
{
    "id": "Z5vMMcATMvm7...",
    "rawId": "Z5vMMcATMvm7...",
    "response": {
        "authenticatorData": "3Wg_Q_XHzhbYlY20S...",
        "clientDataJSON": "eyJ0eXBlIj...",
        "signature": "StUrFYQqbBXKxLFXoKAOh7v...",
        "userHandle": "ZjMwMjZmZTctMmExNi00..."
    },
    "type": "public-key"
    "authenticatorAttachment": "cross-platform"
}

```

The id is the _id_ of the authenticator (`credential.id`), the same you have saved after registration process.

First of all, instantiate the client and the _RP_ has defined [previously ](create-params.md):&#x20;

```php
<?php
use Svgta\WebAuthn\client;
require dirname(__FILE__, 2) . '/vendor/autoload.php';
$webauthn = new client();
$webauthn->rp->set(
  name: 'My wonderful project',
);
```

## Process

Now, instantiate the authentication process :&#x20;

```php
$response = $webauthn->authenticate()->response();
```

{% hint style="info" %}
<mark style="color:blue;">`authenticate::response(?string $json = null)`</mark>` ``:`&#x20;

<mark style="color:blue;">`$json`</mark>` ``:` must be the return of the client browser

If not set, the library force `$json` with `file_get_contents("php://input");`
{% endhint %}

`$response` is an array :&#x20;

* userHandle : the user.id saved in the device
* credentialId : the id you get from the device after registration
* credentialType : "public-key" in this contextode

You need to verify that the couple _userHandle_-_credentialId_ is the same that you have in your database.

## Validation

At this step, you need to give the jsonData saved in your database for the `credentialId` return.&#x20;

```php
$device = ... //your process to get the jsonData string from your dataBase
$validation = $webauthn->authenticate()->validate(
    device: $device
);
```

`$validation` is a json string. You can save it in your dataBase to replace the previous jsonData from the registration. Example of the result :&#x20;

```json
{
    "userHandle": "f3026fe7-...",
    "date": {
        "registrationTs": 1701011778,
        "registrationDate": "2023-11-26 16:16:18",
        "lastAccesTs": 1701014097,
        "lastAccesDate": "2023-11-26 16:54:57"
    },
    "credential": {
        "alg": -8,
        "id": "Z5vMMcATMvm7Y..",
        "publicKeyCredentialSource": {
            "publicKeyCredentialId": "Z5vMMcATMvm7Y...",
            "type": "public-key",
            "transports": [],
            "attestationType": "none",
            "trustPath": {
                "type": "Webauthn\\TrustPath\\EmptyTrustPath"
            },
            "aaguid": "00000000-0000-0000-0000-000000000000",
            "credentialPublicKey": "pAEBAycgBiFYIG..",
            "userHandle": "ZjMwMjZmZT...",
            "counter": 38,
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
        "authentication": {
            "isUserPresent": true,
            "isUserVerified": true,
            "hasAttestedCredentialData": false,
            "signCount": 38
        },
        "attestedCredentialData": {
            "metadataBLOB": "Unknown device",
            "metadataStatement": "Unknown device"
        }
    }
}
```
