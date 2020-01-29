@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../pear2/net_routeros/scripts/roscon.php
php "%BIN_TARGET%" %*
