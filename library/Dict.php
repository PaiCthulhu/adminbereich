<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

/**
 * Proposta de classe de dicionário, será substituído pelo padrão i18n
 * @package AdmBereich
 */
class Dict {
    /**
     * Função principal de tradução en-US -> pt-BR
     * @param string $engString Texto a ser traduzido
     * @return string Texto traduzido
     */
    static function translate($engString){
        switch ($engString){
            case 'object':
                return 'objeto';
            default:
                $ptstr = $engString;
                break;
        }
        return $ptstr;
    }
}