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
    $Action = htmlspecialchars($_REQUEST['a']);
}

if (!isset($_REQUEST['f'])) {
    header("Location: /");
    die();
} else {
    $id = htmlspecialchars($_REQUEST['f']);
}

$html = "";
$html = printHeader($html);

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM uploads');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['id'] == $id) {
        if ($Action != "view") {
            $File = $_SERVER['DOCUMENT_ROOT'] . $line['file'];

            if (file_exists($File)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($File));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($File));
                readfile($File);
            }

            exit;
        } else {
            $BaseFilename = basename($line['file']);
            $ID = $line['id'];
            $Uploader = $line['username'];
            $UploadDate = $line['uploaddate'];
            $Exists = 1;

            $html .= "\t\t\t\t<h2 class=\"fileName\">$BaseFilename</h2>\n";
            $html .= "\t\t\t\t\t<p>This file was uploaded by $Uploader on $UploadDate</p><br>\n";
            $html .= "\t\t\t\t<form class=\"fileForm\" action=\"file.php\">\n";
            $html .= "\t\t\t\t\t<input type=\"hidden\" name=\"a\" value=\"dl\">\n";
            $html .= "\t\t\t\t\t<input type=\"hidden\" name=\"f\" value=\"$ID\">\n";
            $html .= "\t\t\t\t\t<input type=\"submit\" value=\"Download '$BaseFilename'\">\n";
            $html .= "\t\t\t\t</form>\n";
            $html .= "\t\t\t\t\t<br><p><strong>Tip: You can append '&a=true' to get a direct link.</strong></p><br><br><small>You are authorized to download this file. The authors of this site take no responsibility for the uploaded file. By downloading this file, you obtain a copy of the uploaded file. Unless otherwise specified in the file or law, the uploader reserves all rights to the file, including copyright.<br><br><strong>For legal issues, contact the uploader of this file.</strong></small>\n";
        }
        break;
    }
}

$html = printFooter($html);

print "$html";
?>
