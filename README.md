# Blog MVC - Symfony

[![Build Status](https://api.travis-ci.com/ismaail/blogmvc-symfony.svg?branch=develop&status=canceled)](https://www.travis-ci.com/github/ismaail/blogmvc-symfony)

##### Requirements

- PHP 8.0

##### Installation

```composer install```

##### Configuration

Copy ```.env.dist``` to ```.env``` and change database configuration.

##### Database migrations and seed

Run migrations:

```php bin/console doctrine:migrations:migrate```

Fill the database with data:

```php bin/console doctrine:fixtures:load```

> Need to clear the cache before sedding the database.

##### Tests

```php bin/phpunit```
