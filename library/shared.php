<?php
/**
 * Arquivo com funções de configuração
 */
setlocale(LC_TIME, $_ENV['APP_LOCALE_CODE'], "{$_ENV['APP_LOCALE_CODE']}.utf-8", "{$_ENV['APP_LOCALE_CODE']}.utf-8", $_ENV['APP_LOCALE_NAME']);
date_default_timezone_set($_ENV['APP_TIMEZONE']);
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
Kint\Renderer\RichRenderer::$folder = false;

/**
 * Configura as variáveis de exibição de erros conforme o sistema está ou não "em produção"
 *
 * Caso esteja em produção, ao invés de exibir, grava o erro em um arquivo error.log
 */
function setReporting(){
    if($_ENV['APP_DEBUG'] == true){      //Modo Desenvolvimento
        error_reporting(E_ALL);
        ini_set('display_errors',"on");
    }
    else{                   //Modo Produção
        error_reporting(E_ALL);
        ini_set('display_errors',"off");
        ini_set('log_errors','On');
        ini_set('error_log',ROOT.'/tmp/logs/error.log');
    }
}

function setSession(){
    is_dir(SESSION_DIR) or mkdir(SESSION_DIR, 0777);
    if (ini_get("session.use_trans_sid") == true) {
        ini_set("url_rewriter.tags", "");
        ini_set("session.use_trans_sid", false);
    }
    ini_set("session.gc_maxlifetime", '10800');
    ini_set("session.gc_divisor", "1");
    ini_set("session.gc_probability", "1");
    ini_set("session.cookie_lifetime", "0");
    ini_set("session.save_path", SESSION_DIR);
}

/**
 * Replace the last occurrance of the search string with the replacement string
 * @link https://stackoverflow.com/questions/3835636/php-replace-last-occurrence-of-a-string-in-a-string
 * @param string $search The value being searched for, otherwise known as the needle.
 * @param string $replace The replacement value that replaces found search
 * @param string $subject The string being searched and replaced on, otherwise known as the haystack.
 * @return string This function returns a string with the replaced values.
 */
function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if($pos !== false)
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    return $subject;
}

/**
 * Replace the first occurance of the search string with the replacement string
 * @link https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
 * @param string $search The value being searched for, otherwise known as the needle.
 * @param string $replace The replacement value that replaces found search
 * @param string $subject The string being searched and replaced on, otherwise known as the haystack.
 * @return string This function returns a string with the replaced values.
 */
function str_freplace($search, $replace, $subject) {
    if(empty($search))
        return $subject;
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

/**
 * Função para despuração de variáveis
 * @param mixed $var
 */
function dump(...$var){
    if(count($var) == 1)
        $var = $var[0];
    if(class_exists('Kint')){
        Kint::$aliases[] = 'dump';
        Kint::$aliases[] = 'exception_handler';
        Kint::dump($var);
    }
    else
        var_dump($var);
}


set_exception_handler(function ($exception) {
    echo "<style>";
    echo <<<CSS
    @keyframes blink {
      from, to {
        color: transparent;
      }
      50% {
        color: black;
      }
    }
    .blink{
    animation: 1s blink step-end infinite;
    }
CSS;
    echo "</style>";

    echo "<div style='border: 3px double; padding: 1rem;'>";
    echo "<h2><pre>&lt;admBereich&gt;<span class='blink'>_</span></pre></h2>";
    echo "<pre><code>Ocorreu um erro grave, e o sistema não pode prosseguir:</code></pre>";
    dump($exception);
    echo "</div>";
});

/**------------------------------------------------------------------------------------------------------------------**/

setReporting();
setSession();