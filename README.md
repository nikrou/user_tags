# User Tags

Allow visitors to add tag to images

![PHPSTan level](https://img.shields.io/badge/PHPStan-level%201-brightgreen.svg?style=flat)

## Static code analysis

Analysis is made using [PHPStan](https://github.com/phpstan/phpstan) :

```sh
$ composer phpstan
```

The analysis is made with level 1 but the idea is to increase that level and fix more and more possible issues.

## Update code

Code is automatically updated using [Rector](https://packagist.org/packages/rector/rector)

```
$ composer rector
```

Rector enables you to take account of changes to the language from one version to the next and to rewrite the code accordingly.

## Formatting code

Code is automatically formatted using [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) following rules from [config file](./.php-cs-fixer.dist.php).

```sh
$ composer php-cs-fixer
```
