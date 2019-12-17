<?php


use Phinx\Seed\AbstractSeed;

class UsuarioSeeder extends AbstractSeed
{
    public function run()
    {
        $root = new \abApp\Models\Usuario();
        $root->nome = "Administrador";
        $root->username = "admin";
        $root->email = "william.jvenancio@gmail.com";
        $root->datanasc = "0000-00-00";
        $root->senha = password_hash("senha123", PASSWORD_DEFAULT);
        $root->save();
    }
}
