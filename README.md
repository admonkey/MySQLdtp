# Quick Databases for PHP

[![Latest Stable Version][7]][6]
[![Total Downloads][8]][6]
[![License][9]][6]

PHP7 command line console application to create/drop databases & users as well as execute
lists of SQL scripts. This will also generate a PHP file that returns an
instance of [PDO][10].

Currently supports creating MySQL environments with a default character set of
UTF-8 and utf8_unicode_ci collation.

The purge command is for Microsoft SQL Server.

Please report all bugs on the [Github issues page][4].

## Environments

* Development
    * user with all privileges
* Testing Quality Assurance
    * user with all privileges
    * user with only stored procedure execute privileges
* Production
    * user with only stored procedure execute privileges

## Naming Schema

Database names begin with a maximum 7 character name of your choosing, and end
with a randomly generated 5 character identifier. The first letter of the ID
designates its environment `D` (Development), `T` (Test), or `P` (Production).

This allows you to easily spin up alternate environments for development and
testing on the same server without conflict.

Usernames are the same as the database name ending with an `_A` or an `_E` to
designate permissions (All or Execute respectively).
The privileged accounts ending with `_A` (User ALL)
are intended for use with [DDL][1] in development and testing.
The execute only accounts are designated by `_E` (User EXECUTE)
and are intended for use by the application in testing and production.
This follows the [principle of least privilege][3] whereby
all [DML][2] is wrapped within explicit parameterized stored procedures.

The reason names are limited to 7 characters is because up until MySQL 5.7.8
[usernames could only be 16 characters long][11]. Now they can be 32, but this
application currently constrains that for backwards compatibility.

### Examples

* Development
    * Database:         `example_D4JAOb`
    * Privileged  User: `example_D4JAOb_A`
* Test
    * Database:         `example_TzWwAo`
    * Privileged  User: `example_TzWwAo_A`
    * Application User: `example_TzWwAo_E`
* Production
    * Database:         `example_PNITvJ`
    * Application User: `example_PNITvJ_E`

The generated PHP file will return an instance of PDO and looks like this:

```php
<?php
return call_user_func(function(){
    $hostname = 'localhost';
    $database = 'example_D4JAOb';
    $username = 'example_D4JAOb_A';
    $password = '8is+G?Gkg.BNW_}9B5kmjPyr02G~Z2lO';

    $pdo = new PDO("mysql:host=$hostname;
        charset=UTF8;
        dbname=$database",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
});
```
The anonymous function allows for easy inclusion in any PHP script
without conflicting with variable names.

```php
<?php

$pdo = require __DIR__.'/example_D4JAOb_A.pdo.php';

$sql = 'SELECT * FROM Users';

$array = $pdo->query($sql)->fetchAll();
```

----------

## Getting Started

Registered on [packagist][6] for easy [global installation][12] using [composer][5].

    composer global require jpuck/qdbp

Make sure your `$PATH` contains the global bin directory,
because [composer doesn't automatically modify your `$PATH` variable][13].
However, composer will tell you the [location of the global bin directory][12]:

    composer global config bin-dir --absolute

You can then [add that location to your shell profile or rc so that it's always available][14].
For example, if you're running Ubuntu 16.04 with bash, then this might work:

    echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc

After installing, run without any arguments to see a list of commands.

    qdbp

Use the `-h` flag with any command to get help with usage.

    qdbp <command> -h

### Examples

Create a *development* environment with name prefix `dbname` on *localhost*:

    qdbp create -e dev dbname

Create a *production* environment with name prefix `dbname` on a server
located at *mysql.example.com*:

    qdbp create -e prod -H mysql.example.com dbname

Execute an SQL script:

    qdbp execute /path/to/ddl.sql

Execute an SQL script using the generated credentials file:

    qdbp execute -p /path/to/example_D4JAOb_A.pdo.php /path/to/ddl.sql

Execute a list of SQL scripts:

    qdbp execute -p example_D4JAOb_A.pdo.php /path/to/sql.lst

A list of SQL scripts to be executed can contain files in the *same* directory,
*relative* paths outside the directory, or *absolute* paths anywhere on the system.
For example, the contents of `sql.lst` could look like this:

    drop_tables.sql
    ../ddl.sql
    /var/www/project/SQL/stored_procedures.sql


  [1]:https://en.wikipedia.org/wiki/Data_definition_language
  [2]:https://en.wikipedia.org/wiki/Data_manipulation_language
  [3]:https://en.wikipedia.org/wiki/Principle_of_least_privilege
  [4]:https://github.com/jpuck/qdbp/issues
  [5]:https://getcomposer.org/
  [6]:https://packagist.org/packages/jpuck/qdbp
  [7]:https://poser.pugx.org/jpuck/qdbp/v/stable
  [8]:https://poser.pugx.org/jpuck/qdbp/downloads
  [9]:https://poser.pugx.org/jpuck/qdbp/license
  [10]:http://php.net/manual/en/book.pdo.php
  [11]:http://dev.mysql.com/doc/refman/5.7/en/user-names.html
  [12]:https://getcomposer.org/doc/03-cli.md#global
  [13]:https://github.com/composer/composer/issues/4072
  [14]:http://unix.stackexchange.com/a/26059/148062
