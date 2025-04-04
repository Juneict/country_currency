# City and Country Management System

A Laravel-based application that manages cities and countries with currency information. The system integrates with the GeoDB Cities API to fetch and manage geographic data and Authenication.

## Overview

- CRUD operations for cities and countries
- Currency management for each country
- API integration with GeoDB Cities
- Inertia.js + Vue.js frontend
- Command-line tools for data import
- Implement user authentication (signup/login)
- two-step verification via email

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Juneict/country_currency.git
cd code_test
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install and build frontend assets:
```bash
npm install
npm run dev
```

4. Copy environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

## Configuration

1. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Configure MailTrap credentials in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
```

## Usage Instructions

### Database Setup

Run migrations to create necessary tables:

```bash
php artisan migrate
```

### Data Import Commands

1. Import countries with their cities:
```bash
php artisan import:countries-with-cities 
```

2. Import currencies for countries:
```bash
php artisan import:currencies
```

### Web Interface

1. Start the development server:
```bash
php artisan serve
```

### Available Features

- View list of countries with their currencies
- View cities within each country
- Add/Edit/Delete countries and cities
- Import data from GeoDB API
- Manage currency information

## API Integration

The system integrates with the GeoDB Cities API for:
- Fetching country data
- Importing city information
- Getting currency details
