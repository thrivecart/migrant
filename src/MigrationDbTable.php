<?php

namespace Fluxoft\Migrant;

class MigrationDbTable {
	private $connection;
	private $tableName;

	public function __construct(\PDO $connection, $tableName = 'migrant_log') {
		$this->connection = $connection;
		$this->tableName  = $tableName;
		$this->init();
	}

	public function GetExecutedMigrations() {
		$sql  = 'SELECT revision, migration_start, migration_end FROM '.$this->tableName . ' ORDER BY revision';
		$stmt = $this->connection->prepare($sql);
		$stmt->execute();
		$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		$hash = [];
		foreach ($data as $row) {
			$hash[$row['revision']] = $row;
		}
		return $hash;
	}

	public function AddMigration($revision, \DateTime $startTime, \DateTime $endTime) {
		$sql  = 'INSERT INTO '.$this->tableName.' (revision, migration_start, migration_end)
		         VALUES (:revision, :migrationStart, :migrationEnd)';
		$stmt = $this->connection->prepare($sql);
		$stmt->execute([
			'revision' => $revision,
			'migrationStart' => $startTime->format('Y-m-d H:i:s'),
			'migrationEnd' => $endTime->format('Y-m-d H:i:s')
		]);
	}

	public function RemoveMigration($revision) {
		$sql  = 'DELETE FROM '.$this->tableName.' WHERE revision = :revision';
		$stmt = $this->connection->prepare($sql);
		$stmt->execute([
			'revision' => $revision
		]);
	}

	private function init() {
		$tableExists = true;
		$result      = false;
		try {
			$result = $this->connection->query('SELECT 1 FROM ' . $this->tableName . ' LIMIT 1');
		} catch (\PDOException $e) {
			$tableExists = false;
		}
		if ($result === false) {
			$tableExists = false;
		}
		if (!$tableExists) {
			$sql = 'CREATE TABLE ' . $this->tableName . '(
				revision BIGINT NOT NULL PRIMARY KEY,
				migration_start DATETIME NOT NULL,
				migration_end DATETIME NOT NULL
				)';
			$this->connection->exec($sql);
		}
	}
}
