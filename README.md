# -- This project is under devolopment --
If you're reading this, it means the project is in a semi-completed state. While it is functional, there is still a need for further clarification and the addition of more features. Please note that this README is not yet complete.

# Masrofi مصروفي

![alt text](assets/image1.png)

Masrofi is a personal expense tracking web application built with Laravel. It's the predecessor of [MasrofiSimple](https://github.com/CuzImAzizx/MasrofiSimple).

## What is Special About Masrofi?
- **Powerd by AI**: Using AI model to analyze SMS transaction messages and extract essential information, such as store names, amounts, and dates for convenient and efficient use.
- **Advanced Searching/Filtering**: Easily query your transactions by name, filter within specific time periods, and search within designated amount ranges for enhanced financial tracking.
- **Valuable Insights**: You can view valuable insights of your transactions.

![alt text](assets/image2.png)

## What Have Been Improved?
Compared to [MasrofiSimple](https://github.com/CuzImAzizx/MasrofiSimple), Masrofi now is using modren technologies, it's a multi-user robust web application for tracking personal expenses. With the ability to manipulate transactions efficiently.

![alt text](assets/image31.png)

## How to use Masrofi?

### Use the online version for free
You can create an account and start using Masrofi **for free** [here](https://google.com). (Not at the moment)

### Self-hosting

If you prefer, you can run and host Masrofi on your own server. (Docker version coming soon)

#### Requirements
Make sure you have the following installed on your machine:
- Git
- PHP
- Composer
- Laravel
- Gemini API key ([Get one for free here](https://ai.google.dev/gemini-api/docs/api-key#:~:text=You%20can%20create%20a%20key%20with%20a%20few%20clicks%20in%20Google%20AI%20Studio))

#### Installation steps

1. **Clone the Project**
```bash
git clone https://github.com/CuzImAzizx/Masrofi
cd Masrofi
```

2. **Install the Dependencies**
```bash
composer install
```

3. **Copy the Environment Configuration**
```bash
cp .env.example .env
```

4. **Generate an Application Key**
```bash
php artisan key:generate
```

5. **Add Your API Key in the `.env` File**
```bash
GEMINI_API_KEY=YOUR_KEY_HERE
```

6. **Database Configuration (Optional)**

You may choose to update the database configuration or keep the default settings, which utilize an SQLite database that requires no additional configuration.

7. **Run Migrations**
```bash
php artisan migrate
```
You may be prompted to create a new SQLite database. Confirm to create the database.

8. **Seed the Database with Initial Data**
```bash
php artisan db:seed
```

9. **Run the Development Server**
```bash
php artisan serve
```
Visit `http://localhost:8000` in your web browser to view the application.





