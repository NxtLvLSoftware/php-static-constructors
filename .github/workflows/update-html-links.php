<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Build\UpdateHtmlLinks;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use function array_map;
use function file_get_contents;
use function file_put_contents;
use function getopt;
use function implode;
use function microtime;
use function preg_quote;
use function preg_replace;
use function realpath;
use function sprintf;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

define('START_TIME', microtime(true));

const SOURCE_FILE_TYPES = [
	'.html'
]; // file types to search

const SOURCE_ATTRIBUTE_TYPES = [
	'href',
	'src'
]; // html attributes to search

function info(string $message, mixed ...$params): void {
	echo '[INFO] ' . sprintf($message, ...$params) . PHP_EOL;
}

function error(string $message, mixed ...$params): never {
	echo '[ERROR] ' . sprintf($message, ...$params) . PHP_EOL;
	exit(1);
}

/**
 * @param string[] $strings
 *
 * @return string[]
 */
function preg_quote_array(array $strings, string $delim = '/'): array {
	return array_map(static function (string $str) use ($delim): string {
		return preg_quote($str, $delim);
	}, $strings);
}

function main(array $opts): void {
	$target = $opts['t'] ?? $opts['target'] ?? null;
	if ($target === null) {
		error('Specify target directory with -t <path> or --target <path>');
	}
	$realTarget = realpath($target);
	info('Scanning target directory "%s" html files for links that expect to be in root.', $realTarget);

	$prefix = $opts['p'] ?? $opts['prefix'] ?? null;
	if ($prefix === null) {
		$prefix = './';
		info('No prefix specified, will make links relative with "%s" prefix.', $prefix);
	} else {
		info('Custom prefix specified. Will prefix links with "%s".', $prefix);
	}

	$iteratorRegex = sprintf(
		'/^%s.*[%s]/i',
		preg_quote($realTarget . DIRECTORY_SEPARATOR, '/'), // must start with this path
		implode('|', preg_quote_array(SOURCE_FILE_TYPES)) // must end with one of these strings
	);

	$directory = new RecursiveDirectoryIterator(
		$realTarget,
		FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS |
		FilesystemIterator::CURRENT_AS_PATHNAME
	);
	$regexIterator = new RecursiveRegexIterator($directory, $iteratorRegex);
	$iteratorIterator = new RecursiveIteratorIterator($regexIterator);

	// search for links that expect to be in the root of a site
	$regex = sprintf('/(%s)="(?![http|%s\.\.\/*])([^"]*)"/i',
		implode('|', preg_quote_array(SOURCE_ATTRIBUTE_TYPES)), // must be one of these attributes
		$prefix !== '' ? preg_quote($prefix, '/') . '|' : '' // ignore paths that already have prefix
	);

	$fileCount = 0;
	$replaceCount = 0;
	foreach ($iteratorIterator as $filename) {
		$found = 0;
		// replace found links with the supplied relative prefix
		$replaced = preg_replace(
			$regex,
			'$1="' . $prefix . '$2"',
			file_get_contents($filename),
			count: $found
		);
		file_put_contents($filename, $replaced);

		++$fileCount;
		$replaceCount += $found;
	}

	info(
		'Done! Prefixed %d links in %d files (%.3fs.)',
		$replaceCount,
		$fileCount,
		microtime(true) - START_TIME
	);
}

main(
	getopt(
		short_options: 't:p:',
		long_options : ['target:', 'prefix:']
	)
);
