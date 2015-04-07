#Phalcon REST#
Phalcon REST provides a framework to build your own API upon, core functionalities include:
 * Response formatting using [Fractal's Transformer concept](http://fractal.thephpleague.com/transformers/)  
 (*I got this concept from [a book](https://leanpub.com/build-apis-you-wont-hate) written by Phil Sturgeon*)
 * Authentication sessions using [Json Web Token](http://jwt.io/)
 * Access Control List using [ACL class](http://docs.phalconphp.com/en/latest/reference/acl.html) Phalcon provides
 * Username and password authentication service
 * Google authentication service
 * Documentation generator based on docblock annotations

##Core dependencies##

### Phalcon Framework ###
https://github.com/phalcon/cphalcon

### Fractal ###
For transforming complex data responses  
https://github.com/thephpleague/fractal

### PHPMailer ###
For sending account activation mail  
https://github.com/PHPMailer/PHPMailer

### Firebase JWT ###

For encoding/decoding Json Web Tokens  
https://github.com/firebase/php-jwt

### Google API Client ###

For obtaining user data to authenticate Google users  
https://github.com/google/google-api-php-client

## Install ##
NOTE: You have to install Phalcon first.

Install Phalcon REST via Composer
````bash
$ composer require olivierandriessen/phalcon-rest
````

## Skeleton application ##
You can use the [skeleton application](https://github.com/olivierandriessen/phalcon-rest-skeleton) as base for your project. But you can use it as an example to set it up yourself too.

## Documentation ##
Documentation is on it's way. For now, i recommend you to read the source of the skeleton application.
