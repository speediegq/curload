<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "core.php";
include "config.php";

// fields
$Username = "";
$Password = "";
$lastUsed = "";
$Issued = "";
$ip = "";
$userAgent = "";

$Redirect = "";
$uploadsLeft = 1;
$AuthorizedCreation = 0;
$AdminIsPrimary = 0;
$firstUser = 0;
$typeNum = 1;
$numberOfUploads = 0;

if (isset($_REQUEST['redir'])) {
    $Redirect = $_REQUEST['redir'];
}

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM users');

if (!checkIfAdminExists()) {
    $firstUser = 1;
} else {
    if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['type'])) {
        header('Location: login.php?redir=admin');
        die();
    } else if ($_SESSION['type'] != 2) { // not allowed
        header('Location: /');
        die();
    }

    $firstUser = 0;
}

$DatabaseQuery = $Database->query('SELECT * FROM users');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['username'] == $_SESSION['username'] && $_SESSION['username'] != "" && $line['password'] == $_SESSION['password']) {
        $AuthorizedCreation = 1;
        $AdminIsPrimary = $line['primaryadmin'];
        break;
    }
}

// not authorized
if ($AuthorizedCreation != 1 && $firstUser != 1) {
    header('Location: /');
    die();
}

// username must be specified
if (isset($_REQUEST['username']) && $_REQUEST['username'] != "") {
    $Username = $_REQUEST['username'];
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=username");
    } else if ($Redirect == "setup") {
        header("Location: setup.php?e=username");
    } else {
        header("Location: /");
    }

    die();
}

// password must be specified
if (isset($_REQUEST['password']) && ($_REQUEST['password'] != "" && $firstUser == 1 || $firstUser != 1)) {
    $Password = generatePassword($_REQUEST['password']);
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=password");
    } else if ($Redirect == "setup") {
        header("Location: setup.php?e=password");
    } else {
        header("Location: /");
    }

    die();
}

// type must be specified
if (isset($_REQUEST['type']) && $_REQUEST['type'] != "") {
    $Type = $_REQUEST['type'];
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=type");
    } else if ($Redirect == "setup") {
        header("Location: setup.php?e=type");
    } else {
        header("Location: /");
    }

    die();
}

// uploads left must be specified for temp users
if (isset($_REQUEST['uploadsleft']) && $Type == "Temporary") {
    $uploadsLeft = $_REQUEST['uploadsleft'];

    if ($uploadsLeft == 0 || !isset($_REQUEST['uploadsleft'])) {
        if ($Redirect == "admin") {
            header("Location: admin.php?action=create&e=uploads");
        } else if ($Redirect == "uploads") {
            header("Location: setup.php?e=type");
        } else {
            header("Location: /");
        }

        die();
    }
} else {
    $uploadsLeft = -1;
}

// only primary admins may create admin users
if ($AdminIsPrimary != 1 && $firstUser != 1 && $Type == "Admin") {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=denied");
    } else if ($Redirect == "setup") {
        header("Location: setup.php?e=denied");
    } else {
        header("Location: /");
    }

    die();
}

// check if a user by the same name already exists
$DatabaseQuery = $Database->query('SELECT * FROM users');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['username'] == "$Username" && $Username != "" && $line['username'] != "") {
        if ($Redirect == "admin") {
            header("Location: admin.php?action=create&e=exists");
        } else if ($Redirect == "setup") {
            header("Location: setup.php?e=exists");
        } else {
            header("Location: /");
        }

        die();
    }
}

if ($storeAgent || $storeAgent == "true") $userAgent = getUserAgent();
if ($storeIssued || $storeIssued == "true") $Issued = date($dateFormat);
if ($storeLastUsage || $storeLastUsage == "true") $lastUsed = date($dateFormat);
if ($storeIP || $storeIP == "true") $ip = getIPAddress();

if ($Type == "Admin") {
    $typeNum = 2;
} else {
    $typeNum = 1;
}

$Database->exec("INSERT INTO users(username, password, usertype, primaryadmin, numberofuploads, uploadsleft, lastused, issued, ip, useragent) VALUES('$Username', '$Password', '$typeNum', '$firstUser', '$numberOfUploads', '$uploadsLeft', '$lastUsed', '$Issued', '$ip', '$userAgent')");

if ($Redirect == "admin") {
    header("Location: admin.php?action=users");
} else {
    header("Location: /");
}

?>
