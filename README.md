
# Installation Guide

To install the AliExpress Importer package along with Laravel, Sanctum, and Botble, follow the steps below:

1. Make sure you have Laravel, Sanctum, and Botble installed in your project. If not, you can install them using the following commands:

    ```
    composer require laravel/sanctum
    composer require botble/botble
    ```

2. Once you have Laravel, Sanctum, and Botble installed, you can install the AliExpress Importer package by running the following command in your terminal:

    ```
    composer require heptaaurium/aliexpress-importer
    ```

    Make sure you have composer installed on your system before running this command.

3. After the installation is complete, you can proceed with configuring and using the AliExpress Importer package as per your requirements.

4. To publish the package's configuration files, run the following command:

    ```
    php artisan vendor:publish --provider="HeptaAurium\AliExpressImporter\AliExpressImporterServiceProvider" --tag="config"
    ```

5. Next, run the migrations to create the necessary database tables:

    ```
    php artisan migrate
    ```

    This will create the required tables for the AliExpress Importer package in your database.

6. Finally, you can start using the AliExpress Importer package in your Laravel project.
