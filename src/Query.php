<?php

namespace LawnGnome\Squall;

use LawnGnome\Squall\Exception\QueryFailedException;
use PDO;
use PDOStatement;

class Query implements QueryInterface {
	protected $sql;

	public function __construct(string $sql) {
		$this->sql = $sql;
	}

	public function execute(PDO $db, array $parameters = null): PDOStatement {
		$stmt = $db->prepare($this->getSQL());
		$return = $parameters ? $stmt->execute($parameters) : $stmt->execute();
		if (!$return) {
			throw new QueryFailedException($stmt->errorInfo()[2]);
		}

		return $stmt;
	}

	public function getSQL(): string {
		return $this->sql;
	}
}

// vim: set noet ts=4 sw=4:
