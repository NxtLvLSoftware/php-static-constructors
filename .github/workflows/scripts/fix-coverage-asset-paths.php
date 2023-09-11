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
	use function array_keys;
	use function array_map;
	use function define;
	use function file_get_contents;
	use function file_put_contents;
	use function getopt;
	use function implode;
	use function is_dir;
	use function microtime;
	use function NxtLvlSoftware\StaticConstructors\Build\CliScriptHelpers\{
		clean_path,
		error,
		info,
		iterate_dir,
		regex_esc,
		regex_esc_array,
		recursive_unlink
	};
	use function preg_match_all;
	use function preg_quote;
	use function rename;
	use function sprintf;
	use function str_replace;

	use const DIRECTORY_SEPARATOR;

	define('START_TIME', microtime(true));

	const ASSETS_DIR_MAPPING = [
		'_css'   => 'css',
		'_icons' => 'icons',
		'_js'    => 'js'
	]; // directories to rename

	const SOURCE_FILE_TYPES = [
		'.html'
	]; // file types to search

	const SOURCE_ATTRIBUTE_TYPES = [
		'href',
		'src'
	]; // html attributes to search

	/**
	 * @param array{t?: string, target?: string} $opts
	 */
	function main(array $opts): void {
		$target = $opts['t'] ?? $opts['target'] ?? null;
		if ($target === null) {
			error('Specify target directory with -t <path> or --target <path>');
		}
		$realTarget = clean_path($target);
		info('Searching target directory "%s" for coverage assets.', $realTarget);

		foreach (ASSETS_DIR_MAPPING as $from => $to) {
			$realFrom = $realTarget . DIRECTORY_SEPARATOR . $from;
			if (!is_dir($realFrom)) {
				error('Could not find "%s" asset directory!', $realFrom);
			}
			$realTo = $realTarget . DIRECTORY_SEPARATOR . $to;
			if (is_dir($realTo)) {
				recursive_unlink($realTo);
			}
			rename($realFrom, $realTo);
			if (!is_dir($realTo)) {
				error('Failed to rename "%s" to "%s".', $realTo);
			} else {
				info('Renamed "%s" to "%s".', $realFrom, $realTo);
			}
		}

		$dirToRenameRegexPart = sprintf(
			'(%s)',
			implode(
				'|', array_map(
					fn(string $value) => sprintf('(?:%s)', preg_quote($value, '/')),
					array_keys(ASSETS_DIR_MAPPING)
				)
			)
		);
		// search for links that expect to be in the root of a site
		$regex = sprintf(
			'/(%s)="(?!(?:http))(.*)%s([^"]*)"/i',
			implode('|', regex_esc_array(SOURCE_ATTRIBUTE_TYPES)), // must be one of these attributes
			$dirToRenameRegexPart // must match one of the mapping keys
		);

		$sourceFileIncludes = array_map(static function (string $suffix): string {
			return sprintf('[^\.].*%s', regex_esc($suffix));
		}, SOURCE_FILE_TYPES); // any file not beginning with . and ending with a source file extension

		$fileCount = 0;
		$replaceCount = 0;
		foreach (iterate_dir($realTarget, $sourceFileIncludes, ignore_files: '') as $filename) {
			/** @var string $filename */
			++$fileCount;
			$contents = file_get_contents($filename);
			if ($contents === false) {
				throw new RuntimeException(
					'file_get_contents() should not return false. Is another application modifying the directory?'
				);
			}
			$matches = [];
			if (preg_match_all($regex, $contents, $matches) === 0) {
				continue;
			}
			$toSearch = [];
			$toReplace = [];
			foreach (array_keys($matches[0]) as $key) {
				[$match, $attribute, $prefix, $replace, $suffix] =
					[$matches[0][$key], $matches[1][$key], $matches[2][$key], $matches[3][$key], $matches[4][$key]];
				$replacement = ASSETS_DIR_MAPPING[$replace] ?? null;
				if ($replacement === null) {
					error('Regex matched "%s" with unknown replacement.', $replace);
				}
				$toSearch[] = $match;
				$toReplace[] = sprintf('%s="%s%s%s"', $attribute, $prefix, $replacement, $suffix);
			}
			$found = 0;
			$contents = str_replace($toSearch, $toReplace, $contents, $found);
			$replaceCount += $found;

			file_put_contents($filename, $contents);
		}

		info(
			'Done! Updated %d links in %d files (%.3fs.)',
			$replaceCount,
			$fileCount,
			microtime(true) - \START_TIME
		);
	}

	main(
		getopt(
			short_options: 't:',
			long_options : ['target:']
		)
	);

} //namespace NxtLvlSoftware\StaticConstructors\Build\UpdateHtmlLinks
