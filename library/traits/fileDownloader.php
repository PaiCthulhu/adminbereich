<?php

namespace AdmBereich\Traits;

trait fileDownloader {

    /**
     * @param string $file_url
     * @throws \Exception
     */
    static function fileDownload($file_url){
        if(file_exists($file_url)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
            header('Content-Length: ' . filesize($file_url));
            ob_clean();
            flush();
            readfile($file_url); // do the double-download-dance (dirty but worky)
            exit();
        }
        else
            throw new \Exception("Arquivo não encontrado ({$file_url})");
    }

}