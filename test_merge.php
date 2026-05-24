<?php
require_once 'D:/Herd/HRIS/vendor/latsmarbls/piacore/src/Notifications/helpers.php';

$pkgConfig = require 'D:/Herd/HRIS/vendor/latsmarbls/piacore/config/notifications.php';
$appConfig = require 'D:/Herd/HRIS/config/notifications.php';
$merged = array_replace_recursive($pkgConfig, $appConfig);
$hasImportFailed = isset($merged['routes']['import_failed']);
echo 'import_failed in merged: ' . ($hasImportFailed ? 'YES' : 'NO') . "\n";
if ($hasImportFailed) {
    echo 'label: ' . $merged['routes']['import_failed']['label'] . "\n";
}
echo 'Total routes: ' . count($merged['routes']) . "\n";
echo 'Routes: ' . implode(', ', array_keys($merged['routes'])) . "\n";
