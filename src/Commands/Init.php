<?php

namespace Fluxoft\Migrant\Commands;

use Fluxoft\Migrant\Exceptions\CommandException;

class Init implements CommandInterface {
	private $workingFolder;

	public function __construct($workingFolder) {
		$this->workingFolder = $workingFolder;
	}

	/**
	 * @return string
	 * @throws CommandException
	 */
	public function Run() {
		try {
			$migrationsFolder = $this->workingFolder . DIRECTORY_SEPARATOR . "migrations";
			$migrationsIni    = $this->workingFolder . DIRECTORY_SEPARATOR . "migrant.ini";
			if (!is_dir($migrationsFolder)) {
				mkdir($migrationsFolder);
			}
			if (!is_file($migrationsIni)) {
				$ini = <<<EOF
[development]
type     = mysql
host     = localhost
dbname   = development
username = username
password = password
;port    = 3306
;charset = utf8
;table   = migrant_log
[testing]
type     = mysql
host     = localhost
dbname   = testing
username = username
password = password
;port    = 3306
;charset = utf8
;table   = migrant_log
[production]
type     = mysql
host     = localhost
dbname   = production
username = username
password = password
;port    = 3306
;charset = utf8
;table   = migrant_log

EOF;
				file_put_contents($migrationsIni, $ini);
			}
			$message = sprintf(
				'Migrant environment is set up. Make sure to modify the migrant.ini file found in "%s".',
				$this->workingFolder
			);
		} catch (\Exception $e) {
			throw new CommandException(sprintf(
				'Unable to initialize the migrant setup. The exception was "%s"',
				$e->getMessage()
			));
		}
		return $message;
	}
}
