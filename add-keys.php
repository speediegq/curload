<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

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

// TODO: Hash passwords
function addKey($adminKey, $Value) {
    include "config.php";

    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM admins');
    $Authorized = 0;

    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $adminKey && $adminKey != "" && $line['key'] != "") {
            $Authorized = 1;
            break;
        }
    }

    // Make sure no existing key exists with that value
    $DatabaseQuery = $Database->query('SELECT * FROM keys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == "$Value") {
            print "A key with that value already exists.";
            die();
        }
    }

    if ($Authorized != 1) {
        print "You are not authorized to perform this action.";
        die();
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

    $Database->exec("INSERT INTO keys(key, numberofuploads, lastused, issued, ip, useragent) VALUES('$Value', '$numberOfUploads', '$lastUsed', '$Issued', '$ip', '$userAgent')");
}

function addTempKey($adminKey, $Value, $uploadsLeft) {
    include "config.php";

    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM admins');
    $Authorized = 0;

    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $adminKey && $adminKey != "" && $line['key'] != "") {
            $Authorized = 1;
            break;
        }
    }

    // Make sure no existing key exists with that value
    $DatabaseQuery = $Database->query('SELECT * FROM tkeys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == "$Value") {
            print "A key with that value already exists.";
            die();
        }
    }

    if ($Authorized != 1) {
        print "You are not authorized to perform this action.";
        die();
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

    $Database->exec("INSERT INTO tkeys(key, numberofuploads, uploadsleft, lastused, issued, ip, useragent) VALUES('$Value', '$numberOfUploads', '$uploadsLeft', '$lastUsed', '$Issued', '$ip', '$userAgent')");
}

function addAdminKey($adminKey, $Value, $Primary) {
    include "config.php";

    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM admins');
    $Authorized = 0;

    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $adminKey && $adminKey != "" && $line['key'] != "" && $line['primaryadmin'] == 1) {
            $Authorized = 1;
            break;
        }
    }

    // Make sure no existing key exists with that value
    $DatabaseQuery = $Database->query('SELECT * FROM admins');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == "$Value") {
            print "A key with that value already exists.";
            die();
        }
    }

    if ($Authorized != 1) {
        print "You are not authorized to perform this action.";
        die();
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

    $Database->exec("INSERT INTO admins(key, primaryadmin, numberofuploads, lastused, issued, ip, useragent) VALUES('$Value', '$Primary', '$numberOfUploads', '$lastUsed', '$Issued', '$ip', '$userAgent')");
}
?>
