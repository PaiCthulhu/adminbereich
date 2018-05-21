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

    /**
     * @param string $path
     * @return string
     */
    static function getFileExtension($path){
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @param $ecode
     * @return string
     */
    static function fileErrorMsg(int $ecode){
        switch ($ecode) {
            case UPLOAD_ERR_OK:
                return 'Arquivo enviado com sucesso!';
                break;
            case UPLOAD_ERR_INI_SIZE:
                return 'Arquivo excede o tamanho máximo ('.ini_get('upload_max_filesize').'b) setado pelo servidor';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                return 'Arquivo excede o limite de tamanho do formulário';
                break;
            case UPLOAD_ERR_PARTIAL:
                return 'Algo ocorreu no envio do arquivo, por favor, tente novamente';
                break;
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo enviado!';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Diretório temporário não configurado!';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao escrever arquivo em disco!';
                break;
            case UPLOAD_ERR_EXTENSION:
                return 'Envio interrompido por extensão!';
                break;
            default:
                return 'Código inválido!';
                break;
        }

    }
}