<?php
    include "config.php";
    include "create-table.php";

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
    $keyID = 0;
    $self = dirname($_SERVER['PHP_SELF']);

    if (!isset($_FILES['file']['name'])) {
        print "You didn't specify a file.";
        die();
    }

    // init database
    if ($sql == "true" || $sql) {
        $Database = createTables($sqlDB);

        $DatabaseQuery = $Database->query('SELECT * FROM keys');
        while ($line = $DatabaseQuery->fetchArray()) {
            if ($line['key'] == $Key && $Key != "" && $line['key'] != "") {
                $id = $line['id'];
                $keyID = $id;
                $numberOfUploads = $line['numberofuploads'] + 1;
                    $lastUsed = date($dateFormat);

                $Database->exec("UPDATE keys SET lastused='$lastUsed' WHERE id='$id'");
                $Database->exec("UPDATE keys SET numberofuploads='$numberOfUploads' WHERE id='$id'");

                if ($storeIP || $storeIP == "true") {
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    $Database->exec("UPDATE keys SET ip='$ip' WHERE id='$id'");
                }

                if ($storeAgent || $storeAgent == "true") {
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    $Database->exec("UPDATE keys SET useragent='$userAgent' WHERE id='$id'");
                }

                $Authorized = 1;
                $tempKeyUsed = 0;
                break;
            }
        }

        if ($Authorized != 1) {
            $DatabaseQuery = $Database->query('SELECT * FROM tkeys');
            while ($line = $DatabaseQuery->fetchArray()) {
                if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && $line['uploadsleft'] != 0) {
                    $uploadsLeft = $line['uploadsleft'] - 1;
                    $numberOfUploads = $line['numberofuploads'] + 1;
                    $lastUsed = date($dateFormat);
                    $id = $line['id'];
                    $keyID = $id;

                    $Database->exec("UPDATE tkeys SET uploadsleft='$uploadsLeft' WHERE id='$id'");
                    $Database->exec("UPDATE tkeys SET lastused='$lastUsed' WHERE id='$id'");
                    $Database->exec("UPDATE tkeys SET numberofuploads='$numberOfUploads' WHERE id='$id'");

                    if ($storeIP || $storeIP == "true") {
                        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                            $ip = $_SERVER['HTTP_CLIENT_IP'];
                        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        } else {
                            $ip = $_SERVER['REMOTE_ADDR'];
                        }

                        $Database->exec("UPDATE tkeys SET ip='$ip' WHERE id='$id'");
                    }

                    if ($storeAgent || $storeAgent == "true") {
                        $userAgent = $_SERVER['HTTP_USER_AGENT'];
                        $Database->exec("UPDATE tkeys SET useragent='$userAgent' WHERE id='$id'");
                    }

                    $Authorized = 1;
                    $tempKeyUsed = 1;
                    break;
                }
            }
        }
    } else { // no sql version
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
        $fileExtension = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
        if (isset($fileExtension)) {
            $extension = "." . $fileExtension;
        }
        $destinationFile = $uploadDir . rand(1000,100000) . $extension;

        if (file_exists($destinationFile)) { // wtf
            print "Failed to upload file.";
            die();
        }
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $destinationFile)) {
        $uploadedFile = dirname($_SERVER['PHP_SELF']) . $destinationFile;

        if ($sql || $sql == "true") {
            $lastUsed = date($dateFormat);
            $DatabaseQuery = $Database->query('SELECT * FROM uploads');
            $Database->exec("INSERT INTO uploads(file, uploaddate, keyid, tempkey) VALUES('$uploadedFile', '$lastUsed', '$keyID', '$tempKeyUsed')");
        }

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
?>
