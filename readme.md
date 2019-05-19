# Ayima Full Stack Developer Test

## Prerequisites
* Docker Desktop
* Text Editor or IDE such as sublime, PHPStorm or notepad++ etc.
* A terminal
* A decent web browser  (preferably a webkit browser such as Chrome or better yet, vivaldi)

## Installation

First, clone this repository to a desirable location in your file system.

**Docker** 
* CD inside laradock found in the root directory.
* Copy the env-example to .env
* Make sure that the "MYSQL_VERSION" is set to 5.7 in the .env file.
* Inside the laradock folder, execute the following command to start the containers:
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

**note:** If you desire to use a different database or create your own database/database user, please change  the above.

* Create or update the TEST_ENDPOINT configuration in the .env file and set it with the appropriate API endpoint:
```dotenv
TEST_ENDPOINT="example.com/api/endpoint" # Place test endpoint here.
```
* Inside the /laradock folder, execute the following command to enter the workspace container:
```bash
docker-compose exec --user=laradock workspace bash
```
* Run a composer and yarn command to download and install all required dependencies
```bash
#composer update
composer update

#yarn update - optional, but nice especially if you want to make a few changes.
yarn
```

* Generate a Laravel application key by executing the following command:
```bash
php artisan key:generate
```

* Run the artisan migrations to create the required database tables
```bash
php artisan migrate
```

That's it, the application should now be ready and available on http://localhost

## Sample Data (Optional)
As the data that the API pull's in is quite limited as well as identical, optionally additional randomised data can be generated to produce unique daily scores from a given date range.

To do so:
* Open the 'testSeed.php' file found inside the database/seeds directory.
* Change the PHP variables as desired to configure the the date range of data that is generated as well as the randomly generated decimals min and max range.
```php
    // Modify these dates for desired date range
    $startDate = Carbon::parse('2019-01-01');
    $endDate = Carbon::parse('2019-04-31');
    
    // Modify these for desired decimal range.
    $randomNumberStart = 0;
    $randomNumberEnd = 3;
```
* Enable the seed by uncommenting  the following line in the 'DatebaseSeeder.php' file found in the same directory:
```php
    public function run()
    {
        //$this->call(testSeed::class);
    }
    
    # to
    
    public function run()
    {
        $this->call(testSeed::class);
    }
    
```
**note:** Remember to comment the line again after running the artisan command to avoid executing this seed file in the future.
* Enter the workspace container (shown above), and execute the following command:
```bash
php artisan migrate --seed
```
