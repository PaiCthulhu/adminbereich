<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich\Traits;

/**
 * Traço que fornece uma função para forçar o download de arquivos
 * @package AdmBereich\Traits
 */
trait fileDownloader {

    /**
     * Preenche os cabeçalhos HTML para forçar o navegador a baixar o arquivo escolhido
     * @param string $file_url Caminho para o arquivo
     * @throws \Exception lança uma exceção caso o arquivo enviado não seja encontrado
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