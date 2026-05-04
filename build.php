#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * CI / deploy helper: install PHP deps and build frontend assets.
 *
 * Does not delete .git or source files. Optional cleanup for release tarballs:
 *   php build.php --cleanup-release
 */

if (PHP_SAPI !== 'cli') {
    exit(0);
}

$pluginRoot = dirname(__FILE__);
chdir($pluginRoot);

$argv = $_SERVER['argv'] ?? [];
$noComposer = in_array('--no-composer', $argv, true);
$noNpm = in_array('--no-npm', $argv, true);
$cleanupRelease = in_array('--cleanup-release', $argv, true);

$buildCommands = [];

if (file_exists('composer.json') && ! $noComposer) {
    $buildCommands[] = 'composer install --prefer-dist --no-progress --no-interaction --no-dev';
}

if (file_exists('package.json') && ! $noNpm) {
    if (file_exists('package-lock.json')) {
        $buildCommands[] = 'npm ci --no-progress --no-audit';
    } else {
        $buildCommands[] = 'npm install --no-progress --no-audit';
    }
    $buildCommands[] = 'npm run build';
}

$dirName = basename($pluginRoot);

foreach ($buildCommands as $buildCommand) {
    echo "---- Running build command '{$buildCommand}' for {$dirName}. ----\n";
    $timeStart = microtime(true);
    $exitCode = executeCommand($buildCommand);
    $buildTime = (int) round(microtime(true) - $timeStart);
    echo "---- Done '{$buildCommand}' for {$dirName} ({$buildTime}s). ----\n\n";
    if ($exitCode > 0) {
        exit($exitCode);
    }
}

if ($cleanupRelease) {
    $removables = [
        'node_modules',
        'build.php',
        '.github',
        '.gitattributes',
        '.gitignore',
        '.npmrc',
        'package-lock.json',
        'package.json',
        'vite.config.mjs',
        'source/sass',
        '.editorconfig',
    ];

    foreach ($removables as $path) {
        $full = $pluginRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (file_exists($full)) {
            echo "Removing {$path} from {$dirName}\n";
            if (is_dir($full)) {
                removeDirectory($full);
            } else {
                @unlink($full);
            }
        }
    }
}

/**
 * @return int Exit code (0 = success).
 */
function executeCommand(string $command): int
{
    $fullCommand = '';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $fullCommand = "cmd /v:on /c \"{$command} 2>&1 & echo Exit status : !ErrorLevel!\"";
    } else {
        $fullCommand = "{$command} 2>&1 ; echo Exit status : $?";
    }

    $proc = popen($fullCommand, 'r');
    if ($proc === false) {
        return 1;
    }

    $completeOutput = '';
    while (! feof($proc)) {
        $chunk = fread($proc, 4096);
        if ($chunk !== false) {
            $completeOutput .= $chunk;
            echo $chunk;
        }
        @flush();
    }

    pclose($proc);

    preg_match('/[0-9]+$/', $completeOutput, $matches);

    return isset($matches[0]) ? (int) $matches[0] : 1;
}

function removeDirectory(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? removeDirectory($path) : @unlink($path);
    }
    @rmdir($dir);
}
