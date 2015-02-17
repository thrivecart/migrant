# Migrant
Database migration tool

[![Build Status](https://travis-ci.org/fluxoft/migrant.svg?branch=master)](https://travis-ci.org/fluxoft/migrant)

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
