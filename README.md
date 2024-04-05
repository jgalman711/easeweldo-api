<p align="center"><a href="https://easeweldo.com" target="_blank"><img src="https://easeweldo.com/es-logo.png" width="300" alt="Laravel Logo"></a></p>

# Easeweldo Api

Welcome to the EaseWeldo API repository! This API serves as the backend for the EaseWeldo payroll system automation application. EaseWeldo aims to streamline payroll management processes by automating tasks such as employee data management, salary calculations, tax deductions, and generating payslips.

## Table of Contents

-   [Installation](#installation)
-   [Usage](#usage)
-   [Contributing](#contributing)
-   [License](#license)

## Installation

To get started with the EaseWeldo API, follow these steps:

### 1. Clone this repository to your local machine:

```bash
git clone https://github.com/jgalman711/easeweldo-api.git
```

### 2. Navigate to the project directory:

```bash
cd easeweldo-api
```

### 3. Setup .env file:

```bash
cp .env.example .env

php artisan key:generate
```

### 4. Configure database connection in .env:

```php
DB_CONNECTION=mysql
DB_HOST=192.168.56.56
DB_PORT=3306
DB_DATABASE=easeweldo-api
DB_USERNAME=homestead
DB_PASSWORD=secret
```

### 5. Migrate and Seed Database

```bash
php artisan migrate

php artisan db:seed
```

### 6. Install dependencies:

```bash
composer install
```

## Usage

To use the EaseWeldo API, you can send HTTP requests to the provided endpoints. Make sure to include any required parameters and headers as specified in the documentation.

You can use tools like Postman, cURL, or any programming language's HTTP client to interact with the API.

## License

The Easeweldo API is licensed under the [MIT license](https://opensource.org/licenses/MIT).
