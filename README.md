#Phalcon REST#
**Note: This project is under development. Develop branch stable for use.**

<a href="http://phalconist.com/olivierandriessen/phalcon-rest" target="_blank">
![Phalcon REST Library](http://phalconist.com/olivierandriessen/phalcon-rest/default.svg)
</a>

*A flexible library, consisting of interchangeable classes made for the modern REST API.*

 * Complex/flexible JSON formatting ([Fractal](https://github.com/thephpleague/fractal), [Build API's You Won't Hate](https://leanpub.com/build-apis-you-wont-hate))
 * Google authentication (optional)
 * Username authentication (optional)
 * Authentication sessions ([JWT](http://jwt.io/))
 * Access control on endpoints ([Phalcon ACL](http://docs.phalconphp.com/en/latest/reference/acl.html))
 * Documentation generator ([Phalcon Annotation Reader](https://docs.phalconphp.com/en/latest/reference/annotations.html))
 * [Postman REST Client](http://getpostman.com) Collection Export generator

## Installing ##
Install using Composer. Not all dependencies are required.
````
{
    "require": {
        "olivierandriessen/phalcon-rest": "dev-develop",
        "league/fractal": "0.12.0",
        "firebase/php-jwt": "2.0.0",
        "phpmailer/phpmailer": "5.2.9",
        "google/apiclient": "1.1.2",
    }
}
````

## Boilerplate ##
For a full implementation of the library, check out the [Boilerplate application](https://github.com/olivierandriessen/phalcon-rest-boilerplate).

## Documentation ##
[Go to the documentation](https://github.com/olivierandriessen/phalcon-rest/wiki/Documentation) for detailed instructions on how to configure each class.

## Contributing ##
Please file issues under GitHub, or submit a pull request if you'd like to directly contribute.

## Changelog ##

*0.0.3* Added Auth/Account/Email, changed user registration flow  
*0.0.2* Major changes  
*0.0.1* Major changes  

###Todo###
* ~~Convert indentation to spaces~~
* DocBlocks
* PSR-2 coding standard
