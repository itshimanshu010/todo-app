# To-Do List App Using Laravel 9

## Installation Steps

### 1. Clone or Download the Repository
Paste the `todo-app` folder into `xampp/htdocs`.

### 2. Install Dependencies
Open the terminal and navigate to the project folder:

```sh
cd todo-app
composer install
```

### 3. Create a `.env` File
Copy `.env.example` and rename it to `.env`:

```sh
cp .env.example .env
```

Generate the application key:

```sh
php artisan key:generate
```

### 4. Configure the Database
Create a new database in MySQL named `todo_app`.

Update the `.env` file with your database credentials:

```
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
Run the following command to create tables:

```sh
php artisan migrate
```

### 6. Start the Application
Run the application using:

```sh
php artisan serve
```

The app will be accessible at `http://127.0.0.1:8000/`.

---



