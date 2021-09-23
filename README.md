# ShishaProject

[![N|Solid](https://img.shields.io/badge/Build-VER1.0-Greeb)](https://github.com/NikolaiBurov/ShishaProject/tree/develop)

### ShishaProject is a private e-commerce project

[ASP.NET part of the project](https://github.com/SvetlinNikolov/Shisha)
## Installation

```bash

Copy the .env.example to root directory and set it up 

 Install composer

 Run: composer install

 Create database - '{ShishaProject}'

  -If project has backup  [sql dump] at "storage/app/Laravel/example.zip" use it
  -If not run php:artisan seed and migrate commands listed below
  
 Run:
    php artisan migrate:refresh --seed
    php artisan db:seed --class=DataTypesTableSeeder
    php artisan db:seed --class=DataRowsTableSeeder
    php artisan db:seed --class=MenusTableSeeder
    php artisan db:seed --class=MenuItemsTableSeeder
    php artisan db:seed --class=RolesTableSeeder
    php artisan db:seed --class=PermissionsTableSeeder
    php artisan db:seed --class=PermissionRoleTableSeeder
    php artisan db:seed --class=PublicUserSeeder

 Backuping the project -
    Run : 
      -php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

 Commands for the backup - 
  --php artisan backup:run, backup:clean, backup:list commands

  The dump will be saved at storage/app/Laravel/test.zip for example
