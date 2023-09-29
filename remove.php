<?php
    include "config.php";
    include "create-table.php";

    if (isset($_REQUEST['key'])) {
        $Key = $_REQUEST['key'];
    } else {
        print "No key specified.";
        die();
    }

    // TODO: Functions that remove stuff
?>
