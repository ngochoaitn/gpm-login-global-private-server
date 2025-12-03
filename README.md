## Command
```
composer update
composer install
php artisan serve --port=8081
```

## Restart test docker
```
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Docker builder
```
docker buildx create --name mybuilder --use --driver docker-container
docker buildx inspect --bootstrap

docker build -t ngochoaitn/gpm-login-global-private-server:latest .
docker push ngochoaitn/gpm-login-global-private-server:latest
```

## Docker publish
```
docker buildx build --platform linux/amd64,linux/arm64 -t ngochoaitn/gpm-login-global-private-server:latest --push .
docker buildx build --platform linux/amd64,linux/arm64 -t ngochoaitn/gpm-login-global-private-server:php_fpm --push -f ./docker/php-fpm/Dockerfile .
```

## Create file update
- Create zip all folder
- Remove artisan, vendor/composer, vender/autoload.php