<?php

/**
 * bootstrap.php for PEAR2_Console_Color.
 * 
 * PHP version 5.3
 *
 * @category Console
 * @package  PEAR2_Console_Color
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version  GIT: $Id$
 * @link     http://pear2.php.net/PEAR2_Console_Color
 */

/**
 * Possible autoloader to initialize.
 */
use PEAR2\Autoload;

chdir(__DIR__);

$autoloader = stream_resolve_include_path('../vendor/autoload.php');
if (false !== $autoloader) {
    include_once $autoloader;
} else {
    $autoloader = stream_resolve_include_path('PEAR2/Autoload.php');
    if (false !== $autoloader) {
        include_once $autoloader;
        Autoload::initialize(realpath('../src'));
    } else {
        fwrite(STDERR, 'No recognized autoloader is available.');
        exit(1);
    }
}
unset($autoloader);
