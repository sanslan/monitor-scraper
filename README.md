## DisplaySpecifications scraper



## Prerequisites

Ensure you have the following installed on your machine before proceeding:

- PHP >= 8.2
- Composer

## Getting Started

### 1. Clone the Repository

To get started, clone the repository to your local machine:

```bash
git clone https://github.com/sanslan/monitor-scraper
cd your-project
```

### 2. Install Dependencies

#### PHP Dependencies

Use Composer to install the PHP dependencies:

```bash
composer install
```

### 3. Set Up Environment

- Copy the example environment file and configure it for your local environment:

```bash
cp .env.example .env
```

- We use https://api.zyte.com as proxy.This service gives 5$ free credit, and it is enough to scrape https://www.displayspecifications.com/en two times. You should update ```PROXY_KEY``` variable in env file which is provided by this service.

### 4. Generate Application Key

Run the following Artisan command to generate a new application key:

```bash
php artisan key:generate
```

Run this command to start scraping

```bash
php artisan app:scrape
```


#### ``monitors.json`` file will be created in ``/storage/app/private/`` directory

