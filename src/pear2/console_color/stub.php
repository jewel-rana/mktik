<?php

/**
 * Stub for PEAR2_Console_Color.
 * 
 * PHP version 5.3
 *
 * @category Console
 * @package  PEAR2_Console_Color
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version  GIT: $Id$
 * @link     http://pear2.php.net/PEAR2_Console_Color
 */

if (count(get_included_files()) > 1) {
    Phar::mapPhar();

    include_once 'phar://' . __FILE__ . DIRECTORY_SEPARATOR
        . '@PACKAGE_NAME@-@PACKAGE_VERSION@' . DIRECTORY_SEPARATOR
        . 'src' . DIRECTORY_SEPARATOR
        . 'PEAR2' . DIRECTORY_SEPARATOR
        . 'Autoload.php';
    return;
}

$isNotCli = PHP_SAPI !== 'cli';
if ($isNotCli) {
    header('Content-Type: text/plain;charset=UTF-8');
}
echo "@PACKAGE_NAME@ @PACKAGE_VERSION@\n";

if (version_compare(phpversion(), '5.3.0', '<')) {
    echo "\nERROR: This package requires PHP 5.3.0 or later.\n";
    exit(1);
}

$missing_extensions = array();
foreach (array('spl') as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}
if ($missing_extensions) {
    echo "\nERROR: You must compile PHP with the following extensions enabled:\n",
        implode(', ', $missing_extensions), "\n",
        "or install the necessary extensions for your distribution.\n";
    exit(2);
}

$supportsPhar = extension_loaded('phar');
if ($supportsPhar) {
    try {
        $phar = new Phar(__FILE__);
        $sig = $phar->getSignature();
        echo "{$sig['hash_type']} hash: {$sig['hash']}\n";
    } catch (Exception $e) {
        echo <<<HEREDOC

The PHAR extension is available, but was unable to read this PHAR file's hash.

HEREDOC;
        if (false !== strpos($e->getMessage(), 'file extension')) {
            echo <<<HEREDOC
This can happen if you've renamed the file to ".php" instead of ".phar".
Regardless, you should be able to include this file without problems.

HEREDOC;
        }
    }
} else {
    echo <<<HEREDOC

WARNING: If you wish to use this package directly from this archive, you need
         to install and enable the PHAR extension. Otherwise, you must instead
         extract this archive, and include the autoloader.

HEREDOC;
}

echo "\n" . str_repeat('=', 80) . "\n";
if ($isNotCli) {
    echo <<<HEREDOC
This package is not useful under this SAPI.
Rerun this file from the command line to see if you can use it there.

HEREDOC;
} else {
    echo <<<HEREDOC
\033[30;42m If you are reading this on a green background (and black text), then \033[0m
\033[30;42m this terminal supports color escape sequences, i.e. this package would \033[0m
\033[30;42m actually be useful in this environment. \033[0m

\033[32;44m In case your default background color is green, here's also a test \033[0m
\033[32;44m with a blue background (and green text). \033[0m

If above you see something like "[30;42m", then this terminal does not support
color escape sequences.

HEREDOC;

    if (substr(PHP_OS, 0, 3) === 'WIN') {
        echo "\n" . str_repeat('=', 80) . "\n";
        echo <<<HEREDOC
To add support for color escape sequences to Windows' command prompt,
install ANSICON.
See http://adoxa.hostmyway.net/ansicon/ or https://github.com/adoxa/ansicon.

HEREDOC;
        if (false !== getenv('ANSICON_VER')) {
            echo <<<HEREDOC

The presense of the "ANSICON_VER" environment variable suggests ANSICON is
already installed. If you are seeing "[30;42m" despite that, then ANSICON is
not really installed, and the command prompt is only pretending that it is.

HEREDOC;
        }
    }
}

__HALT_COMPILER();