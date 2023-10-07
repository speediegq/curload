<?php
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

function createTables($sqlDB) {
    $Database = new SQLite3($sqlDB);

    /* users table
     * id (INTEGER PRIMARY KEY)
     * username (TEXT)
     * password (TEXT)
     * usertype (INT)
     * primaryadmin (INT)
     * numberofuploads (INT)
     * uploadsleft (INT)
     * lastused (TEXT)
     * created (TEXT)
     * ip (TEXT)
     * useragent (TEXT)
     */
    $Database->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY, username TEXT, password TEXT, usertype INT, primaryadmin INT, numberofuploads INT, uploadsleft INT, lastused TEXT, created TEXT, ip TEXT, useragent TEXT)");

    /* uploads table
     * id (INTEGER PRIMARY KEY)
     * file (TEXT)
     * uploaddate (TEXT)
     *usernameeyusername (INT)
     * usertype (INT)
     */
    $Database->exec("CREATE TABLE IF NOT EXISTS uploads(id INTEGER PRIMARY KEY, file TEXT, uploaddate TEXT, username TEXT, usertype INT)");

    return $Database;
}

function printHeader($html) {
    include "config.php";

    $html .= "<!DOCTYPE html>\n";
    $html .= "<html>\n";
    $html .= "\t<head>\n";
    $html .= "\t\t<meta name=\"description\" content=\"$instanceDescription\">\n";
    $html .= "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";

    if (file_exists($Icon)) $html .= "\t\t<link rel=\"icon\" href=\"$Icon\" />\n";
    if (file_exists($Stylesheet)) $html .= "\t\t<link type=\"text/css\" rel=\"stylesheet\" href=\"$Stylesheet\"/>\n";
    if (file_exists($javaScript)) $html .= "\t\t<script src=\"$javaScript\"></script>\n";

    $html .= "\t\t<title>$instanceName</title>\n";
    $html .= "\t\t<div class=\"bar\">\n";
    $html .= "\t\t\t<span id='titleSpan' class='title'>\n";
    if (file_exists($Logo)) $html .= "\t\t\t\t<img src=\"$Logo\" id=\"titleLogo\" class=\"title\" width=\"$logoHeaderSize\" height=\"$logoHeaderSize\">\n";
    $html .= "\t\t\t\t<small id='title'><a id='title' href=\"/\">$instanceName</a></small>\n";

    // javascript free version
    if (!$allowJavascript) {
        if (isset($_SESSION['type'])) $html .= "\t\t\t\t<small id='files'><a id='files' href=\"files.php\">Your files</a></small>\n";
        if ($publicFileList || $publicFileList == "true") $html .= "\t\t\t\t<small id='filelist'><a id='filelist' href=\"all.php\">Find</a></small>\n";

        foreach (glob('*.php') as $file) {
            if (!file_exists("$file".".name")) {
                continue;
            }

            $name = file_get_contents("$file".".name");
            $name = rtrim($name, "\r\n");
            $html .= "\t\t\t\t<small id='$name'><a id='$name' href=\"$file\">$name</a></small>\n";
        }

        if (!isset($_SESSION['type'])) {
            if ($publicAccountCreation) {
                $html .= "\t\t\t\t<small id='register'><a id='register' href=\"register.php\">Register</a></small>\n";
            }

            $html .= "\t\t\t\t<small id='login'><a id='login' href=\"login.php\">Log in</a></small>\n";
        } else {
            $Username = $_SESSION['username'];
            $html .= "\t\t\t\t<small id='username'><a id='username' href=\"account.php\">$Username</a></small>\n";
            $html .= "\t\t\t\t<small id='logout'><a id='logout' href=\"login.php?logout=true\">Log out</a></small>\n";
        }

        if (isset($_SESSION['type']) && $_SESSION['type'] == 2) {
            $html .= "\t\t\t\t<small id='administration'><a id='administration' href=\"admin.php\">Administration</a></small>\n";
        }
    } else { // yay (not) we can use JS, basically stolen from w3schools because i can't JS
        $html .= "\t\t\t\t<script>\n";
        $html .= "\t\t\t\t\tfunction pelem() {\n";
        $html .= "\t\t\t\t\t\tdocument.getElementById(\"dropdown\").classList.toggle(\"show\");\n";
        $html .= "\t\t\t\t\t}\n";
        $html .= "\t\t\t\t\t\n";
        $html .= "\t\t\t\t\twindow.onclick = function(event) {\n";
        $html .= "\t\t\t\t\tif (!event.target.matches('.actionmenu')) {\n";
        $html .= "\t\t\t\t\t\tvar dropdowns = document.getElementsByClassName(\"dropdown-content\");\n";
        $html .= "\t\t\t\t\t\tvar i;\n";
        $html .= "\t\t\t\t\t\tfor (i = 0; i < dropdowns.length; i++) {\n";
        $html .= "\t\t\t\t\t\t\tvar openDropdown = dropdowns[i];\n";
        $html .= "\t\t\t\t\t\t\tif (openDropdown.classList.contains('show')) {\n";
        $html .= "\t\t\t\t\t\t\t\topenDropdown.classList.remove('show');\n";
        $html .= "\t\t\t\t\t\t\t}\n";
        $html .= "\t\t\t\t\t\t}\n";
        $html .= "\t\t\t\t\t}\n";
        $html .= "\t\t\t\t}\n";
        $html .= "\t\t\t\t</script>\n";

        $html .= "\t\t\t\t<button onclick=\"pelem()\" class=\"actionmenu\">☰</button>\n";
        $html .= "\t\t\t\t<div id=\"dropdown\" class=\"dropdown-content\">\n";

        if (isset($_SESSION['type'])) $html .= "\t\t\t\t<a href=\"files.php\">Your files</a>\n";
        if ($publicFileList || $publicFileList == "true") $html .= "\t\t\t\t<a href=\"all.php\">Find</a>\n";

        foreach (glob('*.php') as $file) {
            if (!file_exists("$file".".name")) {
                continue;
            }

            $name = file_get_contents("$file".".name");
            $name = rtrim($name, "\r\n");
            $html .= "\t\t\t\t<a href=\"$file\">$name</a>\n";
        }

        $html .= "\t\t\t\t<br><br>\n";

        if (!isset($_SESSION['type'])) {
            if ($publicAccountCreation) {
                $html .= "\t\t\t\t<a id='register' href=\"register.php\">Register</a>\n";
            }

            $html .= "\t\t\t\t<a id='login' href=\"login.php\">Log in</a>\n";
        } else {
            $Username = $_SESSION['username'];
            $html .= "\t\t\t\t<a id='username' href=\"account.php\">$Username</a>\n";
            $html .= "\t\t\t\t<a id='logout' href=\"login.php?logout=true\">Log out</a>\n";
        }

        if (isset($_SESSION['type']) && $_SESSION['type'] == 2) {
            $html .= "\t\t\t\t<a id='administration' href=\"admin.php\">Administration</a>\n";
        }

        $html .= "\t\t\t\t</div>\n";
    }

    $html .= "\t\t\t</span>\n";
    $html .= "\t\t</div>\n";
    $html .= "\t</head>\n";
    $html .= "\t<body>\n";
    $html .= "\t\t<div class=\"content\">\n";

    return "$html";
}

function printFooter($html) {
    include "config.php";

    $html .= "\t\t</div>\n";
    $html .= "\t</body>\n";
    $html .= "\t<footer>\n";
    $html .= "\t\t<span id='footerSpan' class='footer'>\n";
    $html .= "\t\t\t<p class='footerText' id='footerText'>$footerText</p>\n";
    $html .= "\t\t</span>\n";
    $html .= "\t</footer>\n";
    $html .= "</html>\n";

    return "$html";
}

function printFileUploadForm($html, $Error) {
    include "config.php";

    // print the form
    if (isset($_SESSION['type']) || ($publicUploading || $publicUploading == "true")) {
        $html .= "\t\t\t<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
        $html .= "\t\t\t\t<input type=\"file\" name=\"file\" id=\"file\">\n";
        $html .= "\t\t\t\t<input type=\"submit\" value=\"Upload selected file\" id='web' name=\"web\">\n";
        $html .= "\t\t\t</form>\n";
        $html .= "\t\t\t<p id='maxFileSize'>Max file size: $maxFileSize MB</p>\n";

        // error handling
        if ($Error == "file") {
            $html .= "\t\t\t<p class=\"error\">No file specified.</p>\n";
        } else if ($Error == "size") {
            $html .= "\t\t\t<p class=\"error\">File is too big.</p>\n";
        } else if ($Error == "user") {
            $html .= "\t\t\t<p class=\"error\">File upload failed: No uploads left.</p>\n";
        } else if ($Error == "wtf") {
            $html .= "\t\t\t<p class=\"error\">WTF? Try again.</p>\n";
        }
    }

    return "$html";
}

function checkIfAdminExists() {
    include "config.php";

    $adminExists = 0;

    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM users');

    $adminExists = 0;
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['usertype'] == 2) {
            $adminExists = 1;
            break;
        }
    }

    return $adminExists;
}

function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'];
}

function generatePassword($pwd) {
    return password_hash($pwd, PASSWORD_DEFAULT);
}

?>
