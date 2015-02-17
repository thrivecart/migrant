<?php

namespace Fluxoft\Migrant\Commands;

use Fluxoft\Migrant\Environment;
use Fluxoft\Migrant\Exceptions\EnvironmentException;

class Down implements CommandInterface {
	private $environment;
	private $revision;

	public function __construct(Environment $environment = null, $revision = null) {
		$this->environment = $environment;
		$this->revision    = $revision;
	}

	public function Run() {
		$migrationFolder  = $this->environment->GetMigrationFolder();
		$migrationDbTable = $this->environment->GetMigrationDbTable();

		$availableMigrations = $migrationFolder->GetAvailableMigrations();
		$executedMigrations  = $migrationDbTable->GetExecutedMigrations();

		$message  = "There are ".count($availableMigrations)." available migrations, ";
		$message .= count($executedMigrations)." of which have been executed.\n";

		if (isset($this->revision)) {
			$downTo = $this->revision;
			if (count($executedMigrations) > 0) {
				$message .= "Migrating down to target revision $downTo:\n\n";
			}
		} else {
			$downTo = end(array_keys($executedMigrations));
			if (count($executedMigrations) > 0) {
				$message .= "Migrating down by one: rolling back revision $downTo:\n\n";
			}
		}

		// Migrate down in reverse order.
		krsort($executedMigrations);

		$success = true;
		foreach ($executedMigrations as $revision => $migration) {
			$filename = (isset($availableMigrations[$revision])) ? $availableMigrations[$revision] : null;

			if ($revision >= $downTo) {
				$message .= "Rolling back revision $revision... ";
				try {
					$this->environment->Down($revision, $filename);
					$message .= "Done!\n";
				} catch (EnvironmentException $e) {
					$message .= "FAILED!\n".$e->getMessage()."\n";
					$message .= "\nStopping all further migrations.\n";
					$success = false;
					break;
				}
			} else {
				$message .= "Revision $revision is lower than the target. Skipping... \n";
			}
		}

		if ($success) {
			$message .= '...migrations complete.';
		}

		return $message;
	}
}
