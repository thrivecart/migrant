<?php

namespace Fluxoft\Migrant\Commands;

interface CommandInterface {
	/**
	 * @return string Status message upon successful run of the command.
	 */
	public function Run();
}
