<?php

class {{name}} extends TestCase {
	public function testIndex()
	{
		$response = $this->call('GET', '{{resource}}');
		$this->assertTrue($response->isOk());
	}

	public function testShow()
	{
		$response = $this->call('GET', '{{resource}}/1');
		$this->assertTrue($response->isOk());
	}

	public function testCreate()
	{
		$response = $this->call('GET', '{{resource}}/create');
		$this->assertTrue($response->isOk());
	}

	public function testEdit()
	{
		$response = $this->call('GET', '{{resource}}/1/edit');
		$this->assertTrue($response->isOk());
	}
}
