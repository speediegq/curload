<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$html = "";
$html = printHeader($html);

$Database = createTables($sqlDB);

// init database
if ($publicFileList || $publicFileList == "true") {
    $html .= "\t\t\t<h1>All files</h1>\n";
    $html .= "\t\t\t\t<p>This is a table/list of all files that have been uploaded to curload.</p>\n";

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
        $usertypeID = $line['usertype'];

        $html .= "\t\t\t\t\t<tr class=\"FileView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileID\" id=\"fileID-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileFilename\"><a href=\"$Filename\">$Filename</a></td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileUploadDate\">$uploadDate</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"fileUploader\">$Username</td>\n";

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
} else {
    $html .= "\t\t\t<h1>Not allowed</h1>\n";
    $html .= "\t\t\t\t<p>The server administrator has not allowed viewing of all uploaded files.</p>\n";
}

$html = printFooter($html);

print "$html";
?>
