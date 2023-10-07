<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Username = "";
$Password = "";
$CurUsername = "";
$CurPassword = "";
$Action = "";
$ID = 0;
$Primary = 0;
$IsCurrentUser = false;

// make sure a username and password is specified for authentication
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    $Username = $_SESSION['username'];
    $Password = $_SESSION['password'];
} else {
    header("Location: /");
    die();
}

if (isset($_REQUEST['id'])) {
    $ID = $_REQUEST['id'];
} else {
    $ID = -1; // use the username and password to determine
}

// action
if (isset($_REQUEST['action'])) {
    $Action = $_REQUEST['action'];
} else {
    header("Location: /");
    die();
}

$Authorized = 0;

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM users');

// check permissions
while ($line = $DatabaseQuery->fetchArray()) {
    if ($ID == -1 && $line['username'] == $Username && $Username != "" && $line['password'] != "" && $Password == $line['password']) {
        $ID = $line['id'];
        $Authorized = 1;
        $IsCurrentUser = true;
        $CurUsername = $line['username'];
        $CurPassword = $line['password'];

        break;
    } else if ($line['username'] == $Username && $Username != "" && $line['password'] != "" && $Password == $line['password']) { // We're logged into an admin account
        $UserDatabaseQuery = $Database->query('SELECT * FROM users');
        $Primary = $line['primaryadmin'];

        while ($uline = $UserDatabaseQuery->fetchArray()) {
            if ($ID == $uline['id'] && ($Primary && $uline['usertype'] == 2 || $uline['usertype'] != 2)) {
                $CurUsername = $uline['username'];
                $CurPassword = $uline['password'];
                $Authorized = 1;
                break;
            }
        }
    }
}

if ($Authorized == 0) {
    header("Location: /");
    die();
}

// perform the action
if ($Action == "pass" && ($allowPasswordChange || !$IsCurrentUser)) {
    if (!isset($_REQUEST['newpass']) || !isset($_REQUEST['newpassc'])) {
        header("Location: /");
        die();
    }

    if ($_REQUEST['newpass'] != $_REQUEST['newpassc']) {
        header("Location: /");
        die();
    }

    $NewPassword = htmlspecialchars(generatePassword($_REQUEST['newpass']));

    if (!password_verify($_REQUEST['curpass'], $CurPassword) && $IsCurrentUser) {
        header("Location: /");
        die();
    }

    $Database->exec("UPDATE users SET password='$NewPassword' WHERE id='$ID'");
} else if ($Action == "username" && ($allowUsernameChange || !$IsCurrentUser)) {
    if (!isset($_REQUEST['newusername'])) {
        header("Location: /");
        die();
    }

    if (!isset($_REQUEST['curusername']) && $IsCurrentUser) {
        header("Location: /");
        die();
    }

    $NewUsername = htmlspecialchars($_REQUEST['newusername']);

    if ($CurUsername != $_REQUEST['curusername'] && $IsCurrentUser) {
        header("Location: /");
        die();
    }

    // make sure no duplicates can exist
    $UserDatabaseQuery = $Database->query('SELECT * FROM users');
    while ($uline = $UserDatabaseQuery->fetchArray()) {
        if ($uline['username'] == $NewUsername) {
            header("Location: /");
            die();
            break;
        }
    }

    // change it
    $Database->exec("UPDATE users SET username='$NewUsername' WHERE id='$ID'");
    $Database->exec("UPDATE uploads SET username='$NewUsername' WHERE username='$CurUsername'");
} else {
    header("Location: /");
    die();
}

if ($IsCurrentUser) {
    header('Location: login.php?logout=true');
    die();
}
?>
