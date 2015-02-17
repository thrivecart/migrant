<?php

namespace Fluxoft\Migrant\Commands;

use Fluxoft\Migrant\Environment;

class Status implements CommandInterface {
	private $environment;

	public function __construct(Environment $environment) {
		$this->environment = $environment;
	}

	public function Run() {
		$migrationFolder  = $this->environment->GetMigrationFolder();
		$migrationDbTable = $this->environment->GetMigrationDbTable();

		$availableMigrations = $migrationFolder->GetAvailableMigrations();
		$executedMigrations  = $migrationDbTable->GetExecutedMigrations();

		$message  = "The following migrations were found:\n";
		$message .= str_pad('Revision', 20);
		$message .= str_pad('Filename', 50);
		$message .= str_pad(' Migrated?', 10) . "\n";
		$message .= str_pad('', 80, '=') . "\n";
		foreach ($availableMigrations as $revision => $filename) {
			$message .= str_pad($revision, 20);
			$message .= str_pad($filename, 50);
			$executed = (isset($executedMigrations[$revision])) ? '  YES' : '   NO';
			$message .= str_pad($executed, 10, ' ', STR_PAD_LEFT) . "\n";
		}

		return $message;
	}
}
