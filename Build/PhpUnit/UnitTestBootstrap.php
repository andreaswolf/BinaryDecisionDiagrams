<?php

$composerAutoloader = __DIR__ . '/../../vendor/autoload.php';
if(!file_exists($composerAutoloader)) {
	exit(PHP_EOL . 'Bootstrap Error: The unit test bootstrap requires the Composer autoloader file created at install time. Looked for "' . $composerAutoloader . '" without success.');
}
require_once($composerAutoloader);
