<?php

namespace Fluxoft\Migrant;

class Printer {
	public static function PrintException(\Exception $e) {
		echo "\n";
		echo "*****************\n";
		echo "*** EXCEPTION ***\n";
		echo "*****************\n";
		echo $e->getMessage()."\n";
		echo "\n";
	}

	public static function PrintMessage($message) {
		echo "\n";
		echo "$message\n";
		echo "\n";
	}
}
