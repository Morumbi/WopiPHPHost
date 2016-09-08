<?

class Wopi
{
    static function CheckFileInfo($fileName, $GUID)
    {
        if(isset($_SERVER['DOCUMENT_ROOT'])) {
            $FileInfoDto = array(
                'BaseFileName' => $fileName,
                'OwnerId' => 'admin',
                'ReadOnly' => true,
                'SHA256' => base64_encode(hash_file('sha256', $_SERVER['DOCUMENT_ROOT'] . '/' . $fileName, true)),
                'Size' => filesize($_SERVER['DOCUMENT_ROOT'] . '/' . $fileName),
                'Version' => 1
            );

            $jsonString = json_encode($FileInfoDto);
            header('Content-Type: application/json');
            echo $jsonString;
        }
        else die();
    }

    static function GetFile($fileName)
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $fileName)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($_SERVER['DOCUMENT_ROOT'] . '/' . $fileName));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($_SERVER['DOCUMENT_ROOT'] . '/' . $fileName));
            readfile($_SERVER['DOCUMENT_ROOT'] . '/' . $fileName);
            exit;
        }
    }
}
?>