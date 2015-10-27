<?php

use LawnGnome\Squall\Parser;
use LawnGnome\Squall\Query;
use LawnGnome\Squall\QueryInterface;

class ParserTest extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException LawnGnome\Squall\Exception\ParseException
	 */
	public function testCommentsOnly() {
		$sql = <<<'EOL'
-- foo
  -- bar
EOL;

		(new Parser)->parse($sql);
	}

	/**
	 * @expectedException LawnGnome\Squall\Exception\ParseException
	 */
	public function testNoQueries() {
		(new Parser)->parse('');
	}

	/**
	 * @expectedException LawnGnome\Squall\Exception\ParseException
	 */
	public function testQueryBeforeAnnotation() {
		$sql = <<<'EOL'
SELECT foo FROM bar;

-- name: foo
SELECT foo FROM bar;
EOL;

		(new Parser)->parse($sql);
	}

	public function testQueries() {
		$sql = <<<'EOL'
-- name: foo
SELECT foo FROM bar;

-- name: bar
SELECT bar FROM foo WHERE id = :id;
EOL;

		$queries = (new Parser)->parse($sql);

		$this->assertContainsOnlyInstancesOf(Query::class, $queries);
		$this->assertCount(2, $queries);
		$this->assertArrayHasKey('foo', $queries);
		$this->assertArrayHasKey('bar', $queries);
		$this->assertContains('SELECT foo FROM bar;', $queries['foo']->getSQL());
		$this->assertContains('SELECT bar FROM foo WHERE id = :id;', $queries['bar']->getSQL());
	}

	public function testCreateQuery() {
		$parser = new class extends Parser {
			public function createQuery(string $query): QueryInterface {
				return parent::createQuery($query);
			}
		};
		$query = $parser->createQuery('SELECT foo');
		$this->assertInstanceOf(Query::class, $query);
	}
}

// vim: set noet ts=4 sw=4:
