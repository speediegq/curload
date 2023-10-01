<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

$Stylesheet             = "index.css";
$javaScript             = "index.js";
$Icon                   = "favicon.svg";
$Logo                   = "logo.svg";
$uploadDir              = "uploads/";
$maxFileSize            = "100";
$sqlDB                  = "curload.sql";
$storeIP                = true;
$storeAgent             = true;
$storeIssued            = true;
$storeLastUsage         = true;
$storeUploads           = true;
$publicUploading        = false;
$renameDuplicates       = true;
$replaceOriginal        = false;
$logoHeaderSize         = 16;
$dateFormat             = "Y/m/d";
$instanceName           = "curload";
$instanceDescription    = "curload is a simple file uploading site allowing users to upload files by authenticating using a key.";
$footerText             = "Licensed under the GNU Affero General Public License version 3.0.";
$cookieName             = "speedierocks";
$enableKeys             = true;
$enableAdminKeys        = true;
$enableTemporaryKeys    = true;
$enableUploadRemoval    = true;
$enableKeyUploadRemoval = false;
$cookieTypeName         = "$cookieName" . "_type";

$configFile = "";

if (file_exists("config.ini")) {
    $configFile = "config.ini";
} else if (file_exists("config.def.ini")) {
    $configFile = "config.def.ini";
}

if (!file_exists($configFile)) {
    print "Error: Config file '$configFile' not found.";
    die();
}

// load config file
$configEntries = parse_ini_file($configFile);

$Stylesheet = $configEntries['css'];
$Icon = $configEntries['favicon'];
$Logo = $configEntries['logo'];
$uploadDir = $configEntries['upload_dir'];
$maxFileSize = $configEntries['max_size'];
$sqlDB = $configEntries['sqldb'];
$storeIP = $configEntries['store_ip'];
$storeAgent = $configEntries['store_user_agent'];
$storeIssued = $configEntries['store_issued'];
$storeLastUsage = $configEntries['store_last_usage'];
$storeUploads = $configEntries['store_number_of_uploads'];
$logoHeaderSize = $configEntries['logo_header_size'];
$dateFormat = $configEntries['date_format'];
$instanceName = $configEntries['instance_name'];
$instanceDescription = $configEntries['instance_description'];
$footerText = $configEntries['footer_text'];
$publicUploading = $configEntries['public_uploading'];
$renameDuplicates = $configEntries['rename_duplicates'];
$replaceOriginal = $configEntries['replace_original'];
$enableKeys = $configEntries['enable_keys'];
$enableAdminKeys = $configEntries['enable_admin_keys'];
$enableTemporaryKeys = $configEntries['enable_temporary_keys'];
$enableUploadRemoval = $configEntries['enable_upload_removal'];
$enableKeyUploadRemoval = $configEntries['enable_key_upload_removal'];
$cookieName = $configEntries['cookie_name'];
$javaScript = $configEntries['javascript'];
$cookieTypeName = "$cookieName" . "_type";
?>
