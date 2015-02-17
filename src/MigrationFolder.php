<?php

namespace Fluxoft\Migrant;

use Fluxoft\Migrant\Exceptions\FileException;

class MigrationFolder {
	private $folder;

	public function __construct($workingFolder) {
		$this->folder = $workingFolder.DIRECTORY_SEPARATOR.'migrations';
		if (!is_dir($this->folder)) {
			throw new FileException(sprintf(
				"Cannot find a migrations folder at \"%s\".\nHas \"migrant init\" been run yet?",
				$this->folder
			));
		}
	}

	public function AddFile($filename) {
		try {
			file_put_contents($this->folder.DIRECTORY_SEPARATOR.$filename, $this->getDefaultMigrationText());
		} catch (\Exception $e) {
			throw new FileException(sprintf(
				'Unable to create the file "%s". The exception was "%s"',
				$filename,
				$e->getMessage()
			));
		}
	}

	/**
	 * @return array
	 */
	public function GetAvailableMigrations() {
		$files = array_diff(scandir($this->folder), ['..', '.']);

		$hash = [];
		foreach ($files as $file) {
			list ($revision,) = explode('_', $file, 2);
			$hash[$revision]  = $file;
		}

		return $hash;
	}

	public function GetUpMigration($filename) {
		$contents = file_get_contents($this->folder.DIRECTORY_SEPARATOR.$filename);

		list($up, ) = explode('-- //@UNDO', $contents);
		return $up;
	}

	public function GetDownMigration($filename) {
		$contents = file_get_contents($this->folder.DIRECTORY_SEPARATOR.$filename);

		list(, $down) = explode('-- //@UNDO', $contents);
		return $down;
	}

	private function getDefaultMigrationText() {
		$text = <<<EOF
-- Add the SQL for your "up" command here:

-- //@UNDO
-- Add the SQL for your "down" command here:


EOF;
		return $text;
	}
}
