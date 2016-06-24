# Generate MySQL Environments

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

## Naming Schema

Database names are prefixed with a maximum 7 character name of your choosing.
Then a letter designates their environment `_D_` (Development), `_T_` (Test), or `_P_` (Production).

Every instance contains a randomly generated 5 character identifier. This allows you to easily spin up
alternate environments for development and testing on the same server without conflict.
The associated user accounts also end with this corresponding identifier.

Usernames are the same as the database name distinguished only by a `_U*_` instead of an environment flag.
Test environments have two user accounts with the same password and nearly identical usernames.
The privileged accounts are designated by `_UA_` (User ALL)
and are intended for use with [DDL][1] in development and testing.
The execute only accounts are designated by `_UE_` (User EXECUTE)
and are intended for use by the application in testing and production.
This follows the [principle of least privilege][3] whereby
all [DML][2] is wrapped within explicit parameterized stored procedures.

### Examples

* Development
    * Database Name: `example_D_4JAOb`
    * Privileged User: `example_UA_4JAOb`
* Test
    * Database Name: `example_T_zWwAo`
    * Privileged User: `example_UA_zWwAo`
    * Application User: `example_UE_zWwAo`
* Production
    * Database Name: `example_P_NITvJ`
    * Application User: `example_UE_NITvJ`

----------

## Getting Started

There are two scripts:
one for creating the database and users,
and one for executing SQL scripts, such as [DDL][2].

`create_db_users.bash`

    environment: -e <dev|test|prod>
    database name (limit 7 characters): -n <name>
    database server (optional default localhost): -s <hostname>

`exec_sql.bash`

    list of SQL files: -l </path/to/SQL.lst>
    credentials file:  -c </path/to/credentials.local.bash>
    credentials input: -u

`exec_sql.bash` will execute scripts listed in order from the directory in which the list is located.
The list may contain relative paths to files outside its directory, or absolute paths.

### Examples

Create a development environment with name prefix `dbname` on localhost:

    ./create_db_users.bash -e dev -n dbname

Create a production environment with name prefix `dbname` on a server located at `mysql.example.com`

    ./create_db_users.bash -e prod -n dbname -s "mysql.example.com"

A list of SQL scripts to be executed can contain files in the same directory,
relative paths outside the directory, or absolute paths anywhere on the system.

`example_sql.lst`

    drop_tables.sql
    ../ddl.sql
    /var/www/project/SQL/stored_procedures.sql

Execute scripts to drop tables, create tables, and stored procedures using credentials file:

    ./exec_sql.bash -l "/var/www/project/SQL/example_sql.lst" -c credentials.local.bash

Execute scripts prompting input for database credentials:

    ./exec_sql.bash -l "/var/www/project/SQL/example_sql.lst" -u

----------
[1]:https://en.wikipedia.org/wiki/Data_definition_language
[2]:https://en.wikipedia.org/wiki/Data_manipulation_language
[3]:https://en.wikipedia.org/wiki/Principle_of_least_privilege

> Written with [StackEdit](https://stackedit.io/).
