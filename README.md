#Phalcon REST Library#
![Phalcon REST Library](http://phalconist.com/olivierandriessen/phalcon-rest/default.svg)


Status: In Development.

Phalcon REST Library provides multiple classes to build your RESTful API with, functionalities include:
 * Complex/flexible Json formatting
 * Authenicate users with [Json Web Token](http://jwt.io/)
 * Access Control List ([Phalcon ACL](http://docs.phalconphp.com/en/latest/reference/acl.html))
 * Google and Username Authentication (both optional)
 * Documentation generator ([Phalcon Annotation Reader](https://docs.phalconphp.com/en/latest/reference/annotations.html))
 * [Postman REST Client](http://getpostman.com) Collection Export generator

##Composer Dependencies##
This library provides classes based on the following dependencies.
Not all dependencies are 

### Phalcon Framework ###
https://github.com/phalcon/cphalcon

This library is written solely for Phalcon.

### Fractal ###
https://github.com/thephpleague/fractal

For transforming complex data responses using [Fractal's Transformer concept](http://fractal.thephpleague.com/transformers/)  
(*I got this concept from [a book](https://leanpub.com/build-apis-you-wont-hate) written by Phil Sturgeon*)

### PHPMailer ###
https://github.com/PHPMailer/PHPMailer

Send mails (user activation mail)

### Firebase JWT ###
https://github.com/firebase/php-jwt

For encoding/decoding Json Web Tokens  

### Google API Client ###
https://github.com/google/google-api-php-client

For obtaining user data to authenticate/register Google users  

## Install ##
NOTE: You have to install Phalcon first.

Install Phalcon REST via Composer
````bash
$ composer require olivierandriessen/phalcon-rest
````

## Boilerplate application ##
You can use the [Boilerplate application](https://github.com/olivierandriessen/phalcon-rest-boilerplate) as base for your project. But you can use it as an example to set it up yourself too.

## Documentation ##
Documentation per class is on it's way. I recommend you to checkout the Boilerplate application to see how each class can be integrated.
