// src/merge-package-json.php
<?php

$packageJsonPath = __DIR__ . '/../package.json';
$appPackageJsonPath = 'package.json';

$packageJson = json_decode(file_get_contents($packageJsonPath), true);
$appPackageJson = json_decode(file_get_contents($appPackageJsonPath), true);

$mergedPackageJson = array_merge_recursive($appPackageJson, $packageJson);

file_put_contents($appPackageJsonPath, json_encode($mergedPackageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "package.json merged successfully.\n";
