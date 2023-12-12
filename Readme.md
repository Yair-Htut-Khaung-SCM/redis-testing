### REDIS WITH PHP DATABASE AND API TESTING

##### if you have not yet install Redis in your window system

```
php -v
```

##### Download with your php version [Redis](https://pecl.php.net/package/redis/5.3.7/windows) thread safe version

#### Check the php.ini path that your system is using

```
php --ini
```
#### and paste the `php_redis.ddl` file to your `php > ext` after extract the php_redis-* .* . *- * . * -ts-vc15-x64

### Setup the database

##### create new database in your local `redisphp` and create `users` table

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL
);
```
##### run the dump users information query
```
DELIMITER //
CREATE PROCEDURE ins()
BEGIN
  DECLARE i INT DEFAULT 1;

  WHILE i <= 300 DO
    INSERT INTO users (firstname, lastname, email)
    VALUES (CONCAT('userfirst', i), CONCAT('userlast', i), CONCAT('useremail', i, '@gmail.com'));
    SET i = i + 1;
  END WHILE;
END //
DELIMITER ;

CALL ins();

```

### Setup the project

#### Run 

```
composer install
```

#### Run for checking database and redis server difference

```
php -S localhost:8080/redisbd.php
```

#### Run for checking RESTApi and redis server difference

```
php -S localhost:8080/redisapi.php
```