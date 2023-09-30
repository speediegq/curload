<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "create-table.php";

if (!isset($_COOKIE[$cookieName]) || !isset($_COOKIE[$cookieTypeName])) {
    header('Location: login.php?redir=admin');
    die();
} else if ($_COOKIE[$cookieTypeName] != 2) { // not allowed
    header('Location: /');
    die();
}

if (isset($_REQUEST['id'])) {
    $fileID = $_REQUEST['id'];
} else {
    print "No ID specified.";
    die();
}

if (!$enableUploadRemoval || $enableUploadRemoval == "false") {
    print "Uploads cannot be removed.";
    die();
}

$Redirect = "";
$FileToRemove = "";
$AuthorizedRemoval = 0;
$fileUploadedByPrimary = 0;

if (isset($_REQUEST['redir'])) {
    $Redirect = $_REQUEST['redir'];
}

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM admins');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['key'] == $_COOKIE[$cookieName] && $_COOKIE[$cookieName] != "" && $line['key'] != "" && ($enableKeys || $enableKeys == "true")) {
        $AuthorizedRemoval = 1;
        $AdminIsPrimary = $line['primaryadmin'];
        break;
    }
}

// not authorized
if ($AuthorizedRemoval != 1) {
    header('Location: /');
    die();
}

$DatabaseQuery = $Database->query('SELECT * FROM uploads');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $fileID) { // passed ID is a file that exists

        // check if our key is authorized to remove the file
        if (($enableKeys || $enableKeys == "true") && ($enableKeyUploadRemoval || $enableKeyUploadRemoval == "true")) {
            $keyDatabaseQuery = $Database->query('SELECT * FROM keys');

            while ($kline = $keyDatabaseQuery->fetchArray()) {
                if ($line['keyid'] == $kline['id']) {
                    $AuthorizedRemoval = 1;
                    break;
                }

            }
        }

        // check if the key is an admin key, automatically making it authorized to remove the file provided it wasn't uploaded by a primary admin
        if ($AuthorizedRemoval != 1 && ($enableUploadRemoval || $enableUploadRemoval == "true")) {
            $keyDatabaseQuery = $Database->query('SELECT * FROM admins');

            // check if the file was uploaded by a primary admin
            while ($kline = $keyDatabaseQuery->fetchArray()) {
                if ($kline['key'] == $line['keyid']) {
                    $fileUploadedByPrimary = $kline['primaryadmin'];
                }
            }

            while ($kline = $keyDatabaseQuery->fetchArray()) {
                if ($kline['key'] == $Key && $Key != "" && $kline['key'] != "") { // key = passed key
                    if (($fileUploadedByPrimary == 1 && $kline['primaryadmin'] == 1) || ($fileUploadedByPrimary == 0)) { // primary key passed and primary file OR non primary file
                        $AuthorizedRemoval = 1;
                        break;
                    }
                }
            }
        }

        $FileToRemove = $line['file'];

        break;
    }
}

// fuck off pleb
if ($AuthorizedRemoval != 1) {
    print "You aren't authorized to perform this action.";
    die();
}

$Database->exec("DELETE FROM uploads WHERE id='$fileID'");
unlink(ltrim($FileToRemove, '/'));

if ($Redirect == "admin") {
    header("Location: admin.php?action=files");
} else {
    header("Location: /");
}

?>
