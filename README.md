# AdminBereich
Projeto de desenvolvimento de framework para aprendizado pessoal, onde
este servirá como uma plataforma para sites com gerenciamento de 
conteúdo, ou seja, *Sites Dinâmicos.*

O nome **AdminBereich** veio de meu interesse pelo idioma alemão 
e significa "Área Administrativa". No momento, serve de *placeholder* 
para um nome final.

## Instalação Rápida
```
#Crie o projeto
composer create-project pai-cthulhu/adminbereich <path>

#Configure o banco de dados no .env

#Rode as migrações de Banco de Dados
vendor/bin/phinx migrate

#Rode os semeadores
vendor/bin/phinx seed:run
```
### Bibliotecas/Tecnologias implementadas:
* Blade Templates, implementado através de **BladeOne**
* **scssphp** para compilação dos arquivos .sass
* **PHP dotenv** para utilização de variáveis `$_ENV` carregadas a partir do arquivo .env
* **Kint**, para depuração de variáveis
* **FontAwesome**, como conjunto de ícones
* **JQuery**, para manipulação de DOM, animações e requisições AJAX
* *Twitter* **Bootstrap** 4.0 via CDN para diagramação do layout 
* **Phinx** para rodar migrações de banco de dados