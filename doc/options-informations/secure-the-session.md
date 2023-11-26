# Secure the session

The session of the this library can be encrypted. To do that, you have to defined a strong key to encrypt. To use it, you need to instantiate the client and then give a session key :&#x20;

```php
<?php
use Svgta\WebAuthn\client;
$webauthn = new client();
$webauthn->setSessionKey('an amazing key');
//...
//do what you need
//...
```
