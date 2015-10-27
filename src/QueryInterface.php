<?php

namespace LawnGnome\Squall;

use PDO;
use PDOStatement;

interface QueryInterface {
	public function execute(PDO $db, array $parameters = null): PDOStatement;
}

// vim: set noet ts=4 sw=4:
