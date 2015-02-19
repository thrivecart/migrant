<?php

namespace Fluxoft\Migrant\Commands;

use Fluxoft\Migrant\Environment;
use Fluxoft\Migrant\Exceptions\EnvironmentException;

class Up implements CommandInterface {
	private $environment;
	private $revision;

	public function __construct(Environment $environment = null, $revision = 0) {
		$this->environment = $environment;
		$this->revision    = $revision;
	}

	public function Run() {
		$migrationFolder  = $this->environment->GetMigrationFolder();
		$migrationDbTable = $this->environment->GetMigrationDbTable();

		$availableMigrations = $migrationFolder->GetAvailableMigrations();
		$executedMigrations  = $migrationDbTable->GetExecutedMigrations();
		$pendingMigrations   = count($availableMigrations) - count($executedMigrations);

		$message  = "There are ".count($availableMigrations)." available migrations, ";
		$message .= "$pendingMigrations of which are pending execution.\n";

		if (isset($this->revision)) {
			$upTo = $this->revision;
			if ($pendingMigrations > 0) {
				$message .= "Migrating up to target revision $upTo:\n\n";
			}
		} else {
			$upTo = end(array_keys($availableMigrations));
			if ($pendingMigrations > 0) {
				$message .= "Migrating all:\n\n";
			}
		}

		// make sure the $availableMigrations are sorted by key
		ksort($availableMigrations);

		$success = true;
		foreach ($availableMigrations as $revision => $filename) {
			if (!isset($executedMigrations[$revision])) {
				if ($revision <= $upTo) {
					$message .= "Migrating up to revision $revision... ";
					try {
						$this->environment->Up($revision, $filename);
						$message .= "Done!\n";
					} catch (EnvironmentException $e) {
						$message .= "FAILED!\n".$e->getMessage()."\n";
						$message .= "\nStopping all further migrations.\n";
						$success = false;
						break;
					}
				} else {
					$message .= "Revision $revision is higher than the target. Skipping... \n";
				}
			}
		}

		if ($success) {
			$message .= '...migrations complete.';
		}

		return $message;
	}
}
