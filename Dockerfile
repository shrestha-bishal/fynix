FROM composer:2 AS test

WORKDIR /app

COPY composer.json phpunit.xml phpstan.neon ./
RUN composer install --no-interaction --prefer-dist --no-progress

COPY src ./src
COPY tests ./tests

CMD ["sh", "-lc", "composer test && composer analyse"]
