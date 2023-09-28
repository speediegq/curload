<?php
    include "config.php";

    if (isset($_REQUEST['key'])) {
        $Key = $_REQUEST['key'];
    } else {
        print "No key specified.";
        die();
    }

    $Status = 0;
    $Authorized = 0;
    $tempKeyUsed = 0;
    $uploadLimit = $maxFileSize * 1000000;
    $self = dirname($_SERVER['PHP_SELF']);

    if (isset($_FILES['file']['name'])) {
        // All normal keys will be considered valid
        if (file_exists($keyFile)) {
            $validKeys = explode("\n", file_get_contents($keyFile));
        } else { // one master key must exist
            print("Error: No valid keys found.");
            die();
        }

        foreach ($validKeys as $ValidKey) {
            if ($Key == $ValidKey && $Key != "" && $ValidKey != "") {
                $Authorized = 1;
                $tempKeyUsed = 0;

                break;
            }
        }

        // Temporary keys as well
        if (file_exists($tempKeyFile)) {
            $tempValidKeys = explode("\n", file_get_contents($tempKeyFile));

            foreach ($tempValidKeys as $ValidKey) {
                if ($Key == $ValidKey && $Key != "" && $ValidKey != "") {
                    $Authorized = 1;
                    $tempKeyUsed = 1; // key should be considered invalid after this use.

                    break;
                }
            }
        }

        // Not an authorized key
        if ($Authorized == 0) {
            print "Not authorized: Key '$Key' is invalid.";
            die();
        }

        if ($_FILES['file']['size'] > $uploadLimit) {
            print "File is too big. Max file size is $maxFileSize" . "MB";
            die();
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destinationFile = $uploadDir . basename($_FILES['file']['name']);

        if (file_exists($destinationFile)) { // rename file to distinguish it from existing file
            $destinationFile = $uploadDir . rand(10000,100000) . "." . strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));

            if (file_exists($destinationFile)) { // wtf
                print "Failed to upload file.";
                die();
            }
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $destinationFile)) {
            $uploadedFile = dirname($_SERVER['PHP_SELF']) . $destinationFile;

            if ($tempKeyUsed) { // Remove temporary key
                $file = file_get_contents($tempKeyFile);
                $file = preg_replace("/\b$Key\b/", "", $file);
                file_put_contents($tempKeyFile, $file);
            }

            print "$uploadedFile";

            if (isset($_REQUEST['web'])) { // redirect back to index
                print "<p><a href=\"$uploadedFile\">Your link</a></p>\n";
                die();
            }
        } else {
            print "Failed to upload file.";

            if ($_FILES['file']['error'] == 1) {
                print "Is the upload_max_filesize set up properly?";
            }
            die();
        }
    } else {
        print "You didn't specify a file.";
        die();
    }
?>
