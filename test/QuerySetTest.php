<?php

use LawnGnome\Squall\{ParserInterface, QueryInterface, QuerySet};

require_once __DIR__.'/Mocks.php';

class QuerySetTest extends PHPUnit_Framework_TestCase {
	use Mocks;

	public function testCall() {
		$db = $this->createMockPDO();
		$stmt = $this->createMockPDOStatement();
		$query = $this->getMockBuilder(QueryInterface::class)->getMock();

		$query->expects($this->once())
		      ->method('execute')
		      ->with($this->identicalTo($db))
		      ->willReturn($stmt);

		$qs = $this->createQuerySet($db, ['foo' => $query]);
		$qs->foo();
	}

	public function testCallParameters() {
		$db = $this->createMockPDO();
		$stmt = $this->createMockPDOStatement();
		$query = $this->getMockBuilder(QueryInterface::class)->getMock();

		$query->expects($this->once())
		      ->method('execute')
		      ->with($this->identicalTo($db), $this->identicalTo([1, 2]))
		      ->willReturn($stmt);

		$qs = $this->createQuerySet($db, ['foo' => $query]);
		$qs->foo([1, 2]);
	}

	/**
	 * @expectedException LawnGnome\Squall\Exception\QueryNotFoundException
	 */
	public function testCallNotFound() {
		(new QuerySet($this->createMockPDO()))->foo();
	}

	public function testAll() {
		$queries = ['foo' => null];
		$qs = $this->createQuerySet($this->createMockPDO(), $queries);

		$this->assertSame($queries, $qs->all());
	}

	public function testCount() {
		$queries = ['foo' => null, 'bar' => null];
		$qs = $this->createQuerySet($this->createMockPDO(), $queries);

		$this->assertCount(2, $qs);
		$this->assertSame(2, $qs->count());
	}

	public function testCountZero() {
		$qs = $this->createQuerySet($this->createMockPDO(), []);

		$this->assertCount(0, $qs);
		$this->assertSame(0, $qs->count());
	}

	public function testLoad() {
		$queries = ['foo' => null, 'bar' => null];

		$parser = $this->getMockBuilder(ParserInterface::class)
		               ->getMock();

		$parser->expects($this->once())
		       ->method('parse')
		       ->with('foo')
		       ->willReturn($queries);

		$qs = new QuerySet($this->createMockPDO());
		$qs->load('foo', $parser);
		$this->assertSame($queries, $qs->all());
	}

	public function testLoadFromFile() {
		$db = $this->createMockPDO();

		$mockQS = new class($db) extends QuerySet {
			protected $testCase;

			public function __construct(PDO $db) {
				parent::__construct($db);
			}

			public function load(string $data, ParserInterface $parser = null) {}
		};

		$file = tempnam(sys_get_temp_dir(), 'sql');
		try {
			$qs = get_class($mockQS)::loadFromFile($file, $db);
			$this->assertInstanceOf(QuerySet::class, $qs);
		} finally {
			@unlink($file);
		}
	}

	protected function createQuerySet(PDO $db, array $queries): QuerySet {
		return new class($db, $queries) extends QuerySet {
			public function __construct(PDO $db, $queries) {
				parent::__construct($db);
				$this->queries = $queries;
			}
		};
	}
}

// vim: set noet ts=4 sw=4:
