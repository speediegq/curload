<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "create-table.php";

function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'];
}

$Redirect = "";
$uploadsLeft = 1;
$AuthorizedRemoval = 0;
$AdminIsPrimary = 0;

if (isset($_REQUEST['redir'])) {
    $Redirect = $_REQUEST['redir'];
}

if (!isset($_COOKIE[$cookieName]) || !isset($_COOKIE[$cookieTypeName])) {
    header('Location: login.php?redir=admin');
    die();
} else if ($_COOKIE[$cookieTypeName] != 2) { // not allowed
    header('Location: /');
    die();
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

if (isset($_REQUEST['data']) && $_REQUEST['data'] != "") {
    $Data = $_REQUEST['data'];
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=data");
    } else {
        header("Location: /");
    }

    die();
}

if (isset($_REQUEST['type']) && $_REQUEST['type'] != "") {
    $Type = $_REQUEST['type'];
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=type");
    } else {
        header("Location: /");
    }

    die();
}

if (isset($_REQUEST['uploads']) && $Type == "Temporary") {
    $Uploads = $_REQUEST['uploads'];
} else {
    $Uploads = 1;
}

if (isset($_REQUEST['uploadsleft']) && $Type == "Temporary") {
    $uploadsLeft = $_REQUEST['uploadsleft'];
}

if (($_REQUEST['uploadsleft'] == 0 || !isset($_REQUEST['uploadsleft'])) && $Type == "Temporary") {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=uploads");
    } else {
        header("Location: /");
    }

    die();
}

if ($Type == "Admin") {
    if ($AdminIsPrimary != 1) {
        if ($Redirect == "admin") {
            header("Location: admin.php?action=create&e=denied");
        } else {
            header("Location: /");
        }

        die();
    }

    $DatabaseQuery = $Database->query('SELECT * FROM admins');

    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == "$Data") {
            if ($Redirect == "admin") {
                header("Location: admin.php?action=create&e=exists");
            } else {
                header("Location: /");
            }

            die();
        }
    }

    $numberOfUploads = 0;
    $lastUsed = "";
    $Issued = "";
    $ip = "";
    $userAgent = "";

    if ($storeAgent || $storeAgent == "true") {
        $userAgent = getUserAgent();
    }

    if ($storeIssued || $storeIssued == "true") {
        $Issued = date($dateFormat);
    }

    if ($storeLastUsage || $storeLastUsage == "true") {
        $lastUsed = date($dateFormat);
    }

    if ($storeIP || $storeIP == "true") {
        $ip = getIPAddress();
    }

    $Database->exec("INSERT INTO admins(key, primaryadmin, numberofuploads, lastused, issued, ip, useragent) VALUES('$Data', '0', '$numberOfUploads', '$lastUsed', '$Issued', '$ip', '$userAgent')");
} else if ($Type == "Temporary") {
    $DatabaseQuery = $Database->query('SELECT * FROM tkeys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == "$Data") {
            if ($Redirect == "admin") {
                header("Location: admin.php?action=create&e=exists");
            } else {
                header("Location: /");
            }

            die();
        }
    }

    $numberOfUploads = 0;
    $lastUsed = "";
    $Issued = "";
    $ip = "";
    $userAgent = "";

    if ($storeAgent || $storeAgent == "true") {
        $userAgent = getUserAgent();
    }

    if ($storeIssued || $storeIssued == "true") {
        $Issued = date($dateFormat);
    }

    if ($storeLastUsage || $storeLastUsage == "true") {
        $lastUsed = date($dateFormat);
    }

    if ($storeIP || $storeIP == "true") {
        $ip = getIPAddress();
    }

    $Database->exec("INSERT INTO tkeys(key, numberofuploads, uploadsleft, lastused, issued, ip, useragent) VALUES('$Data', '$numberOfUploads', '$uploadsLeft', '$lastUsed', '$Issued', '$ip', '$userAgent')");
} else if ($Type == "Key") {
    $DatabaseQuery = $Database->query('SELECT * FROM keys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == "$Data") {
            if ($Redirect == "admin") {
                header("Location: admin.php?action=create&e=exists");
            } else {
                header("Location: /");
            }

            die();
        }
    }

    $numberOfUploads = 0;
    $lastUsed = "";
    $Issued = "";
    $ip = "";
    $userAgent = "";

    if ($storeAgent || $storeAgent == "true") {
        $userAgent = getUserAgent();
    }

    if ($storeIssued || $storeIssued == "true") {
        $Issued = date($dateFormat);
    }

    if ($storeLastUsage || $storeLastUsage == "true") {
        $lastUsed = date($dateFormat);
    }

    if ($storeIP || $storeIP == "true") {
        $ip = getIPAddress();
    }

    $Database->exec("INSERT INTO keys(key, numberofuploads, lastused, issued, ip, useragent) VALUES('$Data', '$numberOfUploads', '$lastUsed', '$Issued', '$ip', '$userAgent')");
} else {
    if ($Redirect == "admin") {
        header("Location: admin.php?action=create&e=type");
    } else {
        header("Location: /");
    }

    die();
}

if ($Redirect == "admin") {
    header("Location: admin.php?action=keys");
} else {
    header("Location: /");
}

?>
