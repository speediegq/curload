<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$WebInterface = 1;

$Username = "";
$Password = "";

if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
    $Username = $_REQUEST['username'];
    $Password = $_REQUEST['password'];
    $WebInterface = 0;
} else if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    $Username = $_SESSION['username'];
    $Password = $_SESSION['password'];
    $WebInterface = 1;
} else if (!$publicUploading || $publicUploading == "false") {
    print "Username and password must be specified.";
    die();
}

$Status = 0;
$Authorized = 0;
$userType = 1;
$uploadLimit = $maxFileSize * 1000000;

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
    $DatabaseQuery = $Database->query('SELECT * FROM users');

    while ($line = $DatabaseQuery->fetchArray()) {
        $ValidPassword = false;

        if ($WebInterface == 1 && $Password == $line['password']) {
            $ValidPassword = true;
        } else if ($WebInterface == 0 && password_verify($Password, $line['password'])) {
            $ValidPassword = true; // we passed a plain text password
        }
        if ($line['username'] == $Username && $Username != "" && $ValidPassword && $line['uploadsleft'] != 0) {
            $id = $line['id'];
            $Username = $line['username'];

            // decrease uploads left if temporary
            if ($line['uploadsleft'] != -1) {
                $uploadsLeft = $line['uploadsleft'] - 1;
                $Database->exec("UPDATE users SET uploadsleft='$uploadsLeft' WHERE id='$id'");
            }

            if ($storeLastUsage || $storeLastUsage == "true") {
                $lastUsed = date($dateFormat);
                $Database->exec("UPDATE users SET lastused='$lastUsed' WHERE id='$id'");
            }

            if ($storeUploads || $storeUploads == "true") {
                $numberOfUploads = $line['numberofuploads'] + 1;
                $Database->exec("UPDATE users SET numberofuploads='$numberOfUploads' WHERE id='$id'");
            }

            if ($storeIP || $storeIP == "true") {
                $ip = getIPAddress();
                $Database->exec("UPDATE users SET ip='$ip' WHERE id='$id'");
            }

            if ($storeAgent || $storeAgent == "true") {
                $userAgent = getUserAgent();
                $Database->exec("UPDATE users SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            $userType = $line['usertype'];
            break;
        }
    }

    // Not authorized
    if ($Authorized == 0) {
        if ($WebInterface == 0) {
            print "Not authorized: Your username or password is invalid.";
            die();
        } else {
            header("Location: /?e=user");
            die();
        }
    }
}

if ($_FILES['file']['size'] > $uploadLimit && $uploadLimit > 0 && $userType != 2) {
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
    $Database->exec("INSERT INTO uploads(file, uploaddate, username, usertype) VALUES('$uploadedFile', '$lastUsed', '$Username', '$userType')");

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
