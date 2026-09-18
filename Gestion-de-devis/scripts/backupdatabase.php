#!/usr/bin/env php
<?php

define('ROOT_DIR', dirname(__DIR__));
require_once ROOT_DIR . '/config/config.php';
require_once ROOT_DIR . '/helpers/adminbackuphelper.php';

try {
    $filepath = \Biscaphone\Helpers\AdminBackupHelper::saveBackup();
    fwrite(STDOUT, "Backup created: $filepath\n");
    exit(0);
} catch (\Throwable $e) {
    fwrite(STDERR, "Backup failed: " . $e->getMessage() . "\n");
    exit(1);
}
