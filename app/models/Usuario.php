<?php
namespace abApp\Models;

class Usuario extends \AdmBereich\Model{

    /**
     * @var int $usuario_id
     */
    public $usuario_id;
    /**
     * @var string $nome
     */
    public $nome;
    /**
     * @var string $username
     */
    public $username;
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string|int $datanasc datetime
     */
    public $datanasc;
    /**
     * @var string $senha
     */
    public $senha;

}