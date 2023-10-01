<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "create-table.php";

$WebInterface = 1;

if (isset($_REQUEST['key'])) {
    $Key = $_REQUEST['key'];
    $WebInterface = 0;
} else if (isset($_COOKIE[$cookieName])) {
    $Key = $_COOKIE[$cookieName];
    $WebInterface = 1;
} else {
    print "No key specified.";
    die();
}

$Status = 0;
$Authorized = 0;
$keyType = 0;
$uploadLimit = $maxFileSize * 1000000;
$keyID = 0;
$self = dirname($_SERVER['PHP_SELF']);

if (!isset($_FILES['file']['name'])) {
    if ($WebInterface == 0) {
        print "You didn't specify a file.";
        die();
    } else {
        header("Location: /?e=file");
        die();
    }
}

// init database
if (!$publicUploading || $publicUploading == "false") {
    $Database = createTables($sqlDB);

    $DatabaseQuery = $Database->query('SELECT * FROM keys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && ($enableKeys || $enableKeys == "true")) {
            $id = $line['id'];
            $keyID = $id;

            if ($storeLastUsage || $storeLastUsage == "true") {
                $lastUsed = date($dateFormat);
                $Database->exec("UPDATE keys SET lastused='$lastUsed' WHERE id='$id'");
            }

            if ($storeUploads || $storeUploads == "true") {
                $numberOfUploads = $line['numberofuploads'] + 1;
                $Database->exec("UPDATE keys SET numberofuploads='$numberOfUploads' WHERE id='$id'");
            }

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
            $keyType = 0;
            break;
        }
    }

    if ($Authorized != 1) {
        $DatabaseQuery = $Database->query('SELECT * FROM tkeys');
        while ($line = $DatabaseQuery->fetchArray()) {
            if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && $line['uploadsleft'] != 0 && ($enableTemporaryKeys || $enableTemporaryKeys == "true")) {
                $uploadsLeft = $line['uploadsleft'] - 1;
                $id = $line['id'];
                $keyID = $id;

                $Database->exec("UPDATE tkeys SET uploadsleft='$uploadsLeft' WHERE id='$id'");

                if ($storeLastUsage || $storeLastUsage == "true") {
                    $lastUsed = date($dateFormat);
                    $Database->exec("UPDATE tkeys SET lastused='$lastUsed' WHERE id='$id'");
                }

                if ($storeUploads || $storeUploads == "true") {
                    $numberOfUploads = $line['numberofuploads'] + 1;
                    $Database->exec("UPDATE tkeys SET numberofuploads='$numberOfUploads' WHERE id='$id'");
                }

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
                $keyType = 1;
                break;
            }
        }
    }

    // maybe admin?
    if ($Authorized != 1) {
        $DatabaseQuery = $Database->query('SELECT * FROM admins');

        while ($line = $DatabaseQuery->fetchArray()) {
            if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && ($enableAdminKeys || $enableAdminKeys == "true")) {
                $id = $line['id'];
                $keyID = $id;
                $numberOfUploads = $line['numberofuploads'] + 1;
                $lastUsed = date($dateFormat);

                $Database->exec("UPDATE admins SET lastused='$lastUsed' WHERE id='$id'");
                $Database->exec("UPDATE admins SET numberofuploads='$numberOfUploads' WHERE id='$id'");

                if ($storeIP || $storeIP == "true") {
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    $Database->exec("UPDATE admins SET ip='$ip' WHERE id='$id'");
                }

                if ($storeAgent || $storeAgent == "true") {
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    $Database->exec("UPDATE admins SET useragent='$userAgent' WHERE id='$id'");
                }

                $Authorized = 1;
                $keyType = 2;
                break;
            }
        }
    }

    // Not an authorized key
    if ($Authorized == 0) {
        if ($WebInterface == 0) {
            print "Not authorized: Your key is invalid.";
            die();
        } else {
            header("Location: /?e=key");
            die();
        }
    }
}

if ($_FILES['file']['size'] > $uploadLimit && $uploadLimit > 0) {
    if ($WebInterface == 0) {
        print "File is too big. Max file size is $maxFileSize" . "MB";
        die();
    } else {
        header("Location: /?e=size");
        die();
    }
}

// check if file is too big to be uploaded
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$destinationFile = $uploadDir . basename($_FILES['file']['name']);

// rename file if necessary
if (!$replaceOriginal || $replaceOriginal == "false") {
    if (file_exists($destinationFile)) { // rename file to distinguish it from existing file
        $fileExtension = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
        if (isset($fileExtension)) {
            $extension = "." . $fileExtension;
        }

        if ($renameDuplicates || $renameDuplicates == "true") {
            $destinationFile = $uploadDir . rand(1000,100000) . $extension;
        }

        if (file_exists($destinationFile)) { // wtf
            if ($WebInterface == 0) {
                print "Upload failed.";
                die();
            } else {
                header("Location: /?e=wtf");
                die();
            }
        }
    }
}

if (move_uploaded_file($_FILES['file']['tmp_name'], $destinationFile)) {
    $uploadedFile = dirname($_SERVER['PHP_SELF']) . $destinationFile;

    $lastUsed = date($dateFormat);
    $DatabaseQuery = $Database->query('SELECT * FROM uploads');
    $Database->exec("INSERT INTO uploads(file, uploaddate, keyid, keytype) VALUES('$uploadedFile', '$lastUsed', '$keyID', '$keyType')");

    if ($WebInterface == 0) {
        print "$uploadedFile";
    } else {
        header("Location: $uploadedFile");
    }

    if (isset($_REQUEST['web'])) { // redirect back to index
        header("Redirect: $uploadedFile");
        die();
    }
} else {
    if (file_exists($destinationFile)) { // wtf
        if ($WebInterface == 0) {
            print "Upload failed.";
            die();
        } else {
            header("Location: /?e=wtf");
            die();
        }
    }
}
?>
