<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace {
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'cli-script-helpers.php';
}

namespace NxtLvlSoftware\StaticConstructors\Build\UpdateHtmlLinks {

	use RuntimeException;
	use function array_map;
	use function define;
	use function file_get_contents;
	use function file_put_contents;
	use function getopt;
	use function implode;
	use function microtime;
	use function NxtLvlSoftware\StaticConstructors\Build\CliScriptHelpers\{
		clean_path,
		error,
		info,
		iterate_dir,
		regex_esc,
		regex_esc_array
	};
	use function preg_quote;
	use function sprintf;

	define('START_TIME', microtime(true));

	const SOURCE_FILE_TYPES = [
		'.html'
	]; // file types to search

	const SOURCE_ATTRIBUTE_TYPES = [
		'href',
		'src'
	]; // html attributes to search

	/**
	 * @param array{t?: string, target?: string, p?: string, prefix?: string} $opts
	 */
	function main(array $opts): void {
		$target = $opts['t'] ?? $opts['target'] ?? null;
		if ($target === null) {
			error('Specify target directory with -t <path> or --target <path>');
		}
		$realTarget = clean_path($target);
		info('Scanning target directory "%s" html files for links that expect to be in root.', $realTarget);

		$prefix = $opts['p'] ?? $opts['prefix'] ?? null;
		if ($prefix === null) {
			$prefix = './';
			info('No prefix specified, will make links relative with "%s" prefix.', $prefix);
		} else {
			info('Custom prefix specified. Will prefix links with "%s".', $prefix);
		}

		$sourceFileIncludes = array_map(static function (string $suffix): string {
			return sprintf('[^\.].*%s', regex_esc($suffix));
		}, SOURCE_FILE_TYPES); // any file not beginning with . and ending with a source file extension

		// search for links that expect to be in the root of a site
		$regex = sprintf(
			'/(%s)="(?![http|%s\.\.\/*])([^"]*)"/i',
			implode('|', regex_esc_array(SOURCE_ATTRIBUTE_TYPES)), // must be one of these attributes
			$prefix !== '' ? preg_quote($prefix, '/') . '|' : '' // ignore paths that already have prefix
		);

		$fileCount = 0;
		$replaceCount = 0;
		foreach (iterate_dir($realTarget, $sourceFileIncludes, ignore_files: '') as $filename) {
			/** @var string $filename */
			$found = 0;
			$contents = file_get_contents($filename);
			if ($contents === false) {
				throw new RuntimeException(
					'file_get_contents() should not return false. Is another application modifying the directory?'
				);
			}
			// replace found links with the supplied relative prefix
			$replaced = preg_replace(
				$regex,
				'$1="' . $prefix . '$2"',
				$contents,
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

} //namespace NxtLvlSoftware\StaticConstructors\Build\UpdateHtmlLinks
