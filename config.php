<?php
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

$Stylesheet              = "index.css";
$javaScript              = "index.js";
$Icon                    = "favicon.svg";
$Logo                    = "logo.svg";
$uploadDir               = "uploads/";
$maxFileSize             = "100";
$sqlDB                   = "curload.sql";
$storeIP                 = true;
$storeAgent              = true;
$storeIssued             = true;
$storeLastUsage          = true;
$storeUploads            = true;
$publicUploading         = false;
$allowPasswordChange     = true;
$renameDuplicates        = true;
$replaceOriginal         = false;
$logoHeaderSize          = 16;
$dateFormat              = "Y/m/d";
$instanceName            = "curload";
$instanceDescription     = "curload is a simple file uploading site allowing users to upload files.";
$footerText              = "Licensed under the GNU Affero General Public License version 3.0.";
$enableUploadRemoval     = true;
$enableUserUploadRemoval = false;
$publicFileList          = false;

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
$allowUsernameChange = $configEntries['allow_change_username'];
$allowPasswordChange = $configEntries['allow_change_password'];
$renameDuplicates = $configEntries['rename_duplicates'];
$replaceOriginal = $configEntries['replace_original'];
$enableUploadRemoval = $configEntries['enable_upload_removal'];
$enableUserUploadRemoval = $configEntries['enable_user_upload_removal'];
$publicFileList = $configEntries['public_file_list'];
$javaScript = $configEntries['javascript'];
?>
