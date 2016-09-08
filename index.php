<?php
require_once('wopi.php');
if (isset($_POST['fileName'])) {
    echo GenerateFileLink($_POST['fileName']);
}
if (isset($_REQUEST['access_token'])) {
    if (isset($_SERVER['REDIRECT_URL'])) {
        $fileName = explode('/', $_SERVER['REDIRECT_URL'])[3];
        if (explode('/', $_SERVER['REDIRECT_URL'])[4] == 'contents') {
            Wopi::GetFile($fileName);
        } else {
            Wopi::CheckFileInfo($fileName, $_REQUEST['access_token']);
        }
    }
} else {
    GenerateFileLink('test.docx'); //TEST FUNCTION
}

function GenerateFileLink($fileName)
{
    //main vars
    $arXlsReadFormat = array('ods', 'xls', 'xlsb', 'xlsm', 'xlsx');
    $arWordReadFormat = array('doc', 'docm', 'docx', 'dot', 'dotm', 'dotx', 'odt');
    $arPPReadFormat = array('odp', 'pot', 'potm', 'potx', 'pps', 'ppsm', 'ppsx', 'ppt', 'pptm', 'pptx');
    $arPDFFormat = array('pdf');
    $action = 'view';
    $guid = GUID();
    $wopi_url_temlpate = "WOPISrc={0}&access_token={1}";
    $ooServerURL = 'https://ooServerURL/hosting/discovery'; // replace ooServerURL to your oos server hostname
    if (isset($_SERVER['DOCUMENT_ROOT'])) {
        $filePath = $_SERVER['DOCUMENT_ROOT']. '/' . $fileName;
    }
    $fileExtension = array_pop(explode('.', $fileName));
    if (in_array($fileExtension, $arXlsReadFormat)) {
        $fileType = 'Excel';
    } elseif (in_array($fileExtension, $arWordReadFormat)) {
        $fileType = 'Word';
    } elseif (in_array($fileExtension, $arPPReadFormat)) {
        $fileType = 'PowerPoint';
    } elseif (in_array($fileExtension, $arPDFFormat)) {
        $fileType = 'WordPdf';
    } else {
        die();
    }
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    if(!$sourceXml = file_get_contents($ooServerURL, false, stream_context_create($arrContextOptions))) {
        $error = error_get_last();
        echo "HTTP request failed. Error was: " . $error['message'];
        return;
    }
    str_replace('"', "'", $sourceXml);
    $xml = simplexml_load_string($sourceXml);
    $elements = $xml->xpath("net-zone[@name='external-https']/app[@name='$fileType']/action[@name='$action'][@ext='$fileExtension']");
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $protocol = "https";
    } else {
        $protocol = "http";
    }
    if (isset($_SERVER['HTTP_HOST'])) {
        $hostName = $_SERVER['HTTP_HOST'];
    }
    $fileUrl = urlencode($protocol . "://" . $hostName . "/wopi/files/" . $fileName);
    $requestUrl = preg_replace("/<.*>/", "", (string)$elements[0]["urlsrc"]);
    $requestUrl = $requestUrl . str_replace('{1}', $guid, $wopi_url_temlpate);
    $requestUrl = str_replace("{0}", $fileUrl, $requestUrl); 
    echo $protocol . "://" . $hostName . "/wopi/files/$fileName?access_token=$guid" . "<br /><br />"; //LINK SHOW TEST
    echo $protocol . "://" . $hostName . "/wopi/files/$fileName/contents?access_token=$guid" . "<br /><br />"; //LINK SHOW TEST
    echo $requestUrl; //LINK SHOW TEST
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}