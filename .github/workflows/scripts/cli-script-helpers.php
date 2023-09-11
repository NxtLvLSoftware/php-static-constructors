<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Build\CliScriptHelpers;

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RuntimeException;
use function array_map;
use function implode;
use function is_dir;
use function is_file;
use function preg_quote as php_preg_quote;
use function preg_replace;
use function realpath;
use function rmdir;
use function rtrim;
use function scandir;
use function sprintf;
use function trim;
use function unlink;
use const DIRECTORY_SEPARATOR;
use const PATH_SEP;
use const SCANDIR_SORT_NONE;

/**
 * Log an info message to stdout with sprintf formatting.
 */
function info(string $message, bool|float|int|string|null ...$params): void {
	echo '[INFO] ' . sprintf($message, ...$params) . PHP_EOL;
}

/**
 * Log an error message to stdout with sprintf formatting and
 * exit the script with an error.
 */
function error(string $message, bool|float|int|string|null ...$params): never {
	echo '[ERROR] ' . sprintf($message, ...$params) . PHP_EOL;
	exit(1);
}

/**
 * Quote regular expression characters.
 *
 * Same as php's built-in preg_quote() with delim param set to a
 * reasonable default.
 *
 * @param string $str
 * @param string $delim
 *
 * @return string
 */
function regex_esc(string $str, string $delim = '/'): string {
	return php_preg_quote($str, $delim);
}

define('PATH_SEP', DIRECTORY_SEPARATOR);
define('REGEX_PATH_SEP', regex_esc(PATH_SEP));

/**
 * Quote regular expression characters for every string in an array.
 *
 *  Same as php's built-in preg_quote() with delim param set to a
 *  reasonable default.
 *
 * @param string[] $strings
 *
 * @return string[]
 */
function regex_esc_array(array $strings, string $delim = '/'): array {
	return array_map(static function (string $str) use ($delim): string {
		return regex_esc($str, $delim);
	}, $strings);
}

/**
 * Remove extra slash chars from a file/dir path.
 *
 * @param string $path The path to clean.
 * @param string[] $chars List of chars to de-duplicate and replace.
 * @param string $replace_with The replacement separator/char.
 * @param bool $realpath Apply realpath() to cleaned path?
 * @param bool $trim Apply trim() to cleaned path?
 * @param bool $rtrim Apply rtrim() to cleaned path?
 *
 * @return string
 */
function clean_path(
	string $path,
	array  $chars = ['/', '\\'],
	string $replace_with = PATH_SEP,
	bool   $realpath = false,
	bool   $trim = false,
	bool   $rtrim = false
): string {
	$regex = sprintf(
		'/((?:%1$s)*?)((?:%2$s)+)((?:%1$s)*?)/',
		implode(
			'|', array_map(
				static fn(string $char) => sprintf(
					'[^%s]',
					regex_esc($char)
				), $chars
			)
		), // ignore non-slash chars
		implode('|', regex_esc_array($chars)) // match one or more slash chars
	);
	$cleaned = preg_replace(
		$regex,
		sprintf('$1%s$3', $replace_with),
		$path
	); // replace 1 or more slash chars with a single slash
	if ($cleaned === null) {
		throw new InvalidArgumentException(sprintf('Failed to clean path "%s". Regex: %s', $path, $regex));
	}
	if ($realpath) {
		$beforeRealpath = $cleaned;
		$cleaned = realpath($cleaned);
		if ($cleaned === false) {
			throw new InvalidArgumentException(sprintf(
				'Provided path "%s" does not exist. Cleaned: %s', $path, $beforeRealpath
			));
		}
		unset($beforeRealpath);
	}
	if ($trim) {
		$cleaned = trim($cleaned, implode('', $chars));
	} elseif ($rtrim) {
		$cleaned = rtrim($cleaned, implode('', $chars));
	}
	return $cleaned;
}

/**
 * Create a recursive iterator for contents of a directory filtered with a regular expression.
 *
 * @param string $path The directory to search for files.
 * @param string[] $include_files List of file patterns to explicitly include (negates $ignore_files.)
 * @param string $ignore_files Pattern to ignore files beginning with certain chars or sequences.
 * @param string[] $include_paths List of path patterns to explicitly include (negates $ignore_paths.)
 * @param string $ignore_paths Pattern to ignore paths beginning with certain chars or sequences.
 * @param bool $apply_realpath Should realpath() be applied to the provided path to make sure the regex works?
 * @param int $iterator_flags Flags to pass to the {@link \RecursiveDirectoryIterator} object.
 *
 * @return \RecursiveIteratorIterator<RecursiveRegexIterator>
 */
function iterate_dir(
	string $path,
	array  $include_files = [],        // file patterns to explicitly include (negates $ignore_files.)
	array  $include_paths = [],        // include all paths that aren't ignored
	string $ignore_files = '[^\.].*',  // hidden files (.gitignore, .bash_profile, etc)
	string $ignore_paths = '[^\/\.]*', // hidden directories (.git, .cache, etc)
	bool   $apply_realpath = true,
	int    $iterator_flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS |
							 FilesystemIterator::CURRENT_AS_PATHNAME
): RecursiveIteratorIterator {
	$realPath = clean_path($path, realpath: $apply_realpath, rtrim: true);
	if (!is_dir($realPath)) {
		throw new InvalidArgumentException(sprintf('Provided path "%s" does not exist.', $path));
	}
	$regexPattern = sprintf(
		'/%s((\/((%s|(%s))\/?))*$|(\/((%s)|(%s))$))/',
		regex_esc($realPath),
		$ignore_paths,
		implode('|', $include_paths),
		$ignore_files,
		implode('|', $include_files)
	);
	echo($regexPattern . PHP_EOL);
	return new RecursiveIteratorIterator(
		new RecursiveRegexIterator(
			new RecursiveDirectoryIterator(
				$realPath,
				$iterator_flags
			), $regexPattern
		)
	);
}

/**
 * Recursively delete a directory and its contents.
 *
 * @param string $dir Directory/path to delete.
 */
function recursive_unlink(string $dir): void {
	if (is_dir($dir)) {
		$objects = scandir($dir, SCANDIR_SORT_NONE);
		if ($objects === false) {
			throw new RuntimeException(
				'scandir() shouldn\'t return false when is_dir() returns true'
			);
		}
		foreach ($objects as $object) {
			if ($object === '.' || $object === '..') {
				continue;
			}
			if (is_dir($dir . '/' . $object)) {
				recursive_unlink($dir . '/' . $object);
			} else {
				$path = realpath($dir . '/' . $object);
				if ($path === false) {
					throw new RuntimeException(
						'realpath() shouldn\'t return false. Is another application modifying the dir?'
					);
				}
				unlink($path);
			}
		}
		rmdir($dir);
	} elseif (is_file($dir)) {
		$path = realpath($dir);
		if ($path === false) {
			throw new RuntimeException(
				'realpath() shouldn\'t return false. Is another application modifying the dir?'
			);
		}
		unlink($path);
	}
}
