<?php
namespace abApp\Models;

use AdmBereich\Model;

class Config extends Model {
    /**
     * @var int $config_id
     */
    public $config_id;
    /**
     * @var int $config_cat_id
     */
    public $config_cat_id;
    /**
     * @var string $label
     */
    public $label;
    /**
     * @var string $val
     */
    public $val;
    /**
     * @var string $key
     */
    public $key;
    /**
     * @var string $field
     */
    public $field;


    function getByKey($key){
        return self::first(['key'=>$key])->val;
    }

}