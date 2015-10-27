<?php

namespace LawnGnome\Squall;

use LawnGnome\Squall\Exception\ParseException;

class Parser implements ParserInterface {
	public function parse(string $data): array {
		$current = null;
		$queries = [];

		foreach (explode("\n", $data) as $n => $line) {
			$matches = null;
			if (preg_match('/^\s*--\s+name:\s*(.*)\s*$/', $line, $matches)) {
				// New name: annotation.
				if ($current) {
					$queries[$current[0]] = $this->createQuery($current[1]);
				}
				$current = [$matches[1], ''];
			} elseif (is_null($current) && !preg_match('/^\s*--/', $line)) {
				// Check for query definitions before annotations.
				throw new ParseException("Error in line $n: query definition before a name: annotation");
			} elseif ($current) {
				// Normal content.
				$current[1] .= "$line\n";
			}
		}

		if ($current) {
			$queries[$current[0]] = $this->createQuery($current[1]);
		}

		if (count($queries) == 0) {
			throw new ParseException('No queries defined');
		}

		return $queries;
	}

	protected function createQuery(string $query): QueryInterface {
		return new Query($query);
	}
}

// vim: set noet ts=4 sw=4:
