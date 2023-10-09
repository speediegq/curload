<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['type'])) {
    header('Location: login.php');
    die();
} else if ($_SESSION['type'] != 2 && (!$enableUserUploadRemoval || $enableUserUploadRemoval == "false")) { // not allowed
    header('Location: /');
    die();
}

if (isset($_REQUEST['id'])) {
    $fileID = htmlspecialchars($_REQUEST['id']);
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
    $Redirect = htmlspecialchars($_REQUEST['redir']);
}

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM uploads');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $fileID) { // passed ID is a file that exists

        // check if our user is authorized to remove the file
        if ($enableUserUploadRemoval || $enableUserUploadRemoval == "true") {
            $userDatabaseQuery = $Database->query('SELECT * FROM users');

            while ($kline = $userDatabaseQuery->fetchArray()) {
                if ($line['username'] == $kline['username'] && $_SESSION['username'] == $kline['username'] && $_SESSION['password'] == $kline['password']) {
                    $AuthorizedRemoval = 1;
                    break;
                }
            }
        }

        // check if the user is an admin, automatically making it authorized to remove the file provided it wasn't uploaded by a primary admin
        if ($AuthorizedRemoval != 1 && ($enableUploadRemoval || $enableUploadRemoval == "true")) {
            $userDatabaseQuery = $Database->query('SELECT * FROM users');

            // check if the file was uploaded by a primary admin
            while ($kline = $userDatabaseQuery->fetchArray()) {
                if ($kline['username'] == $line['username']) {
                    $fileUploadedByPrimary = $kline['primaryadmin'];
                }
            }

            while ($kline = $userDatabaseQuery->fetchArray()) {
                if ($kline['username'] == $_SESSION['username'] && $_SESSION['username'] != "" && $kline['password'] == $_SESSION['password'] && $kline['usertype'] == 2) {
                    if (($fileUploadedByPrimary == 1 && $kline['primaryadmin'] == 1) || ($fileUploadedByPrimary == 0)) {
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
    header("Location: /");
    die();
}

$Database->exec("DELETE FROM uploads WHERE id='$fileID'");
unlink(ltrim($FileToRemove, '/'));

if ($Redirect == "admin") {
    header("Location: admin.php?action=files");
} else if ($Redirect == "files") {
    header("Location: files.php");
} else {
    header("Location: /");
}

?>
