<?php
echo "Testing routing...\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PATH_INFO: " . (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : 'Not set') . "\n";
echo "QUERY_STRING: " . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'Not set') . "\n";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Parsed path: " . $path . "\n";

$path = ltrim($path, '/');
echo "After ltrim: " . $path . "\n";

$basePath = dirname($_SERVER['SCRIPT_NAME']);
echo "Base path: " . $basePath . "\n";

if ($basePath !== '/') {
    $path = substr($path, strlen($basePath));
}
echo "After base path removal: " . $path . "\n";

$path = ltrim($path, '/');
echo "Final path: " . $path . "\n";
?>
