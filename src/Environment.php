<?php

namespace Fluxoft\Migrant;

use Fluxoft\Migrant\Exceptions\EnvironmentException;

class Environment {
	private $workingFolder;
	private $environmentName;

	private $config     = null;
	private $connection = null;

	private $migrationFolder  = null;
	private $migrationDbTable = null;

	public function __construct($workingFolder, $environmentName = 'development') {
		$this->workingFolder   = $workingFolder;
		$this->environmentName = $environmentName;
	}

	public function GetWorkingFolder() {
		return $this->workingFolder;
	}

	public function GetEnvironmentName() {
		return $this->environmentName;
	}

	public function GetConfig() {
		if (!isset($this->config)) {
			$this->config = new Config(
				$this->workingFolder.DIRECTORY_SEPARATOR.'migrant.ini',
				$this->environmentName
			);
		}
		return $this->config;
	}

	public function GetConnection() {
		if (!isset($this->connection)) {
			$config = $this->GetConfig();
			// set up the appropriate PDO connection
			$type       = $config['type'];
			$host       = $config['host'];
			$dbname     = $config['dbname'];
			$connString = "$type:host=$host;dbname=$dbname";
			if (isset($config['port'])) {
				$port        = $config['port'];
				$connString .= ";port=$port";
			}
			if (isset($config['charset'])) {
				$charset     = $config['charset'];
				$connString .= ";charset=$charset";
			}

			$username = $config['username'];
			$password = $config['password'];

			try {
				$this->connection = new \PDO($connString, $username, $password);
				$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			} catch (\PDOException $e) {
				throw new EnvironmentException(sprintf(
					'There was a problem connecting to the database. The exception was "%s".',
					$e->getMessage()
				));
			}
		}
		return $this->connection;
	}

	public function GetMigrationFolder() {
		if (!isset($this->migrationFolder)) {
			$this->migrationFolder = new MigrationFolder($this->GetWorkingFolder());
		}
		return $this->migrationFolder;
	}

	public function GetMigrationDbTable() {
		if (!isset($this->migrationDbTable)) {
			$config     = $this->GetConfig();
			$connection = $this->GetConnection();
			if (isset($config['table'])) {
				$this->migrationDbTable = new MigrationDbTable($connection, $config['table']);
			} else {
				$this->migrationDbTable = new MigrationDbTable($connection);
			}
		}
		return $this->migrationDbTable;
	}

	public function Up($revision, $filename) {
		$migrationFolder  = $this->GetMigrationFolder();
		$migrationUp      = $migrationFolder->GetUpMigration($filename);
		$connection       = $this->GetConnection();
		$migrationDbTable = $this->GetMigrationDbTable();

		try {
			$startTime = new \DateTime('now', new \DateTimeZone('UTC'));
			$connection->exec($migrationUp);
			$endTime = new \DateTime('now', new \DateTimeZone('UTC'));

			$migrationDbTable->AddMigration($revision, $startTime, $endTime);
		} catch (\PDOException $e) {
			throw new EnvironmentException(sprintf(
				'Unable to run the up command. Exception: "%s"',
				$e->getMessage()
			));
		}
	}

	public function Down($revision, $filename = null) {
		$migrationFolder  = $this->GetMigrationFolder();
		$connection       = $this->GetConnection();
		$migrationDbTable = $this->GetMigrationDbTable();

		// It is possible that a down migration might happen for a revision for which the
		// matching file has already been deleted or for some other reason is not present.
		// In this case, the migration can't be properly run, but we do still delete the
		// matching revision in the change log table in an attempt to quell confusion.
		/*
		 * @todo: this may be a terrible idea; revisit the possibility of throwing an exception if the file is not found.
		 */
		try {
			if (isset($filename)) {
				$migrationDown = $migrationFolder->GetDownMigration($filename);
				$connection->exec($migrationDown);
			}

			$migrationDbTable->RemoveMigration($revision);
		} catch (\PDOException $e) {
			throw new EnvironmentException(sprintf(
				'Unable to run the down command. Exception: "%s"',
				$e->getMessage()
			));
		}
	}
}
