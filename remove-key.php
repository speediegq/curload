<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "create-table.php";

if (isset($_REQUEST['key'])) {
    $Key = $_REQUEST['key'];
} else {
    print "No key specified.";
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

$Database = createTables($sqlDB);

// check if the key we passed is an admin key and if it's a primary admin key
$DatabaseQuery = $Database->query('SELECT * FROM admins');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($Key == $line['key']) {
        if ($line['primaryadmin'] == 1) {
            $AdminIsPrimary = 1;
        }

        $AuthorizedRemoval = 1;
        break;
    }
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

if ($AuthorizedRemoval != 1) {
    print "You aren't authorized to perform this action.";
    die();
}

?>
