<?php


use Phinx\Seed\AbstractSeed;

class ConfigCatSeeder extends AbstractSeed
{

    public function run()
    {
        $data = [
            [
                "nome"=>"Configurações Básicas",
                "slug"=>"basico",
                "icon"=>"fa-wrench",
                "ordem"=>1
            ]
        ];
        $table = $this->table("config_cat");
        $table->insert($data)->save();
    }
}
