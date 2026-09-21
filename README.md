# Telegram Bot Manager

A robust Telegram Bot Manager built with Laravel, designed for automated service inquiries, lead generation, and dynamic interactions for AmanProjects.

## Features

- **Interactive Service Navigation**: Users can browse pricing, projects, and services directly within Telegram.
- **Lead Generation State Machine**: Captures user details step-by-step (e.g., service needed, budget, additional requirements).
- **Automated PDF Delivery**: Sends brochures and dynamically caches Telegram `file_id` for quick subsequent deliveries.
- **Deep Linking**: Track campaigns and referral links seamlessly (`/start campaign_x`).
- **Broadcast System**: A powerful rate-limited broadcast engine to send mass messages to bot users.
- **AdminLTE Dashboard**: A web-based admin interface to track leads, monitor bot activity, and manage broadcasts.
- **Robust Webhook Handling**: Secure double-secret validation and duplicate payload protection.

## Setup Instructions

### 1. Requirements
- PHP 8.2+
- MySQL or PostgreSQL
- Composer
- Node.js (for frontend compilation if needed)
- A Telegram Bot Token from [BotFather](https://t.me/BotFather)

### 2. Installation
Clone the repository and install dependencies:
```bash
git clone https://github.com/your-repo/telegram-bot-manager.git
cd telegram-bot-manager
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
Update your `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telegram_bot
DB_USERNAME=root
DB_PASSWORD=
```
Run the migrations and seed the initial data (pricing, projects, services):
```bash
php artisan migrate --seed
```

### 4. Telegram Configuration
Add your Telegram bot credentials to your `.env` file:
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_SECRET=your_custom_secret_key_here
TELEGRAM_ADMIN_CHAT_ID=your_personal_chat_id_for_notifications
```

### 5. Setup Webhook
To start receiving messages, you must register your webhook with Telegram. 
The Webhook URL structure is: `https://your-domain.com/api/telegram/webhook/your_custom_secret_key_here`

You can manually set this via a GET request to:
`https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://your-domain.com/api/telegram/webhook/<YOUR_SECRET_KEY>&secret_token=<YOUR_SECRET_KEY>`

### 6. Queue and Scheduler
The broadcast system requires a running queue worker and scheduler.
In production, use Supervisor to keep the queue worker running:
```bash
php artisan queue:work
```
And add this Cron entry to your server to run the scheduler every minute:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Usage

- **User Flow:** Send `/start` to the bot to view the main menu.
- **Admin Dashboard:** Visit `http://your-domain.com/admin` to manage leads and broadcasts.

## License

This project is open-source and licensed under the MIT License.
