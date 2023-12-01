# Create Params

## To start

This section explain you how to create the json to send to the client browser.

First of all, you need to instantiate the client

```php
<?php
use Svgta\WebAuthn\client;
$webauthn = new client();
```

## To process

### Relying Party

You must defined the Relying Party (your server). The exemple show all options :

```php
$webauthn->rp->set(
    icon: 'data:image/png;base64,iVBORw0KGgoAAA...',
    name: 'My wonderful project',
    id: 'myproject.tld'
);
```

Explanation :&#x20;

* icon (optional) : must be an image base64 encoded
* name (required) : the name of you app
* id (optional) : hostname of you project. If not set, the library take the hostname of the server

You are maybe on an url like : https://reg.myproject.tld. You can set the id with theses values :&#x20;

* reg.myproject.tld
* myproject.tld

Using _myproject.tld_ for the id can be useful if the authentication process is on https://auth.myproject.tld

### User

You have to defined the user who wants to create a registration.

```php
$webauthn->user->set(
  name: '@svgta',
  id: 'userUniqueId',
  displayName: 'Super Vegeta',
  icon : 'data:image/png;base64,iVBORw0KGgoAAA...'
);
```

&#x20;Explanation :&#x20;

* name (required) : the name of the user. You may avoid to use the user email
* id (optional) : the library will create an uuid for it if not set
* displayName (optional) : if not set, the library use the name
* icon (optional) : must be an image base64 encoded

{% hint style="info" %}
The username can be composed of any displayable characters, including emojis. Username "😝🥰😔" is perfectly valid.

Developers should not add rules that prevent users from choosing the username they want.
{% endhint %}

{% hint style="warning" %}
For privacy reasons, it is not recommended using the e-mail as username.
{% endhint %}

### pubKeyCredParams

You have to list the signg algorithms you want to use for your application.&#x20;

You must give the list in your preference ordrer. Your device will then take the first one that it cans handle with. Example :&#x20;

```php
$webauthn->pubKeyCredParams->add('EDDSA');
$webauthn->pubKeyCredParams->add('ES256');
$webauthn->pubKeyCredParams->add('RS512');
$webauthn->pubKeyCredParams->add('RS256');

// OR, this is the same thing

$webauthn->pubKeyCredParams->add(-8);
$webauthn->pubKeyCredParams->add(-7);
$webauthn->pubKeyCredParams->add(-259);
$webauthn->pubKeyCredParams->add(-257);

// Another method, with all parameters

$webauthn->pubKeyCredParams->add('EDDSA', 'public-key');
$webauthn->pubKeyCredParams->add('ES256', 'public-key');
$webauthn->pubKeyCredParams->add('RS512', 'public-key');
$webauthn->pubKeyCredParams->add('RS256', 'public-key');
```

Then, the parameters are :&#x20;

* alg (required) : the algorithm you want to use (string or integer)
* type (optional) : the type of key. By default _public-key_

To have the list of algorithms supported by the library, use  `pubKeyCredParams::getAlgList()`:&#x20;

```php
echo json_encode($webauthn->pubKeyCredParams::getAlgList(), JSON_PRETTY_PRINT);
```

As result, you will have someting like this :&#x20;

```json
{
    "ES256": -7,
    "ES256K": -46,
    "ES384": -35,
    "ES512": -36,
    "EDDSA": -8,
    "ED256": -260,
    "ED512": -261,
    "PS256": -37,
    "PS384": -38,
    "PS512": -39,
    "RS256": -257,
    "RS384": -258,
    "RS512": -259,
    "RS1": -65535
}
```

{% hint style="info" %}
If no alg is given, the library use the default : ES256 and RS256.
{% endhint %}

### authenticatorSelection

Now, you will defined how you will converse with the device. You have to set :&#x20;

* [userVerification](create-params.md#userverification)
* [residentKey](create-params.md#residentkey)
* [authenticatorAttachment](create-params.md#authenticatorattachment)

You don't have to defined the _requireResidentKey_ ; the library does the job with your inputs.

#### userVerification

Three possible options :&#x20;

* required
* discouraged
* preferred

{% hint style="info" %}
Default value : **preferred**
{% endhint %}

```php
$webauthn->userVerification->required();
// OR
$webauthn->userVerification->discouraged();
// OR
$webauthn->userVerification->preferred();
```

#### residentKey

Three possible options :&#x20;

* required
* discouraged
* preferred

{% hint style="info" %}
Default value : **preferred**
{% endhint %}

```php
$webauthn->residentKey->required();
// OR
$webauthn->residentKey->discouraged();
// OR
$webauthn->residentKey->preferred();
```

#### authenticatorAttachment

Thee possible options :&#x20;

* all
* cross\_platform
* platform

{% hint style="info" %}
Default value : **all**
{% endhint %}

```php
$webauthn->authenticatorAttachment->all();
// OR
$webauthn->authenticatorAttachment->cross_platform();
// OR
$webauthn->authenticatorAttachment->platform();
```

### attestation

Four possible options :&#x20;

* none
* indirect
* direct
* entreprise

{% hint style="info" %}
Default value : **none**
{% endhint %}

```php
$webauthn->attestation->none();
// OR
$webauthn->attestation->indirect();
// OR
$webauthn->attestation->direct();
// OR
$webauthn->attestation->enterprise();
```

### excludeCredentials

You want to avoid some devices. For example, to not authorize a user to use the same devices he used to do a previous registration.

```php
$webauthn->excludeCredentials->add('8EX0yiDLK8V3RZ0Cu-FQAgfs63JOl9vXLICwAyuYpCawD9km-YXsPLVm4y4Axeqn');
$webauthn->excludeCredentials->add('another key');
```

the method allow 3 arguments :&#x20;

* id (required) : the id of the key defined by the library as _publicKeyCredentialId_
* type (optional): by default it's set to _public-key_
* transports (optional) : it's an array. By default the array is empty

### timeout

By default, the timeout is _300000._ You can set another timeout like that :&#x20;

```php
webauthn->timeout('5000');
```

If the timeout is to big or to low, you will get in your server logs a message like that :&#x20;

```log
.... PHP message: Svgta Lib Alert: The recommended range for timeout is : 30000 milliseconds to 600000 milliseconds
```

### Challenge

The challenge is generated by the library. Nothing to do for you.

## To finish

Now, you just need to get the json to send to the client. For example :&#x20;

```php
header('Content-Type: application/json; charset=utf-8');
echo $webauthn->register()->toJson();
```

The session store now all the informations to deal with the response of the client.

You may want the user informations without parsing this json to send to the client. You can get it by two ways, it will be seen in the [next step](callback.md) of registration process.

## Usable Examples

### Use default values&#x20;

code :&#x20;

```php
<?php
use Svgta\WebAuthn\client;
require dirname(__FILE__, 2) . '/vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

$webauthn = new client();

$webauthn->rp->set(
  name: 'My wonderful project',
);
$webauthn->user->set(
  name: '@svgta'
);

echo $webauthn->register()->toJson();
```

Result :&#x20;

```json
{
    "rp": {
        "name": "My wonderful project",
        "id": "myProject.tld"
    },
    "user": {
        "name": "@svgta",
        "id": "N2NiZjk1NzktY...",
        "displayName": "@svgta"
    },
    "challenge": "5BWeonlacolrgA6AsFBGna2DrsrvjPlIFl9EA8ivxuG2O6kG1ozxGeZNvW9zpb820J_eLUhpstD3bWiu2yd6-g",
    "pubKeyCredParams": [
        {
            "type": "public-key",
            "alg": -7
        },
        {
            "type": "public-key",
            "alg": -257
        }
    ],
    "timeout": 300000,
    "excludeCredentials": [
        {
            "type": "public-key",
            "id": "8EX0yiDLK8V3RZ0Cu-FQAgfs63JOl9vXLICwAyuYpCawD9km-YXsPLVm4y4Axeqn"
        }
    ],
    "authenticatorSelection": {
        "requireResidentKey": false,
        "userVerification": "preferred",
        "residentKey": "preferred"
    },
    "attestation": "none"
}
```

### A more secure example

It's may be not what you want. It depends of the security level you need for your application.

Code :&#x20;

```php
<?php
use Svgta\WebAuthn\client;
require dirname(__FILE__, 2) . '/vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

$webauthn = new client();

$webauthn->pubKeyCredParams->add('EDDSA');
$webauthn->pubKeyCredParams->add('RS256');

$webauthn->rp->set(
  name: 'My wonderful project',
);
$webauthn->user->set(
  name: '@svgta'
);

$webauthn->userVerification->required();
$webauthn->authenticatorAttachment->cross_platform();
$webauthn->residentKey->required();
$webauthn->attestation->direct();
$webauthn->excludeCredentials->add('8EX0yiDLK8V3RZ0Cu-FQAgfs63JOl9vXLICwAyuYpCawD9km-YXsPLVm4y4Axeqn');

echo $webauthn->register()->toJson();
```

Result :&#x20;

```json
{
    "rp": {
        "name": "My wonderful project",
        "id": "webauthn.a6uqvj6wsxqfnfkxqsggmnkiay5ebhil67isjmjpqi7hnxguyq3jqhad.onion"
    },
    "user": {
        "name": "@svgta",
        "id": "YzQ4M2IyMDYtMGQ0YS00Yjg3LWIxN2YtMDNiOTZkYmI3Mjdl",
        "displayName": "@svgta"
    },
    "challenge": "V4kSuyHdTcJ6VP8wEF_L-NAkwaMnhj0I6GSqsOQfLnRERmsYc1L-6djVSJvifLg0kBZvrcX-RkOGFwq3K-Uzog",
    "pubKeyCredParams": [
        {
            "type": "public-key",
            "alg": -8
        },
        {
            "type": "public-key",
            "alg": -257
        }
    ],
    "timeout": 300000,
    "excludeCredentials": [
        {
            "type": "public-key",
            "id": "8EX0yiDLK8V3RZ0Cu-FQAgfs63JOl9vXLICwAyuYpCawD9km-YXsPLVm4y4Axeqn"
        }
    ],
    "authenticatorSelection": {
        "requireResidentKey": true,
        "userVerification": "required",
        "residentKey": "required",
        "authenticatorAttachment": "cross-platform"
    },
    "attestation": "direct"
}
```

