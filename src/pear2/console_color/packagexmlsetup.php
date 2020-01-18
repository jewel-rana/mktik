<?php

/**
 * packagexmlsetup.php for PEAR2_Console_Color.
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

/**
 * References the package in $package
 */
use Pyrus\Developer\PackageFile\v2;

$packageGen = function (
    v2 $package,
    v2 $compatible = null
) {
    $srcDirTask = array(
        'tasks:replace' => array(
            array(
                'attribs' => array(
                    'from' => '../src',
                    'to' => 'php_dir',
                    'type' => 'pear-config'
                )
            )
        )
    );
    $srcFileTasks = array(
        'tasks:replace' => array(
            array(
                'attribs' => array(
                    'from' => 'GIT: $Id$',
                    'to' => 'version',
                    'type' => 'package-info'
                )
            )
        )
    );

    $hasCompatible = null !== $compatible;
    if ($hasCompatible) {
        $compatible->license = $package->license;
        $compatible->files[
            "test/{$package->channel}/{$package->name}/bootstrap.php"
            ] = array_merge_recursive(
                $compatible->files[
                "test/{$package->channel}/{$package->name}/bootstrap.php"
                ]->getArrayCopy(),
                $srcDirTask
            );
        $compatible->files[
            "test/{$package->channel}/{$package->name}/phpunit.xml"
            ] = array_merge_recursive(
                $compatible->files[
                "test/{$package->channel}/{$package->name}/phpunit.xml"
                ]->getArrayCopy(),
                $srcDirTask
            );
    }

    $package->files['tests/bootstrap.php'] = array_merge_recursive(
        $package->files['tests/bootstrap.php']->getArrayCopy(),
        $srcDirTask
    );

    $package->files['tests/phpunit.xml'] = array_merge_recursive(
        $package->files['tests/phpunit.xml']->getArrayCopy(),
        $srcDirTask
    );

    $oldCwd = getcwd();
    chdir(__DIR__);
    foreach (new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            'src',
            RecursiveDirectoryIterator::UNIX_PATHS
            | RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::LEAVES_ONLY
    ) as $path) {
            $filename = $path->getPathname();

            $package->files[$filename] = array_merge_recursive(
                $package->files[$filename]->getArrayCopy(),
                $srcFileTasks
            );

        if ($hasCompatible) {
            $compatibleFilename = str_replace('src/', 'php/', $filename);
            $compatible->files[$compatibleFilename] = array_merge_recursive(
                $compatible->files[$compatibleFilename]->getArrayCopy(),
                $srcFileTasks
            );
        }
    }

    foreach (new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            '.',
            RecursiveDirectoryIterator::UNIX_PATHS
            | RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::LEAVES_ONLY
    ) as $path) {
            $filename = substr($path->getPathname(), 2);

        if (isset($package->files[$filename])) {
            $as = (strpos($filename, 'examples') === 0)
                ? $filename
                : substr($filename, strpos($filename, '/') + 1);
            $package->getReleaseToInstall('php')->installAs($filename, $as);
        }
    }
    chdir($oldCwd);
    return array($package, $compatible);
};

list($package, $compatible) = $packageGen(
    $package,
    isset($compatible) ? $compatible : null
);
if (null === $compatible) {
    unset($compatible);
}
