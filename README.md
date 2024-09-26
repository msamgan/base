# What is base?

Base is a Laravel 11 based application starter kit that is used to create a new Laravel application. It is a simple and easy to use starter kit which provides a solid foundation to build your application on top of it.

Detailed documentation is available below for features and how to use them.

## Technologies

- [x] Laravel 11
- [x] PHP 8.*
- [x] MySQL 8.*
- [x] Tailwind 3 (CSS)
- [x] React 18 (UI)

## Installation

1. Fork the repository
2. Use the form as a template to create a new repository
3. Clone the new repository and run the following commands:

```bash
composer install
npm install
npm run dev

cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## Usage

```bash
php artisan serve
```

Open your browser and visit `http://localhost:8000`

## Login

- Email: ``sadmin@base.com``
- Password: ``Pass@123!321``
- Role: ``Super Admin``

## Register

- Visit `http://localhost:8000/register`
- Register a new user, the user will be assigned the `Business` role by default.

## Roles and Permissions

The Application comes with 3 roles by default:

- Super Admin
- Business
- Customer

Each role has its own set of permissions. You can create new roles and permissions from the Business panel.

### Super Admin

The Super Admin has access to all the modules and features of the application.
The Super Admin can access the Business panel and can perform all the operations.

### Business

The Business role is for the business owners. The business owner can create new users and assign roles to them. All the new modules created will have all the permissions assigned to the Business role by default. The business owner can assign permissions to the roles.

### Customer

The Customer role is for the customers. As of now, the customer role does not have any permissions assigned to it.

## Making a new Module

To make a new module, run the following command. The command will ask for the module name and will create all the necessary files for the module.

```bash
php artisan app:make:module
```
This command will create the following files:
- Controller
- Model
- Migration
  - for menu management
  - for database table
- Factory
- Seeder
- Request
- Routes
- Views
- Permissions
- Menu
- Policy

Update the migration files as per the requirements and run the following command to migrate the database.

```bash
php artisan migrate
```
