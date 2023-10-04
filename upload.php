<?php session_start();
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$WebInterface = 1;

if (isset($_REQUEST['key'])) {
    $Key = $_REQUEST['key'];
    $WebInterface = 0;
} else if (isset($_SESSION['key'])) {
    $Key = $_SESSION['key'];
    $WebInterface = 1;
} else if (!$publicUploading || $publicUploading == "false") {
    print "No key specified.";
    die();
}

$Status = 0;
$Authorized = 0;
$keyType = 1;
$uploadLimit = $maxFileSize * 1000000;
$keyID = 0;

if (!isset($_FILES['file']['name']) || $_FILES['file']['name'] == "") {
    if ($WebInterface == 0) {
        print "You didn't specify a file.";
        die();
    } else {
        header("Location: /?e=file");
        die();
    }
}

$Database = createTables($sqlDB);

// init database
if (!$publicUploading || $publicUploading == "false") {
    $DatabaseQuery = $Database->query('SELECT * FROM keys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && $line['uploadsleft'] != 0 && ($enableKeys || $enableKeys == "true")) {
            $id = $line['id'];
            $keyID = $id;

            // decrease uploads left if temporary
            if ($line['uploadsleft'] != -1) {
                $uploadsLeft = $line['uploadsleft'] - 1;
                $Database->exec("UPDATE keys SET uploadsleft='$uploadsLeft' WHERE id='$id'");
            }

            if ($storeLastUsage || $storeLastUsage == "true") {
                $lastUsed = date($dateFormat);
                $Database->exec("UPDATE keys SET lastused='$lastUsed' WHERE id='$id'");
            }

            if ($storeUploads || $storeUploads == "true") {
                $numberOfUploads = $line['numberofuploads'] + 1;
                $Database->exec("UPDATE keys SET numberofuploads='$numberOfUploads' WHERE id='$id'");
            }

            if ($storeIP || $storeIP == "true") {
                $ip = getIPAddress();
                $Database->exec("UPDATE keys SET ip='$ip' WHERE id='$id'");
            }

            if ($storeAgent || $storeAgent == "true") {
                $userAgent = getUserAgent();
                $Database->exec("UPDATE keys SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            $keyType = $line['keytype'];
            break;
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
