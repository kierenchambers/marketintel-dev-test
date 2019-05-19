# Ayima Full Stack Developer Test

## Prerequisites
* Docker Desktop
* Text Editor or IDE such as sublime, PHPStorm or notepad++ etc.
* A terminal

## Installation

First, clone this repository to a desirable location in your file system.

**Docker** 
* CD inside laradock found in the root directory.
* Copy the env-example to .env
* Make sure that the "MYSQL_VERSION" is set to 5.7 in the .env file.
* Inside the laradock folder execute to start the containers:
```bash
docker-compose up -d workspace nginx php-worker redis mysql
```

**Laravel Configuration**
* Using an editor of your choice, copy the '.env.example' file to '.env' in the root directory.
* Edit the .env file and set the mysql details as follows:
```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=default
DB_USERNAME=root
DB_PASSWORD=root
```

**note**: If you desire to use a different database or create your own database/database user, please change  the above.

* Create or update the TEST_ENDPOINT configuration in the .env file and set it with the appropriate API endpoint:
```dotenv
TEST_ENDPOINT="example.com/api/endpoint" # Place test endpoint here.
```
* Inside the /laradock folder, execute the following command to enter the workspace container:
```bash
docker-compose exec --user=laradock workspace bash
```
* Run a composer and yarn command to pull in all required dependencies
```bash
#composer update
composer update

#yarn update 
yarn
```

* Run the artisan migrations to create the required database tables
```bash
php artisan migrate
```

That's it, the application should now be ready and available on http://localhost

##Sample Data (Optional)
As the API pull's in limited as well as identical data, if desired optional data can be generated for the daily scores.

To do so:
* Open the 'testSeed.php' file found inside the database/seeds directory.
* Change the PHP variables as desired to configure the the date range of data that is generated as well as the randomly generated decimals min and max range.
* Enter the workspace container (shown above), and execute the following command:
```bash
php artisan migrate --seed
```
