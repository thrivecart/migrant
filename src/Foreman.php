<?php

namespace Fluxoft\Migrant;

use Fluxoft\Migrant\Exceptions\CommandException;

class Foreman {
	private $workingFolder;

	public function __construct($workingFolder) {
		$this->workingFolder = $workingFolder;
	}

	/**
	 * @param array $arguments
	 * @return Commands\CommandInterface|null
	 * @throws CommandException
	 */
	public function GetCommand(array $arguments) {
		/** @var Commands\CommandInterface|null $command */
		$command = null;
		switch ($arguments[0]) {
			case 'init':
				// Init takes no other arguments:
				$command = new Commands\Init($this->workingFolder);
				break;
			case 'add':
				if (!isset($arguments[1])) {
					throw new CommandException('You must give a name for the migration to be added.');
				}
				$migrationName   = $arguments[1];
				$migrationFolder = new MigrationFolder($this->workingFolder);
				$command         = new Commands\Add($migrationFolder, $migrationName);
				break;
			case 'up':
				// both revision and environment are optional
				$revision        = null;
				$environmentName = null;
				if (isset($arguments[1])) {
					if (isset($arguments[2])) {
						// if both are set, set them as revision and $environment
						$revision        = $arguments[1];
						$environmentName = $arguments[2];
					} else {
						// only one value was passed
						// if numeric, assume it is a revision, otherwise environment
						if (is_numeric($arguments[1])) {
							$revision = $arguments[1];
						} else {
							$environmentName = $arguments[1];
						}
					}
				}
				if (isset($environmentName)) {
					$environment = new Environment($this->workingFolder, $environmentName);
				} else {
					$environment = new Environment($this->workingFolder);
				}
				$command = new Commands\Up($environment, $revision);
				break;
			case 'down':
				// both revision and environment are optional
				$revision        = null;
				$environmentName = null;
				if (isset($arguments[1])) {
					if (isset($arguments[2])) {
						// if both are set, set them as revision and $environment
						$revision        = $arguments[1];
						$environmentName = $arguments[2];
					} else {
						// only one value was passed
						// if numeric, assume it is a revision, otherwise environment
						if (is_numeric($arguments[1])) {
							$revision = $arguments[1];
						} else {
							$environmentName = $arguments[1];
						}
					}
				}
				if (isset($environmentName)) {
					$environment = new Environment($this->workingFolder, $environmentName);
				} else {
					$environment = new Environment($this->workingFolder);
				}
				$command = new Commands\Down($environment, $revision);
				break;
			case 'status':
				if (isset($arguments[1])) {
					$environment = new Environment($this->workingFolder, $arguments[1]);
				} else {
					$environment = new Environment($this->workingFolder);
				}
				$command = new Commands\Status($environment);
				break;
			default:
				// no valid command found:
				throw new CommandException('No valid command was found.');
				break;
		}
		return $command;
	}
}
