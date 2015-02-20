<?php

namespace Fluxoft\Migrant;

class ConfigTest extends \PHPUnit_Framework_TestCase {
	protected $configMock;

	protected function setup() {
		$this->configMock = $this->getMockBuilder('\Fluxoft\Migrant\Config')
			->disableOriginalConstructor()
			->getMock();
	}

	protected function teardown() {}

	public function testFooNotEqualBar() {
		$this->assertNotEquals('foo', 'bar');
	}
}
 