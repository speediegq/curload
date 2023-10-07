<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Action = "";

if (!isset($_REQUEST['action'])) {
    $Action = "files";
} else {
    $Action = $_REQUEST['action'];
}

$html = "";
$html = printHeader($html);

$html .= "\t\t\t<h1>All</h1>\n";
$html .= "\t\t\t\t<div class=\"allLinks\">\n";
$html .= "\t\t\t\t\t<span id='allSpan' class='title'>\n";

if ($publicFileList) {
    if ($Action == "files") {
        $html .= "\t\t\t\t\t\t<a href=\"/all.php?action=files\" id='sel'>Files</a>\n";
    } else {
        $html .= "\t\t\t\t\t\t<a href=\"/all.php?action=files\">Files</a>\n";
    }
}

if ($publicUserList) {
    if ($Action == "users") {
        $html .= "\t\t\t\t\t\t<a href=\"/all.php?action=users\" id='sel'>Users</a>\n";
    } else {
        $html .= "\t\t\t\t\t\t<a href=\"/all.php?action=users\">Users</a>\n";
    }
}

$html .= "\t\t\t\t\t</span>\n";
$html .= "\t\t\t\t</div>\n";

// init database
if ($Action == "files" && ($publicFileList || $publicFileList == "true")) {
    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM uploads');

    $html .= "\t\t\t\t<table class=\"FileView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"FileView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileID\">ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileFilename\">Filename</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileUploadDate\">Upload date</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileUploader\">Uploader</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    while ($line = $DatabaseQuery->fetchArray()) {
        $ID = $line['id'];
        $Filename = $line['file'];
        $uploadDate = $line['uploaddate'];
        $Username = $line['username'];

        $html .= "\t\t\t\t\t<tr class=\"FileView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileID\" id=\"fileID-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileFilename\"><a href=\"$Filename\">$Filename</a></td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileUploadDate\">$uploadDate</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileUploader\">$Username</td>\n";

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
} else if ($Action == "users" && ($publicUserList || $publicUserList == "true")) {
    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM users');

    $html .= "\t\t\t\t<table class=\"UserView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"UserView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"userName\">Username</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"userNumberOfUploads\">Uploads</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"userIssued\">Issued</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"userType\">User type</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    while ($line = $DatabaseQuery->fetchArray()) {
        $Username = $line['username'];
        $numberofuploads = $line['numberofuploads'];
        $Issued = $line['issued'];
        $usertypeID = $line['usertype'];
        $userType = "";

        if ($line['usertype'] == 1) {
            $userType = "User";
        } else if ($line['usertype'] == 2) {
            $userType = "Administrator";
        } else {
            $userType = "Unknown";
        }

        $html .= "\t\t\t\t\t<tr class=\"View\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"userName\">$Username</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"userNumberOfUploads\">$numberofuploads</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"userIssued\">$Issued</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"userType\">$userType</td>\n";

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
} else {
    header("Location: /");
    die();
}

$html = printFooter($html);

print "$html";
?>
