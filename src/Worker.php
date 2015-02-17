<?php

namespace Fluxoft\Migrant;

use Fluxoft\Migrant\Commands\Init;
use Fluxoft\Migrant\Exceptions\CommandException;
use Fluxoft\Migrant\Exceptions\EnvironmentException;

class Worker {
	public function Work(array $arguments, $workingFolder) {
		if (empty($arguments)) {
			$this->printUsage();
		} else {
			try {
				$foreman = new Foreman($workingFolder);
				$command = $foreman->GetCommand($arguments);
				Printer::PrintMessage($command->Run());
			} catch (CommandException $e) {
				Printer::PrintException($e);
				$this->printUsage();
			} catch (\Exception $e) {
				Printer::PrintException($e);
			}
		}
	}

	private function printUsage() {
		echo <<<EOF

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


EOF;
	}
}
