<?php

trait Mocks {
	protected function createMockPDO(): PDO {
		return $this->getMockBuilder('PDO')
		            ->disableOriginalConstructor()
		            ->getMock();
	}

	protected function createMockPDOStatement(): PDOStatement {
		return $this->getMockBuilder('PDOStatement')
		            ->disableOriginalConstructor()
		            ->getMock();
	}
}

// vim: set noet ts=4 sw=4:
