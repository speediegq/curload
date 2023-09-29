<?php
    include "config.php";
    include "add-keys.php";

    if (isset($_REQUEST['key'])) {
        $Key = $_REQUEST['key'];
    } else {
        print "No admin key specified.";
        die();
    }

    if (isset($_REQUEST['data'])) {
        $Data = $_REQUEST['data'];
    } else {
        print "No data specified.";
        die();
    }

    if (isset($_REQUEST['type'])) {
        $Type = $_REQUEST['type'];
    } else {
        print "No type specified.";
        die();
    }

    if (isset($_REQUEST['uploads']) && $Type == "Temporary") {
        $Uploads = $_REQUEST['uploads'];
    } else {
        $Uploads = 1;
    }

    if ($Type == "Admin") {
        addAdminKey($Key, $Data, 0);
    } else if ($Type == "Temporary") {
        addTempKey($Key, $Data, $Uploads);
    } else if ($Type == "Key") {
        addKey($Key, $Data);
    } else {
        print "Invalid type specified.";
        die();
    }
?>
