<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Error = "";
$html = "";

if (isset($_REQUEST['e'])) $Error = $_REQUEST['e'];

$html = printHeader($html);
$html .= "\t\t\t<h1>Your files</h1>\n";
$html .= "\t\t\t\t<p>These are the files you have uploaded using this account.</p>\n";

// If logged in ...
if (isset($_SESSION['type']) && (!$publicUploading || $publicUploading == "false")) {
    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM uploads');

    $html .= "\t\t\t\t<table class=\"FileView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"FileView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileID\">ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileFilename\">Filename</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"fileUploadDate\">Upload date</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    while ($line = $DatabaseQuery->fetchArray()) {
        $ID = $line['id'];
        $Filename = $line['file'];
        $uploadDate = $line['uploaddate'];
        $Username = $line['username'];
        $usertypeID = $line['usertype'];
        $CorrectFile = 0;

        if ($line['usertype'] == 1) {
            $userType = "User";
        } else if ($line['usertype'] == 2) {
            $userType = "Administrator";
        } else {
            $userType = "Unknown";
        }

        $UserDatabaseQuery = $Database->query('SELECT * FROM users');
        while ($uline = $UserDatabaseQuery->fetchArray()) {
            if ($uline['username'] == $Username && $_SESSION['username'] == $uline['username']) {
                $CorrectFile = 1;
                break;
            }
        }

        // wrong file, move on
        if ($CorrectFile != 1) {
            continue;
        }

        $BaseFilename = basename($Filename);
        $html .= "\t\t\t\t\t<tr class=\"FileView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileID\" id=\"fileID-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileFilename\"><a href=\"$Filename\">$BaseFilename</a></td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileUploadDate\">$uploadDate</td>\n";

        if (($enableUserUploadRemoval || $enableUserUploadRemoval == "true") || $usertypeID == 2) {
            $html .= "\t\t\t\t\t\t<td class=\"fileRemove\"><a href=\"/remove.php?redir=files&id=$ID\">Remove</a></td>\n";
        }

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
} else {
    header("Location: index.php");
    die();
}

// End the content div and print footer
$html = printFooter($html);

// Finally print it all out at once
print "$html";

?>
