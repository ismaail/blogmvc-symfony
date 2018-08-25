# Blog MVC - Symfony 4

##### Requirements

- PHP 7.2

<br>

##### Installation

```composer install```

<br>

##### Configuration

Copy ```.env.dist``` to ```.env``` and change database configuration.

<br>

##### Database migrations and seed

Run migrations:

```php bin/console doctrine:migrations:migrate```

Fill the database with data:

```php bin/console doctrine:fixtures:load```

<br>

##### Tests

```php bin/phpunit```
