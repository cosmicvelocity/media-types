#!/usr/bin/env php
<?php

namespace CosmicVelocity\MediaType;

use Exception;

try {
    if (2 <= $argc) {
        $url = $argv[1];
    } else {
        $url = 'https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
    }

    $lines = file($url);

    if ($lines === false) {
        throw new Exception("Failed to read from URL ({$url}).", 2);
    }

    $duplicateExtensions = [
        'sub' => 'text/vnd.dvb.subtitle',
        'wmz' => 'application/x-msmetafile',
    ];
    $entries = [];

    foreach ($lines as $line) {
        if ($line{0} == '#') {
            continue;
        }

        $line = preg_replace("/\\s+/", ' ', trim($line));
        $parts = explode(' ', $line);
        $type = array_shift($parts);

        foreach ($parts as $extension) {
            if (isset($entries[$extension])) {
                if (isset($duplicateExtensions[$extension])) {
                    $entries[$extension] = $duplicateExtensions[$extension];
                } else {
                    $duplicateType = $entries[$extension];

                    throw new Exception(sprintf('Extension %s is a duplicate in %s and %s.', $extension, $type, $duplicateType), 1);
                }
            } else {
                $entries[$extension] = $type;
            }
        }
    }

    asort($entries);

    info('<?php');
    info('');
    info('return [');

    foreach ($entries as $extension => $type) {
        info(sprintf("    '%s' => '%s',", $extension, $type));
    }

    info('];');

    $exitCode = 0;
} catch (Exception $ex) {
    error($ex->getMessage());

    $exitCode = $ex->getCode();
}

exit($exitCode);

/**
 * Give line breaks and output to standard output.
 */
function info($message)
{
    echo $message . PHP_EOL;
}

/**
 * Give line breaks and output to standard error output.
 */
function error($message)
{
    file_put_contents('php://stderr', $message . PHP_EOL);
}
