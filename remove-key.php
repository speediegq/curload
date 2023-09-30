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
    $id = $_REQUEST['id'];
} else {
    print "No ID specified.";
    die();
}

$AdminIsPrimary = 0;
$KeyIsPrimary = 0;
$AuthorizedRemoval = 0;
$Removed = 0;
$Redirect = "";

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

$DatabaseQuery = $Database->query('SELECT * FROM keys');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $id && $line['id'] != "" && $id != "") { // passed ID is a key that exists
        if ($AuthorizedRemoval == 1) {
            $Database->exec("DELETE FROM keys WHERE id='$id'");
            $Removed = 1;
        } else {
            print "You aren't authorized to perform this action.";
            die();
        }

        break;
    }
}

$DatabaseQuery = $Database->query('SELECT * FROM tkeys');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $id && $line['id'] != "" && $id != "" && $Removed != 1) { // passed ID is a key that exists
        if ($AuthorizedRemoval == 1) {
            $Database->exec("DELETE FROM tkeys WHERE id='$id'");
            $Removed = 1;
        } else {
            print "You aren't authorized to perform this action.";
            die();
        }

        break;
    }
}

$DatabaseQuery = $Database->query('SELECT * FROM admins');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $id && $line['id'] != "" && $id != "" && $Removed != 1 && $line['primaryadmin'] != 1) { // passed ID is a key that exists
        if ($AuthorizedRemoval == 1 && $AdminIsPrimary == 1) { // in order to delete an admin key you must be a primary admin
            $Database->exec("DELETE FROM admins WHERE id='$id'");
            $Removed = 1;
        } else {
            print "You aren't authorized to perform this action.";
            die();
        }

        break;
    }
}

if ($Redirect == "admin") {
    header("Location: admin.php?action=keys");
} else {
    header("Location: /");
}

?>
