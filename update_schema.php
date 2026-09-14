<?php
$sql = file_get_contents('fix_hullandgrahamtrading.php');
$extracted = substr($sql, strpos($sql, 'CREATE TABLE'));
$extracted = str_replace('");', '', $extracted);
$extracted = str_replace('echo "OK\n";', '', $extracted);
file_put_contents('database/tenant_schema.sql', "\n\n" . trim($extracted), FILE_APPEND);
echo "Schema updated!\n";
