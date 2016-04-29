<?php

/**
 * It's a quick autoloader
 * Include this file only if you don't use composer
 */

spl_autoload_register(function ($classname) 
{
	$classname = ltrim($classname, "\\");
	preg_match('/^(.+)?([^\\\\]+)$/U', $classname, $match);
	$classname = __DIR__ . '/src/'.str_replace("\\", "/", $match[1])
		. str_replace(array("\\", "_"), "/", $match[2])
		. ".php";

	require_once $classname;
});
