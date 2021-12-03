# How to use this repo

- Clone this repo
- copy .env.example to .env
- Run command: `composer install`
- Run command: `php artisan key:generate`

**This repo includes all auth routes with user registration**

## Deply script

cd /home/forge/project-folder

git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader --no-dev

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then

    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan cache:clear
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan view:clear
    $FORGE_PHP artisan horizon:terminate
    $FORGE_PHP artisan route:cache

fi
