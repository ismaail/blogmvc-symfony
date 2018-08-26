# Blog MVC - Symfony 4

![Build Status](https://travis-ci.org/ismaail/BlogMVC-Laravel.svg?branch=master)

##### Requirements

- PHP 7.2

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
