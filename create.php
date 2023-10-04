<?php session_start();
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "core.php";
include "config.php";

$Redirect = "";
$uploadsLeft = 1;
$AuthorizedCreation = 0;
$AdminIsPrimary = 0;
$firstKey = 0;
$typeNum = 1;
$numberOfUploads = 0;
$lastUsed = "";
$Issued = "";
$ip = "";
$userAgent = "";

if (isset($_REQUEST['redir'])) {
    $Redirect = $_REQUEST['redir'];
}

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM keys');

if (!checkIfAdminExists()) {
    $firstKey = 1;
} else {
    if (!isset($_SESSION['key']) || !isset($_SESSION['type'])) {
        header('Location: login.php?redir=admin');
        die();
    } else if ($_SESSION['type'] != 2) { // not allowed
        header('Location: /');
        die();
    }

    $firstKey = 0;
}

$DatabaseQuery = $Database->query('SELECT * FROM keys');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['key'] == $_SESSION['key'] && $_SESSION['key'] != "" && $line['key'] != "" && ($enableKeys || $enableKeys == "true")) {
        $AuthorizedCreation = 1;
        $AdminIsPrimary = $line['primaryadmin'];
        break;
    }
}

// not authorized
if ($AuthorizedCreation != 1 && $firstKey != 1) {
    header('Location: /');
    die();
}

// data must be specified
if (isset($_REQUEST['data']) && $_REQUEST['data'] != "") {
    $Data = $_REQUEST['data'];
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=data");
    } else if ($Redirect == "setup") {
        header("Location: setup.php?e=data");
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

// uploads left must be specified for temp keys
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

// only primary admins may create admin keys
if ($AdminIsPrimary != 1 && $firstKey != 1 && $Type == "Admin") {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=denied");
    } else if ($Redirect == "setup") {
        header("Location: setup.php?e=denied");
    } else {
        header("Location: /");
    }

    die();
}

// check if a key by the same name already exists
$DatabaseQuery = $Database->query('SELECT * FROM keys');
while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['key'] == "$Data" && $Data != "" && $line['key'] != "") {
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

$Database->exec("INSERT INTO keys(key, keytype, primaryadmin, numberofuploads, uploadsleft, lastused, issued, ip, useragent) VALUES('$Data', '$typeNum', '$firstKey', '$numberOfUploads', '$uploadsLeft', '$lastUsed', '$Issued', '$ip', '$userAgent')");

if ($Redirect == "admin") {
    header("Location: admin.php?action=keys");
} else {
    header("Location: /");
}

?>
