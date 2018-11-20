<?php

    class FileStreaming {
        
        public static function ToBase64($file) {
            $fileData = FileInfo::ReadAll($file);
            $fi = new FileInfo($file);
            $mime = new MimeType($fi->extension);
            $mimeType = $mime->data;
            return 'data:'.$mimeType.';base64,'.base64_encode($fileData);
        }
        
    }

?>