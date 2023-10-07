<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "core.php";
include "config.php";

$Action = "";
$Authorized = 0;
$Primary = 0;
$filterID = -1;
$Error = "";

if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['type'])) {
    header('Location: login.php?redir=admin');
    die();
} else if ($_SESSION['type'] != 2) { // not allowed
    header('Location: /');
    die();
}

if (!isset($_REQUEST['action'])) {
    $Action = "files";
} else {
    $Action = $_REQUEST['action'];
}

if (!isset($_REQUEST['id'])) {
    $filterID = -1;
} else {
    $filterID = $_REQUEST['id'];
}

if (!isset($_REQUEST['e'])) {
    $Error = "";
} else {
    $Error = $_REQUEST['e'];
}

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM users');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['username'] == $_SESSION['username'] && $_SESSION['username'] != "" && $line['password'] == $_SESSION['password'] && $_SESSION['password'] != "" && $line['usertype'] == 2) {
        $Authorized = 1;
        $Primary = $line['primaryadmin'];
        break;
    }
}

// not authorized
if ($Authorized != 1) {
    header('Location: /');
    die();
}

$html = "";
$html = printHeader($html);

$html .= "\t\t\t<h1>Administrator panel</h1>\n";
$html .= "\t\t\t\t<div class=\"adminLinks\">\n";
$html .= "\t\t\t\t\t<span id='adminSpan' class='title'>\n";

if ($Action == "files") {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=files\" id='sel'>Files</a>\n";
} else {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=files\">Files</a>\n";
}

if ($Action == "users") {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=users\" id='sel'>Users</a>\n";
} else {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=users\">Users</a>\n";
}

if ($Action == "create") {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=create\" id='sel'>Create</a>\n";
} else {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=create\">Create</a>\n";
}

$html .= "\t\t\t\t\t</span>\n";
$html .= "\t\t\t\t</div>\n";

if ($Action == "files") {
    $DatabaseQuery = $Database->query('SELECT * FROM uploads');

    $html .= "\t\t\t\t<table class=\"adminFileView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"adminFileView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminID\">ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminFilename\">Filename</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUploadDate\">Upload date</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUploader\">Uploader</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminuserType\">User type</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    while ($line = $DatabaseQuery->fetchArray()) {
        $ID = $line['id'];
        $Filename = $line['file'];
        $uploadDate = $line['uploaddate'];
        $Username = $line['username'];
        $usertypeID = $line['usertype'];

        if ($line['usertype'] == 1) {
            $userType = "User";
        } else if ($line['usertype'] == 2) {
            $userType = "Administrator";
        } else {
            $userType = "Unknown";
        }

        $html .= "\t\t\t\t\t<tr class=\"adminFileView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminID\" id=\"adminID-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminFilename\"><a href=\"$Filename\">$Filename</a></td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUploadDate\">$uploadDate</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUsername\"><a href=\"admin.php?action=users#id-$usertypeID-$Username\">$Username</a></td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminuserType\">$userType</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminRemove\"><a href=\"/remove.php?redir=admin&id=$ID\">Remove</a></td>\n";

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
} else if ($Action == "create") {
    $html .= "\t\t\t\t<form class=\"adminCreateForm\" action=\"create.php?redir=admin\" method=\"post\">\n";
    $html .= "\t\t\t\t\t<label for=\"type\">User type</label>\n";
    $html .= "\t\t\t\t\t<select name=\"type\" required>\n";

    if ($Primary == 1) {
        $html .= "\t\t\t\t\t\t<option value=\"Admin\">Administrator</option>\n";
    }

    $html .= "\t\t\t\t\t\t<option value=\"User\" selected=\"selected\">User</option>\n";
    $html .= "\t\t\t\t\t\t<option value=\"Temporary\">Temporary User</option>\n";
    $html .= "\t\t\t\t\t</select>\n";
    $html .= "\t\t\t\t\t<label for=\"username\">Username</label>\n";
    $html .= "\t\t\t\t\t<input type=\"text\" name=\"username\" placeholder=\"Username\">\n";
    $html .= "\t\t\t\t\t<label for=\"password\">Password</label>\n";
    $html .= "\t\t\t\t\t<input type=\"text\" name=\"password\" placeholder=\"Password\">\n";
    $html .= "\t\t\t\t\t<br><br>\n";
    $html .= "\t\t\t\t\t<label for=\"uploadsleft\">Number</label>\n";
    $html .= "\t\t\t\t\t<input type=\"number\" name=\"uploadsleft\" min=\"1\" value=\"1\">\n";
    $html .= "\t\t\t\t\t<input type=\"submit\" value=\"Create user\" name=\"create\">\n";
    $html .= "\t\t\t\t</form>\n";

    // handle errors
    if ($Error == "data") {
        $html .= "\t\t\t\t<p class=\"adminError\">Invalid user.</p>\n";
    } else if ($Error == "type") {
        $html .= "\t\t\t\t<p class=\"adminError\">Invalid type.</p>\n";
    } else if ($Error == "denied") {
        $html .= "\t\t\t\t<p class=\"adminError\">You don't have permission to create a user of this type.</p>\n";
    } else if ($Error == "exists") {
        $html .= "\t\t\t\t<p class=\"adminError\">This user already exists.</p>\n";
    } else if ($Error == "uploads") {
        $html .= "\t\t\t\t<p class=\"adminError\">Invalid amount of uploads.</p>\n";
    } else if ($Error == "username") {
        $html .= "\t\t\t\t<p class=\"adminError\">You must specify a username.</p>\n";
    }
} else if ($Action == "users") {
    if ($Primary != 1) {
        $html .= "\t\t\t\t<p class=\"adminWarning\">Administrator users are not visible.</p>\n";
    }

    $html .= "\t\t\t\t<table class=\"adminUserView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"adminUserView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminID\">ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUser\">User</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminNumberOfUploads\">Uploads</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUploadsLeft\">Uploads left</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminLastUsed\">Last used</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminCreated\">Created</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminIP\">IP</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUserAgent\">User agent</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminuserType\">User type</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    $DatabaseQuery = $Database->query('SELECT * FROM users');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['id'] != $filterID && $filterID != -1) {
            continue;
        }

        if ($line['usertype'] == 2 && $Primary != 1) {
            continue;
        }

        $ID = $line['id'];
        $Username = $line['username'];
        $NumberOfUploads = $line['numberofuploads'];
        $UploadsLeft = "";
        $LastUsed = $line['lastused'];
        $Created = $line['created'];
        $IP = $line['ip'];
        $UserAgent = $line['useragent'];

        $userType = "Temporary";
        $UploadsLeft = $line['uploadsleft'];

        if ($line['uploadsleft'] == -1) {
            $UploadsLeft = "∞";
            $userType = "User";
        }
        if ($line['usertype'] == 2) {
            $userType = "Administrator";

            if ($line['primaryadmin'] == 1) {
                $userType = "Primary Administrator";
            }
        }

        $html .= "\t\t\t\t\t<tr class=\"adminUserView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminID\" id=\"id-1-$Username\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUser\">$Username</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminNumberOfUploads\"><a href=\"admin.php?action=files&id=$ID\">$NumberOfUploads</a></td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUploadsLeft\">$UploadsLeft</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminLastUsed\">$LastUsed</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminCreated\">$Created</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIP\">$IP</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUserAgent\">$UserAgent</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminuserType\">$userType</td>\n";

        if ($Primary == 1 && $line['primaryadmin'] != 1) { // primary admins cannot be removed
            $html .= "\t\t\t\t\t\t<td class=\"adminRemove\"><a href=\"/remove-user.php?redir=admin&id=$ID&type=2\">Remove</a></td>\n";
            $html .= "\t\t\t\t\t\t<td class=\"adminEdit\"><a href=\"/account.php?id=$ID\">Edit</a></td>\n";
        }

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
}

$html = printFooter($html);

print "$html";

?>
