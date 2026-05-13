<?php
$file = __DIR__ . '/../vendor/doctrine/dbal/src/Schema/AbstractSchemaManager.php';
if (!file_exists($file)) {
    exit(0);
}
$content = file_get_contents($file);
$patched = str_replace('->setOptions($options);', '->setOptions($options ?? []);', $content);
if ($content !== $patched) {
    file_put_contents($file, $patched);
    echo 'DBAL MariaDB patch applied' . PHP_EOL;
}
