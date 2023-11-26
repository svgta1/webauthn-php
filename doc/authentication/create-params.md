# Create params

## To start

This section explain you how to create the json to send to the client browser.

First of all, you need to instantiate the client

```php
<?php
use Svgta\WebAuthn\client;
$webauthn = new client();
```

## To process

As for [registration](../registration/create-params.md#relying-party), you have to give your Relaying Party, ex :&#x20;

```php
$webauthn->rp->set(
    name: 'My wonderful project',
);
```

AllowCredentials : now, give all credentials ID (`credential.id`) of the devices saved for the user :&#x20;

```php
$webauthn->allowCredentials->add(
    id: "O1kSf7QDZGYUcZXpMdRFM...",
    type: "public-key",
);

$webauthn->allowCredentials->add(
    id: "other key",
    type: "public-key",
);
```

Then, you will get the parameters to send with :&#x20;

```php
header('Content-Type: application/json; charset=utf-8');
echo $webauthn->authenticate()->toJson();
```

You will get something like that :&#x20;

```json
{
    "challenge": "35Ph_rnJbr4OZd...",
    "rpId": "myproject.tld",
    "userVerification": "preferred",
    "allowCredentials": [
        {
            "type": "public-key",
            "id": "O1kSf7QDZGYUcZXpMdRFM..."
        }
    ],
    "timeout": 300000
}
```

## Set userVerification

By default, user verification is set to "preferred". You can force it with :&#x20;

```php
// ...
$webauthn->userVerification->required();
// OR
$webauthn->userVerification->discouraged();
// OR (default)
$webauthn->userVerification->preferred();
//...
header('Content-Type: application/json; charset=utf-8');
echo $webauthn->authenticate()->toJson();
```

## Set extensions

You can set extensions like that :&#x20;

```php
$webauthn->extensions->add("credProps", true);
$webauthn->extensions->add("another id", "the value");
```

## Anonymous authentication

You can do an anonymous authentication (without knowing the user before the process). In this case :&#x20;

* In the **registration** phase you needed :&#x20;
  * force the <mark style="color:blue;">userVerification</mark> to <mark style="color:orange;">required</mark>
  * force the <mark style="color:blue;">residentKey</mark> to <mark style="color:orange;">required</mark>
* In **authentication** phase :
  * You can't give any _<mark style="color:blue;">allowCredentials</mark>_
  * You need to force the _<mark style="color:blue;">userVerification</mark>_ to _<mark style="color:orange;">required</mark>_&#x20;

Full authentication example :&#x20;

```php
<?php
use Svgta\WebAuthn\client;
$webauthn = new client();
$webauthn->rp->set(
    name: 'My wonderful project',
);
$webauthn->userVerification->required();
header('Content-Type: application/json; charset=utf-8');
echo $webauthn->authenticate()->toJson();
```

Json sent :&#x20;

```json
{
    "challenge": "6_kySRZQWsVBYCizNa...",
    "rpId": "myproject.tld",
    "userVerification": "required",
    "timeout": 300000
}
```
