# Generate MySQL Environments

[![Latest Stable Version][7]][6]
[![Total Downloads][8]][6]
[![License][9]][6]

PHP console application to create/drop databases & users as well as execute
lists of SQL scripts.

The default character set is UTF-8 and collation is utf8_unicode_ci.

* Development
    * user with all privileges (Bash & PHP credentials file generated)
    * 16 character password
* Testing Quality Assurance
    * user with all privileges (Bash credentials file generated)
    * user with only stored procedure execute privileges (PHP credentials file generated)
    * 16 character matching passwords
* Production
    * user with only stored procedure execute privileges (PHP credentials file generated)
    * 32 character password

## Requirements

* PHP >= 7
* mysql  Ver 14.14 Distrib 5.5.49, for debian-linux-gnu (x86_64) using readline 6.3

Please report all bugs on the [Github issues page][4].

## Naming Schema

Database names begin with a maximum 7 character name of your choosing, and end
with a randomly generated 5 character identifier. The first letter of the ID
designates its environment `D` (Development), `T` (Test), or `P` (Production).

This allows you to easily spin up alternate environments for development and
testing on the same server without conflict.

Usernames are the same as the database name ending with an `_A` or an `_E` to
designate perissions All or Execute respectively.
The privileged accounts ending with `_A` (User ALL)
and are intended for use with [DDL][1] in development and testing.
The execute only accounts are designated by `_E` (User EXECUTE)
and are intended for use by the application in testing and production.
This follows the [principle of least privilege][3] whereby
all [DML][2] is wrapped within explicit parameterized stored procedures.

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

----------

## Getting Started

It's recommended to install this from [packagist][6] into your project as a dependency using [composer][5].

    php composer.phar require jpuck/mydtp

There are two scripts:
one for creating the database and users,
and one for executing SQL scripts, such as [DDL][2].

* `create_db_users.bash`

        environment: -e <dev|test|prod>
        database name (limit 7 characters): -n <name>
        database server (optional default localhost): -s <hostname>

   This will generate one or both of `credentials.local.bash` and `credentials.local.inc.php`

* `exec_sql.bash`

        list of SQL files: -l </path/to/SQL.lst>
        credentials file  (optional prompt): -c </path/to/credentials.local.bash>

   This will execute SQL scripts listed in order from the directory in which the list is located.
   The list may contain relative or absolute paths to SQL files.

### Examples

Create a *development* environment with name prefix `dbname` on *localhost*:

    ./vendor/jpuck/mydtp/create_db_users.bash -e dev -n dbname

Create a *production* environment with name prefix `dbname` on a *server* located at `mysql.example.com`

    ./vendor/jpuck/mydtp/create_db_users.bash -e prod -n dbname -s "mysql.example.com"

A list of SQL scripts to be executed can contain files in the *same* directory,
*relative* paths outside the directory, or *absolute* paths anywhere on the system.

`example_sql.lst`

    drop_tables.sql
    ../ddl.sql
    /var/www/project/SQL/stored_procedures.sql

Use the generated credentials file to execute the list of SQL scripts:

    ./vendor/jpuck/mydtp/exec_sql.bash -l "/var/www/project/SQL/example_sql.lst" -c credentials.local.bash

Or prompt for database credentials when executing:

    ./vendor/jpuck/mydtp/exec_sql.bash -l "/var/www/project/SQL/example_sql.lst"

----------

  [1]:https://en.wikipedia.org/wiki/Data_definition_language
  [2]:https://en.wikipedia.org/wiki/Data_manipulation_language
  [3]:https://en.wikipedia.org/wiki/Principle_of_least_privilege
  [4]:https://github.com/jpuck/mydtp/issues
  [5]:https://getcomposer.org/
  [6]:https://packagist.org/packages/jpuck/mydtp
  [7]:https://poser.pugx.org/jpuck/mydtp/v/stable
  [8]:https://poser.pugx.org/jpuck/mydtp/downloads
  [9]:https://poser.pugx.org/jpuck/mydtp/license
