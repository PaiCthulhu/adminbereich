<?php
namespace AdmBereich;

class Dict {
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