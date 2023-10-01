<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

function main() {
    include "config.php";
    include "core.php";

    $Error = "";
    $html = "";

    $html = printHeader($html);

    $html .= "\t\t\t<h1>$instanceName</h1>\n";
    $html .= "\t\t\t\t<p>$instanceDescription</p>\n";

    if (isset($_REQUEST['e'])) {
        $Error = $_REQUEST['e'];
    }

    if (isset($_COOKIE[$cookieTypeName]) || ($publicUploading || $publicUploading == "true")) {
        $html .= "\t\t\t<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
        $html .= "\t\t\t\t<input type=\"file\" name=\"file\" id=\"file\">\n";
        $html .= "\t\t\t\t<input type=\"submit\" value=\"Upload selected file\" name=\"web\">\n";
        $html .= "\t\t\t</form>\n";
        $html .= "\t\t\t<p>Max file size: $maxFileSize MB</p>\n";

        // error handling
        if ($Error == "file") {
            $html .= "\t\t\t<p class=\"error\">No file specified.</p>\n";
        } else if ($Error == "size") {
            $html .= "\t\t\t<p class=\"error\">File is too big.</p>\n";
        } else if ($Error == "key") {
            $html .= "\t\t\t<p class=\"error\">Invalid key. WTF?</p>\n";
        } else if ($Error == "wtf") {
            $html .= "\t\t\t<p class=\"error\">WTF? Try again.</p>\n";
        }
    }

    $html = printFooter($html);

    print "$html";
}

main();

?>
