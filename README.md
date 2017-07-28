# MNM Vehicle System BE

## Installation


Install vendor packages

```shell
composer update
```

Rename `.env.example` to `.env` and configure the database

```shell
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Generate laravel app key

```shell
php artisan key:generate
```

Generate JWT key

```shell
php artisan jwt:generate
```

Publish configuration options for laravel/jwt/dingo packages if needed
```shell
php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"
```

Migrate the database

```shell
php artisan migrate
```

Run the server

```shell
php artisan ser
```
