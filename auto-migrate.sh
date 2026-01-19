#!/bin/bash
# filepath: c:\xampp2\htdocs\document_archive\auto.migrateDB.sh

# Fresh migration
php artisan migrate:fresh

# Database seeds
php artisan db:seed
php artisan db:seed --class=PermissionTableSeeder
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=FeatureSeeder
php artisan db:seed --class=DocumentCategories

# Accounts and Users
php artisan db:seed --class=OfficeCompanyUser
php artisan db:seed --class=CreateAdminUserSeeder



echo "Database migration and seeding completed!"