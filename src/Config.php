<?php

namespace Fluxoft\Migrant;

use \Fluxoft\Migrant\Exceptions\ConfigException;

class Config implements \ArrayAccess {
	private $config = [];
	public function __construct($iniFile, $environmentName) {
		if (!is_file($iniFile)) {
			throw new ConfigException(sprintf(
				'No config file was found at "%s". If you have not yet run "migrant init" in this folder, do so now.',
				$iniFile
			));
		}
		$config = parse_ini_file($iniFile, true);
		if (!isset($config[$environmentName])) {
			throw new ConfigException(sprintf(
				'No configuration for the environment "%s" was found. Please check the migrant.ini file.',
				$environmentName
			));
		}
		$this->config = $config[$environmentName];
		foreach (['type', 'host', 'dbname'] as $setting) {
			if (!isset($this->config[$setting])) {
				throw new ConfigException(sprintf(
					'The "%s" must be set for "%s" in the migrant.ini file.',
					$setting,
					$environmentName
				));
			}
		}
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		return (isset($this->config[$offset]));
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		return $this->config[$offset];
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @throws ConfigException
	 */
	public function offsetSet($offset, $value) {
		throw new ConfigException('Config values cannot be written by this class.');
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @throws ConfigException
	 */
	public function offsetUnset($offset) {
		throw new ConfigException('Config values cannot be deleted by this class.');
	}
}
