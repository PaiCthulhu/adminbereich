<?php

use Phinx\Migration\AbstractMigration;

class CreateUsuarioTable extends AbstractMigration
{

    public function change()
    {
        $table = $this->table("usuario", ["id"=>"usuario_id"]);
        $table->addColumn("nome", "string", ["limit"=>80])
            ->addColumn("username", "string", ["limit"=>80])
            ->addColumn("email", "string", ["limit"=>64])
            ->addColumn("datanasc", "date")
            ->addColumn("senha", "string", ["limit"=>64])
            ->addIndex(["username", "email"],["unique"=>true])
        ->create();
    }
}
