<?php

$targetSubDir = 'uploads/blog_cms/';
$fileName = time() . "_" . str_replace(" ", "_", $_FILES['upload']['name']);
if (!file_exists($targetSubDir)) {
    mkdir($targetSubDir, 0777);
}
$url = $targetSubDir . $fileName;

//extensive suitability check before doing anything with the file…
if (($_FILES['upload'] == "none" ) OR ( empty($_FILES['upload']['name']))) {
    $message = "No file uploaded.";
} else if ($_FILES['upload']["size"] == 0) {
    $message = "The file is of zero length.";
} else if ($_FILES['upload']["size"] >= '2097152') {
    $message = "File cannot be greater than 2MB.";
} else if (($_FILES['upload']["type"] != "image/pjpeg") AND ( $_FILES ['upload']["type"] != "image/jpeg") AND ( $_FILES ['upload']["type"] != "image/png")) {
    $message = "The image must be in either JPG or PNG format. Please upload a JPG or PNG instead.";
} else if (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
    $message = "You may be attempting to hack our server. We're on to you; expect a knock on the door sometime soon.";
} else {
    $message = "";
    $move = move_uploaded_file($_FILES['upload']['tmp_name'], $url);
    if (!$move) {
        $message = "Error moving uploaded file. Check the script is granted Read/Write/Modify permissions.";
    }
    $basepath = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
    $url = $basepath .'/'. $url;
}
$funcNum = $_GET['CKEditorFuncNum'];
echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
