<?php

namespace Fluxoft\Migrant\Commands;

use Fluxoft\Migrant\Exceptions\CommandException;
use Fluxoft\Migrant\MigrationFolder;

class Add implements CommandInterface {
	private $migrationFolder;
	private $migrationName;

	public function __construct(MigrationFolder $migrationFolder, $migrationName) {
		$this->migrationFolder = $migrationFolder;
		$this->migrationName   = $migrationName;
	}

	public function Run() {
		if (!ctype_alnum(str_replace(['-', '_'], '', $this->migrationName))) {
			throw new CommandException(
				'The migration name may only consist of letters, numbers, hyphens (-), and underscores (_).'
			);
		}

		$now       = new \DateTime('now', new \DateTimeZone('UTC'));
		$timestamp = $now->format('YmdHis');
		$filename  = "{$timestamp}_{$this->migrationName}.sql";

		$this->migrationFolder->AddFile($filename);

		$message = "The migration was added as \"$filename.\"";

		return $message;
	}
}
