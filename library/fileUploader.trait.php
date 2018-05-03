<?php

namespace AdmBereich;

trait fileUploader{

    /**
     * @param string $folder O path da pasta onde o arquivo será salvo
     * @param array $file O array $_FILES que contém o arquivo
     * @param string $filename
     * @return bool
     */
    static function fileUpload($folder, $file, $filename = ''){
        if(!is_dir($folder))
            if(!mkdir($folder))
                return false;
        if(empty($filename))
            $filename = date('YmdHis').uniqid().".".static::getFileExtension($file['name']);
        $move = move_uploaded_file($file['tmp_name'], $folder.$filename);
        return $move;
    }

    static function getFileExtension($path){
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}