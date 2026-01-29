<!-- omit in toc -->
# Contributing to CustomerReorderPlugin

To contribute you need to:

1. Clone this repository into you development environment and go to the plugin's root directory,

2. Then, from the plugin's root directory, run the following commands:

   ```bash
   composer install
   ```

3. Copy `tests/TestApplication/.env` in `tests/TestApplication/.env.local` and set configuration specific for your development environment.

4. Link node_modules:

    ```bash
    ln -s vendor/sylius/test-application/node_modules node_modules
    ```

5. Run docker (create a `compose.override.yml` if you need to customize services):

    ```bash
    docker-compose up -d
    ```

6. Then, from the plugin's root directory, run the following commands:

    ```bash
    composer test-app-init
    ```

7. Run your local server:

    ```bash
    symfony server:ca:install
    symfony server:start -d
    ```

8. Now at http://localhost:8080/ you have a full Sylius testing application which runs the plugin

### Static checks

  - Coding Standard
    ```bash
    vendor/bin/ecs check --fix
    ```

  - Psalm
  
    ```bash
    vendor/bin/psalm
    ```

  - PHPStan

    ```bash
    vendor/bin/phpstan analyse
    ```

### Testing

After your changes you must ensure that the tests are still passing.

First setup your test database:

```bash
APP_ENV=test vendor/bin/console doctrine:database:create
APP_ENV=test vendor/bin/console doctrine:migrations:migrate -n
# Optionally load data fixtures
APP_ENV=test vendor/bin/console sylius:fixtures:load -n
```

And build assets:

```bash
(cd vendor/sylius/test-application && yarn install)
(cd vendor/sylius/test-application && yarn build)
vendor/bin/console assets:install
```

The current CI suite runs the following tests:

- PHPUnit

    ```bash
    vendor/bin/phpunit
    ```

- PHPSpec

    ```bash
    vendor/bin/phpspec run
    ```

- Behat

    ```bash
    vendor/bin/behat --strict
    ```
