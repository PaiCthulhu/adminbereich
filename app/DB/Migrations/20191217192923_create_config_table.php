<?php

use Phinx\Migration\AbstractMigration;

class CreateConfigTable extends AbstractMigration
{

    public function change()
    {
        $category = $this->table("config_cat", ["id"=>"config_cat_id"]);
        $category->addColumn("nome", "string", ["limit"=>80])
            ->addColumn("slug", "string", ["limit"=>30])
            ->addColumn("icon", "string", ["limit"=>80])
            ->addColumn("ordem", "smallinteger")
            ->create();

        $config = $this->table("config", ["id"=>"config_id"]);
        $config->addColumn("label", "string")
            ->addColumn("val", "string")
            ->addColumn("key", "string")
            ->addColumn("field", "string", ["limit"=>15])
            ->addColumn("config_cat_id", "integer")
            ->addForeignKey("config_cat_id", "config_cat", "config_cat_id", ["delete"=>"CASCADE","update"=>"NO_ACTION"])
        ->create();
    }
}
