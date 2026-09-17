<?php
/**
 * config/db.php
 * Bridge file to maintain backward compatibility for legacy APIs using procedural global $pdo.
 */
require_once __DIR__ . '/Database.php';
$pdo = \Config\Database::getInstance()->getConnection();
