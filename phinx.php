<?php
const ROOT = __DIR__;
\AdmBereich\App::loadEnv();

return [
    "paths"=>[
        "migrations"=>'%%PHINX_CONFIG_DIR%%/app/DB/Migrations',
        "seeds"=>'%%PHINX_CONFIG_DIR%%/app/DB/Seeds'
    ],
    "environments"=>[
        "default_migration_table"=>"migration",
        "default_database"=>"environment",
        "environment"=>[
            "adapter"=>"mysql",
            "host"=>$_ENV["DB_HOST"],
            "name"=>$_ENV["DB_NAME"],
            "user"=>$_ENV["DB_USER"],
            "pass"=>$_ENV["DB_PSWD"],
            "port"=>$_ENV["DB_PORT"],
            "charset"=>"utf8"
        ]
    ]
];