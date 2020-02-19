# Blog MVC - Symfony 4

[![Build Status](https://travis-ci.org/ismaail/blogmvc-sf4.svg?branch=develop)](https://travis-ci.org/ismaail/blogmvc-sf4)

##### Requirements

- PHP 7.4

##### Installation

```composer install```

##### Configuration

Copy ```.env.dist``` to ```.env``` and change database configuration.

##### Database migrations and seed

Run migrations:

```php bin/console doctrine:migrations:migrate```

Fill the database with data:

```php bin/console doctrine:fixtures:load```

##### Tests

```php bin/phpunit```
