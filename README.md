<div align="center">
<h1>API - Laravel</h1>

[![tests](https://github.com/morphalex90/api/actions/workflows/tests.yml/badge.svg)](https://github.com/morphalex90/api/actions/workflows/tests.yml)
![Static Badge](https://img.shields.io/badge/Laravel-v13.x-red?style=flat&logo=laravel&label=Laravel)
![Static Badge](https://img.shields.io/badge/PHP-8.4-4F5B93?style=flat&logo=php&php=8.4)
</div>

## Locally install & run
    composer setup
    composer run dev
    
then open http://localhost:8000

### Pint
    composer run lint

### Larastan
    ./vendor/bin/phpstan analyse

### Create model with migration, controller, resource and factory
    php artisan make:model Post -mcrf
