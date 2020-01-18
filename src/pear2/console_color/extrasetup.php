<?php

/**
 * extrasetup.php for PEAR2_Console_Color.
 * 
 * PHP version 5.3
 *
 * @category Console
 * @package  PEAR2_Console_Color
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version  GIT: $Id$
 * @link     http://pear2.php.net/PEAR2_Console_Color
 */

$extrafiles = array();
$phpDir = Pyrus\Config::current()->php_dir;
$packages = array('PEAR2/Autoload');

$oldCwd = getcwd();
chdir($phpDir);
foreach ($packages as $pkg) {
    if (is_dir($pkg)) {
        foreach (new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $pkg,
                RecursiveDirectoryIterator::UNIX_PATHS
                | RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::LEAVES_ONLY
        ) as $path) {
            $extrafiles['src/' . $path->getPathname()] = $path->getRealPath();
        }
    }

    if (is_file($pkg . '.php')) {
        $extrafiles['src/' . $pkg . '.php']
            = $phpDir . DIRECTORY_SEPARATOR . $pkg . '.php';
    }
}
chdir($oldCwd);
