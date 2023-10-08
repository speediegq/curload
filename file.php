<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Action = "";
$id = 0;
$Exists = 0;

if (!isset($_REQUEST['a'])) {
    $Action = "view";
} else {
    $Action = $_REQUEST['a'];
}

if (!isset($_REQUEST['f'])) {
    header("Location: /");
    die();
} else {
    $id = $_REQUEST['f'];
}

$html = "";
$html = printHeader($html);

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM uploads');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $id) {
        if ($Action != "view") {
            $File = $line['file'];
            header("Location: $File");
            die();
        } else {
            $BaseFilename = basename($line['file']);
            $ID = $line['id'];
            $Uploader = $line['username'];
            $UploadDate = $line['uploaddate'];
            $Exists = 1;

            /*
            $html .= "\t\t\t\t<table class=\"FileView\">\n";
            $html .= "\t\t\t\t\t<tr class=\"FileView\">\n";
            $html .= "\t\t\t\t\t\t<th class=\"fileFilename\">Filename</th>\n";
            $html .= "\t\t\t\t\t\t<td class=\"fileFilename\">$BaseFilename</td>\n";
            $html .= "\t\t\t\t\t</tr>\n";
            $html .= "\t\t\t\t\t\t<th class=\"fileUploader\">Uploader</th>\n";
            $html .= "\t\t\t\t\t\t<td class=\"fileUploader\">$Uploader</td>\n";
            $html .= "\t\t\t\t\t</tr>\n";
            $html .= "\t\t\t\t\t\t<th class=\"fileUploadDate\">Uploaded</th>\n";
            $html .= "\t\t\t\t\t\t<td class=\"fileUploadDate\">$UploadDate</td>\n";
            $html .= "\t\t\t\t\t</tr>\n";
            $html .= "\t\t\t\t\t\t<th class=\"fileID\">ID</th>\n";
            $html .= "\t\t\t\t\t\t<td class=\"fileID\">$ID</td>\n";
            $html .= "\t\t\t\t\t</tr>\n";
            $html .= "\t\t\t\t</table>\n";
             */

            $html .= "\t\t\t\t<h2 class=\"fileName\">$BaseFilename</h2>\n";
            $html .= "\t\t\t\t\t<p>This file was uploaded by $Uploader on $UploadDate</p><br><br><br>\n";
            $html .= "\t\t\t\t<form class=\"fileForm\" action=\"file.php\">\n";
            $html .= "\t\t\t\t\t<input type=\"hidden\" name=\"a\" value=\"dl\">\n";
            $html .= "\t\t\t\t\t<input type=\"hidden\" name=\"f\" value=\"$ID\">\n";
            $html .= "\t\t\t\t\t<input type=\"submit\" value=\"Download $BaseFilename\">\n";
            $html .= "\t\t\t\t</form>\n";
            $html .= "\t\t\t\t\t<br><br><br><br><br><small>You are authorized to download this file. The authors of this site take no responsibility for the uploaded file. By downloading this file, you obtain a copy of the uploaded file. Unless otherwise specified in the file or law, the uploader reserves all rights to the file, including copyright.<br><br><strong>For legal issues, contact the uploader of this file.</strong></small>\n";
        }
        break;
    }
}

$html = printFooter($html);

print "$html";
?>