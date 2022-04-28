<h1 align="center">Booking App - Sample</h1>

<p align="center">
    <strong>A sample PHP api to creating and listing booking.</strong>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/php->=8.1-blue?colorB=%238892BF" alt="Code Coverage">  
    <img src="https://img.shields.io/badge/coverage-100%25-brightgreen" alt="Code Coverage">   
</p>

## Installation

1. Create `.env` file from `.env.example`
```
cp .env.example .env
```

2. Create `.env.test` file from `.env.test.example`
```
cp .env.test.example .env.test
```

3. Install dependencies
```
composer install
```

4. Run docker image
```
docker-compose up -d
```

5. Update DB schema
```
docker exec -i booking-php-container php bin/console doctrine:schema:update --force
```

```
docker exec -i booking-php-container php bin/console --env=test  doctrine:schema:update --force
```

6. Api is available under
```
http://localhost:8080
```

## Commands

### Run fix (PHP CS Fixer + PHPStan)

```
bin/fix
```

or

```
docker exec -i booking-php-container bin/fix
```

### Run tests

```
bin/test
```

or

```
docker exec -i booking-php-container bin/test
```

## Postman Collection

You can download collection json [here](booking-api-postman-collection.json)
