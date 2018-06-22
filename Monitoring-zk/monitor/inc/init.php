<?php


define('APPROOT', str_replace('\\', '/', dirname(__DIR__)));
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

spl_autoload_register(function ($className) {
	$classRoot = APPROOT . '/inc/classes';
	$extension = '.php';
	$path = $classRoot . '/' . $className . $extension;
	if(file_exists($path)) {
		require $path;
	}
});

set_error_handler(function ($severity, $message, $file, $line) {
	if(!(error_reporting() & $severity)) {
		// This error code is not included in error_reporting
		return;
	}
	throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($exception) {
	$exception instanceof Exception;
	http_response_code(500);
	echo "<pre>\n";
	echo "[[[Fatale uitzondering]]]\n\n";
	echo $exception;
	die();
});


?>
