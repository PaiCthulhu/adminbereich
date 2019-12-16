<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

use ScssPhp\ScssPhp\Compiler;

/**
 * Encapsula a biblioteca scssphp/scssphp para verificar e compilar arquivos .sass
 *
 * Baseado no laravel-sass, criado por Chris @panique (https://www.dev-metal.com/)
 * @package AdmBereich
 * @link https://github.com/panique/laravel-sass Classe panique/laravel-sass
 */
class Sass {

    /**
     * Compiles all .scss files in a given folder into .css files in a given folder
     *
     * @param string $scss_folder source folder where you have your .scss files
     * @param string $css_folder destination folder where you want your .css files
     * @param string $format_style CSS output format, see http://leafo.net/scssphp/docs/#output_formatting for more.
     * @return bool
     */
    static public function compile($scss_folder, $css_folder, $format_style = "scss_formatter"){
        // get all .scss files from scss folder
        $filelist = glob($scss_folder . "*.scss");

        // loop through .scss files and see if any need recompilation
        $has_changes = false;
        foreach ($filelist as $file_path) {
            $css_path = str_replace(array($scss_folder, '.scss'), array($css_folder, '.css'), $file_path);
            if (! realpath($css_path) or filemtime($file_path) > filemtime($css_path)) {
                $has_changes = true;
                break;
            }
        }
        // no files are changed, retun
        if (! $has_changes) return false;

        // scssc will be loaded automatically via Composer
        $scss_compiler = new Compiler();

        // set the path where your _mixins are
        $scss_compiler->setImportPaths($scss_folder);
        // set css formatting (normal, nested or minimized), @see http://leafo.net/scssphp/docs/#output_formatting
        $scss_compiler->setFormatter($format_style);

        $scss_compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);

        // step through all .scss files in that folder
        foreach ($filelist as $file_path) {
            // get scss and css paths
            $scss_path = $file_path;
            $css_path = str_replace(array($scss_folder, '.scss'), array($css_folder, '.min.css'), $file_path);
            // do not compile if scss has not been recently updated
            if (realpath($css_path) and ! filemtime($scss_path) > filemtime($css_path)) continue;

            $file = explode('/', $css_path);
            $file = end($file);
            $params = [
                'sourceMapWriteTo'  => ROOT.'/public/css/' . $file . ".map",
                'sourceMapURL'      => App::resourcePath("css/{$file}.map"),
                'sourceMapFilename' => PATH.'/public/css/'.$file,  // url location of .css file
                'sourceMapBasepath' => ROOT.'/public/sass/',  // difference between file & url locations, removed from ALL source files in .map
                'sourceRoot'        => PATH.'/public/sass/'
            ];
            if (in_array(PHP_OS, ["WINNT","WIN32","Windows"]))
                $params['sourceMapBasepath'] = str_replace(DIRECTORY_SEPARATOR, "/", ROOT)."/public/sass/";
            $scss_compiler->setSourceMapOptions($params);

            // get .scss's content, put it into $string_sass
            $string_sass = file_get_contents($scss_path);
            // compile this SASS code to CSS
            $string_css = $scss_compiler->compile($string_sass, str_replace('.min.css', '.scss', $file));
            // write CSS into file with the same filename, but .css extension
            file_put_contents($css_path, $string_css);
        }

        return true;
    }

}