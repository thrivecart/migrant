Migrant -- Database migration tool
==================================

[![Build Status](https://travis-ci.org/fluxoft/migrant.svg?branch=master)](https://travis-ci.org/fluxoft/migrant)
[![Latest Stable Version](https://poser.pugx.org/fluxoft/migrant/v/stable.svg)](https://packagist.org/packages/fluxoft/migrant)
[![Total Downloads](https://poser.pugx.org/fluxoft/migrant/downloads.svg)](https://packagist.org/packages/fluxoft/migrant)
[![Latest Unstable Version](https://poser.pugx.org/fluxoft/migrant/v/unstable.svg)](https://packagist.org/packages/fluxoft/migrant)
[![License](https://poser.pugx.org/fluxoft/migrant/license.svg)](https://packagist.org/packages/fluxoft/migrant)

## Description

This is a simple database migration tool for automating versioning between copies of the same database on different
environments.

The design goal for this package was to provide a easily memorable command-line interface for a program that would allow
the developer to use native SQL code for the migrations themselves, including the ability to drop in the output from a
database dump as the first migration and have that code execute properly.
 
## Installation
Add the dependency. https://packagist.org/packages/fluxoft/migrant

    {
    	"require": {
    		"fluxoft/migrant": "dev-master"
    	}
    }
    
Download the composer.phar

    curl -sS https://getcomposer.org/installer | php

Install the library.

    php composer.phar install

## Usage

Once Migrant is installed in your Composer vendor directory for the project, you will just need to make a folder to 
keep your migration set up (I usually make a "db" folder at the same level as my "vendor" folder in the root of my
project).

Migrant is used by passing it a series of command-line arguments. There are 5 recognized commands:

### init

To initialize your migrant directory:

    ../vendor/bin/migrant init

This will create a migrant.ini file where you will need to set up your database connections for your various
environments and a migrations folder where new migrations are created.

### add

Next, either add a migration file manually or by
using "migrant add" to create one in your migrations folder:

    ../vendor/bin/migrant add example

The command above will create a file named something like "migrations/20150219114901_example.sql" with a blank migration
template. Open that file and add the SQL that should be run for both the "up" and "down" migrations. Make sure to leave
the line "`-- //@UNDO`" intact between the up and the down. If you create your own migration, keep in mind that the
integer before the first slash is used as the sorting key, so if you use an integer with fewer characters than 14 (by
default "migrant add" uses the year, month, date, hour, minute, and second as the sort value), migrations may appear out
of order.

For instance, the following files:

```
drwxr-xr-x 2 www-data www-data 4096 Feb 19 16:53 ./
drwxr-xr-x 3 www-data www-data 4096 Feb 17 19:12 ../
-rw-r--r-- 1 www-data www-data  225 Feb 19 16:53 123_test.sql
-rw-r--r-- 1 www-data www-data  400 Feb 17 19:13 20150217191326_initial.sql
-rw-r--r-- 1 www-data www-data  235 Feb 17 19:21 20150217191656_second.sql
-rw-r--r-- 1 www-data www-data  225 Feb 19 16:53 321_test2.sql
```

Are actually executed in the following order:

```
root@vagrant-ubuntu-trusty-64:/websites/test.com/db2# ../vendor/bin/migrant status

The following migrations were found:
Revision            Filename                                           Migrated?
================================================================================
123                 123_test.sql                                              NO
321                 321_test2.sql                                             NO
20150217191326      20150217191326_initial.sql                                NO
20150217191656      20150217191656_second.sql                                 NO
```

### up

When you are ready to migrate, run the "up" command:

    migrant up

This will run all available migrations against the "development" environment. To specify a different environment:

    migrant up production

To migrate up to a specific revision number and no further:

    migrant up 20150220123456

...or to this revision on the production environment:

    migrant up 20150220123456 production

### down

If you need to roll back a migration, the "down" command works in much the same way as the "up" command:

    migrant down

The key difference is that "down" will only roll back a single revision at a time unless a target revision is given:

    migrant down 20141231235959

To roll back every migration, specify "0" as the target revision:

    migrant down 0

As with "down" a specific environment can be given as the last argument:

    migrant down testing

### status

To see all available migrations and to see which ones have been run, use the "status" command.

```
The following migrations were found:
Revision            Filename                                           Migrated?
================================================================================
123                 123_test.sql                                             YES
321                 321_test2.sql                                            YES
20150217191326      20150217191326_initial.sql                                NO
20150217191656      20150217191656_second.sql                                 NO
```

As with the other commands, this command will accept an environment name but runs against "development" by default:

    migrant status production

### Help

To get the inline help, simply run migrant with no arguments:

```
root@vagrant-ubuntu-trusty-64:/var/www/db# ../vendor/bin/migrant 

Usage:
  migrant <command> [<params>] [<environment> (default = "development")]

Available commands:

  init    Set up a fresh installation with default migrations directory
          and config files.
            "migrant init"

  add     Add a new migration with parameter <name>
            "migrant add <name> [<environment>]"

  up      Update the database to a specific revision, or using all
          available migrations.
            "migrant up [<revision>] [<environment>]"

  down    Rollback the database by a single migration from its current state,
          or all revisions with a revision number higher than or equal to
          <revision>, or roll back every migration by passing <revision> of 0.
            "migrant down [<revision>] [<environment>]"

  status  Report on available versus installed migrations.
            "migrant status"
            
root@vagrant-ubuntu-trusty-64:/var/www/db#
```
