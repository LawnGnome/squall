<?php

namespace LawnGnome\Squall;

use PDOStatement;

interface QuerySetInterface {
	public function __call(string $name, array $arguments): PDOStatement;
}

// vim: set noet ts=4 sw=4:
