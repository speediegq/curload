<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Username = "";
$Password = "";
$id = 0;

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    $Username = $_SESSION['username'];
    $Password = $_SESSION['password'];
} else {
    print "Username and password must be specified.";
    die();
}

$Authorized = 0;

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM users');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['username'] == $Username && $Username != "" && $line['password'] != "" && $Password == $line['password']) {
        $id = $line['id'];
        $Authorized = 1;

        break;
    }
}

if ($Authorized == 0) {
    die();
}

// Do whatever the fuck you want here
?>
