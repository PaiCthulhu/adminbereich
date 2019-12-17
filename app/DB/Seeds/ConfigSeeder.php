<?php


use Phinx\Seed\AbstractSeed;

class ConfigSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return [
            'ConfigCatSeeder'
        ];
    }

    public function run()
    {
        $title = new \abApp\Models\Config();
        $title->label = "Nome do site";
        $title->val = "aBApp";
        $title->key = "site_name";
        $title->field = "text";
        $title->config_cat_id = 1;
        $title->saveNew();

        $description = new abApp\Models\Config();
        $description->label = "Descrição do site";
        $description->val = "Aplicativo criado com o microframework AdminBereich";
        $description->key = "description";
        $description->field = "text";
        $description->config_cat_id = 1;
        $description->saveNew();
    }
}
