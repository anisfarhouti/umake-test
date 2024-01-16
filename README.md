# Used libraries
Symfony version: v7

Php version: 8.3

## Recommended IDE Setup

[phpstorm](), [vscode](), ...

## Needed configuration

Symfony cli: See [Symfony cli](https://symfony.com/download).

Composer: See [composer](https://getcomposer.org/).

Mysql, wih pdo_mysql extension enabled

## Project setup ans start

```sh
git clone git@github.com:anisfarhouti/umake-test.git

composer install

php bin/console d:d:c

php bin/console doctrine:migrations:migrate

php bin/console doctrine:fixtures:load

symfony server:start

```

## Api
To test api via postman, call this method with post POST https://127.0.0.1:8000/book

and the payload is a json in this format


```
{
"foodtruck_id": "5",
"reservation_date": "11/09/2024"
}
```



To test API via swagger,visit 
```
{
127.0.0.1:8000/api/doc
}
```