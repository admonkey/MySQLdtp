# Generate MySQL Environments

[![Latest Stable Version](https://poser.pugx.org/jpuck/mydtp/v/stable)](https://packagist.org/packages/jpuck/mydtp) [![Total Downloads](https://poser.pugx.org/jpuck/mydtp/downloads)](https://packagist.org/packages/jpuck/mydtp) [![Latest Unstable Version](https://poser.pugx.org/jpuck/mydtp/v/unstable)](https://packagist.org/packages/jpuck/mydtp) [![License](https://poser.pugx.org/jpuck/mydtp/license)](https://packagist.org/packages/jpuck/mydtp)

Use Bash & MySQL client to create dev/test/prod database & users, and execute a list of your SQL scripts.
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

* GNU bash, version 4.3.11(1)-release (x86_64-pc-linux-gnu)
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

### Saving Commands & Version Control

Writing out these long commands repeatedly gets old quickly.
`exec_sql.bash` will save the last command you run to an executable file called `exec_sql`
Notice that it has no file extension such as `.bash`
You will be able to run the last saved command with an easy shortcut:

    ./exec_sql

If you run a different command later, then you will be prompted to overwrite before saving.

It is advisable to commit both your shortcut `exec_sql` and the SQL list to version control,
but *do not* track your `credentials.local.*` files.

### Troubleshooting

    line 95: $sql: ambiguous redirect

This is most likely caused by sending your `ddl.sql` file as the
list. The `sql.lst` is supposed to be a list of filenames that
contain DDL, and not the DDL file itself. This is a common mistake
when your project only has one DDL file.

## Developing and Testing this Project

There's a `tests` folder with some trivial SQL and an executable `exec_sql`
the contents of which are:

    ../exec_sql.bash -c credentials.local.bash -l SQL/sql.lst

So in order to generate the required `credentials.local.bash` file, just run
the included script to set up your test database, for example called `mydtp`:

    cd tests
    ../create_db_users.bash -e test -n mydtp

----------

  [1]:https://en.wikipedia.org/wiki/Data_definition_language
  [2]:https://en.wikipedia.org/wiki/Data_manipulation_language
  [3]:https://en.wikipedia.org/wiki/Principle_of_least_privilege
  [4]:https://github.com/jpuck/mydtp/issues
  [5]:https://getcomposer.org/
  [6]:https://packagist.org/packages/jpuck/mydtp
