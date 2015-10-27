<?php

namespace LawnGnome\Squall;

use Countable;
use LawnGnome\Squall\Exception\QueryNotFoundException;
use PDO;
use PDOStatement;

class QuerySet implements Countable, QuerySetInterface {
	protected $db = null;
	protected $queries = [];

	public function __construct(PDO $db) {
		$this->db = $db;
	}

	public function __call(string $name, array $arguments): PDOStatement {
		if (isset($this->queries[$name])) {
			if (count($arguments)) {
				return $this->queries[$name]->execute($this->db, $arguments[0]);
			} else {
				return $this->queries[$name]->execute($this->db);
			}
		}

		throw new QueryNotFoundException("Query $name is undefined");
	}

	public function all(): array {
		return $this->queries;
	}

	public function count(): int {
		return count($this->queries);
	}

	public function load(string $data, ParserInterface $parser = null) {
		$parser = $parser ?? new Parser;
		$this->queries = $parser->parse($data);
	}

	public static function loadFromFile(string $filename, PDO $db): self {
		$set = new static($db);
		$set->load(file_get_contents($filename));

		return $set;
	}
}

// vim: set noet ts=4 sw=4:
