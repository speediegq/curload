<?php
    include "create-table.php";

    function getIPAddress() {
        include "config.php";

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
        $DatabaseQuery = $Database->query('SELECT * FROM keys');

        $numberOfUploads = 0;
        $lastUsed = date($dateFormat);
        $Issued = date($dateFormat);
        $ip = "";
        $userAgent = "";

        if ($storeAgent || $storeAgent == "true") {
            $userAgent = getUserAgent();
        }

        if ($storeIP || $storeIP == "true") {
            $ip = getIPAddress();
        }

        $Database->exec("INSERT INTO keys(key, numberofuploads, lastused, issued, ip, useragent) VALUES('$Value', '$numberOfUploads', '$lastUsed', '$Issued', '$ip', '$userAgent')");
    }

    function addTempKey($adminKey, $Value, $uploadsLeft) {
        include "config.php";

        $Database = createTables($sqlDB);
        $DatabaseQuery = $Database->query('SELECT * FROM tkeys');

        $numberOfUploads = 0;
        $lastUsed = date($dateFormat);
        $Issued = date($dateFormat);
        $ip = "";
        $userAgent = "";

        if ($storeAgent || $storeAgent == "true") {
            $userAgent = getUserAgent();
        }

        if ($storeIP || $storeIP == "true") {
            $ip = getIPAddress();
        }

        if ($storeAgent || $storeAgent == "true") {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        $Database->exec("INSERT INTO tkeys(key, numberofuploads, uploadsleft, lastused, issued, ip, useragent) VALUES('$Value', '$numberOfUploads', '$uploadsLeft', '$lastUsed', '$Issued', '$ip', '$userAgent')");
    }

    // TEMPORARY FUNCTION: TO BE REMOVED
    function addAdminKey($Value) {
        include "config.php";

        $Database = createTables($sqlDB);
        $DatabaseQuery = $Database->query('SELECT * FROM admins');

        $lastUsed = date($dateFormat);
        $Issued = date($dateFormat);
        $ip = "";
        $userAgent = "";

        if ($storeAgent || $storeAgent == "true") {
            $userAgent = getUserAgent();
        }

        if ($storeIP || $storeIP == "true") {
            $ip = getIPAddress();
        }

        if ($storeAgent || $storeAgent == "true") {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        $Database->exec("INSERT INTO admins(id, key, lastused, issued, ip, useragent) VALUES('$Value', '$lastUsed', '$Issued', '$ip', '$userAgent')");
    }
?>
