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
 * Traço que fornece funções para facilitar o upload de arquivos
 * @package AdmBereich\Traits
 */
trait fileUploader{

    /**
     * Recebe um arquivo vindo de um formulário HTML e então faz upload do mesmo na pasta especificada
     * @param string $folder O caminho da pasta onde o arquivo será salvo
     * @param array $file O array $_FILES que contém o arquivo
     * @param string $filename Caso preenchido, renomeia o arquivo para o valor recebido, caso contrário, a função gera
     * um valor aleatório conforme a data e hora que o arquivo foi recebido
     * @return bool Retorna TRUE se conseguiu fazer o upload do arquivo, FALSE caso contrário
     * @throws \Exception <p>Quando o diretório não possui permissão de escrita</p>
     */
    static function fileUpload($folder, $file, $filename = ''){
        if(!is_dir($folder))
            if(!mkdir($folder))
                return false;
        if(!is_writable($folder))
            throw new \Exception("O diretório \"{$folder}\" não possui permissão de escrita.");
        if(empty($filename))
            $filename = date('YmdHis').uniqid().".".static::getFileExtension($file['name']);
        $move = move_uploaded_file($file['tmp_name'], $folder.$filename);
        return $move;
    }

    /**
     * Retorna apenas o nome do arquivo ao receber seu caminho completo
     * @param string $path Caminho completo do arquivo
     * @return string Nome do arquivo
     */
    static function getFileName($path){
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Retorna a extensão do arquivo ao receber seu caminho completo
     * @param string $path Caminho completo do arquivo
     * @return string Extensão do arquivo
     */
    static function getFileExtension($path){
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Função usada de dicionário para converter os erros padrões do PHP em mensagens mais descritivas
     * @param int $ecode Código do erro, conforme as constantes padrões de erro de upload do PHP
     * @return string Mensagem descritiva do erro ocorrido
     */
    static function fileErrorMsg(int $ecode){
        switch ($ecode) {
            case UPLOAD_ERR_OK:
                return 'Arquivo enviado com sucesso!';
                break;
            case UPLOAD_ERR_INI_SIZE:
                return 'Arquivo excede o tamanho máximo ('.ini_get('upload_max_filesize').'b) definido pelo servidor';
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