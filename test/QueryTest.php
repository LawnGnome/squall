<?php

use LawnGnome\Squall\Query;

require_once __DIR__.'/Mocks.php';

class QueryTest extends PHPUnit_Framework_TestCase {
	use Mocks;

	public function testExecute() {
		$sql = 'SELECT 1;';

		$stmt = $this->createMockPDOStatement();
		$stmt->expects($this->once())
		     ->method('execute')
		     ->willReturn(true);

		$db = $this->createMockPDO();
		$db->expects($this->once())
		   ->method('prepare')
		   ->with($sql)
		   ->willReturn($stmt);

		$this->assertSame($stmt, (new Query($sql))->execute($db));
	}

	/**
	 * @expectedException LawnGnome\Squall\Exception\QueryFailedException
	 */
	public function testExecuteFailed() {
		$sql = 'SELECT 1;';

		$stmt = $this->createMockPDOStatement();
		$stmt->expects($this->once())
		     ->method('execute')
		     ->willReturn(false);

		$db = $this->createMockPDO();
		$db->expects($this->once())
		   ->method('prepare')
		   ->with($sql)
		   ->willReturn($stmt);

		$this->assertSame($stmt, (new Query($sql))->execute($db));
	}

	public function testSQL() {
		$query = new Query('SELECT 1;');
		$this->assertSame('SELECT 1;', $query->getSQL());
	}
}

// vim: set noet ts=4 sw=4:
