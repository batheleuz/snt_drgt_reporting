<?php

/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 20/03/2017
 * Time: 16:18
 */
class CodeCompressor {

    public static function compress_js($buffer) {

        /* remove comments */
        //$buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);

        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '), '', $buffer);

        /* remove other spaces before/after ) */
        $buffer = preg_replace(array('(( )+\))','(\)( )+)'), ')', $buffer);
        
        return $buffer;
    }

    public static function compress_css($buffer){
        
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  '), '', $buffer);
        $buffer = str_replace('{ ', '{', $buffer);
        $buffer = str_replace(' }', '}', $buffer);
        $buffer = str_replace('; ', ';', $buffer);
        $buffer = str_replace(', ', ',', $buffer);
        $buffer = str_replace(' {', '{', $buffer);
        $buffer = str_replace('} ', '}', $buffer);
        $buffer = str_replace(': ', ':', $buffer);
        $buffer = str_replace(' ,', ',', $buffer);
        $buffer = str_replace(' ;', ';', $buffer);
        return $buffer;
        
    }

    public static function compress_html($compress){
        $search = array(
            '/\n/',                 // replace end of line by a space
            '/\>[^\S ]+/s',         // strip whitespaces after tags, except space
            '/[^\S ]+\</s',         // strip whitespaces before tags, except space
            '/\> \</s',
            '/(\s)+/s',             // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // supprime les commentaires html
            '!/\*[^*]*\*+([^/][^*]*\*+)*/!' // supprime les commentaires css
        );

        $replace = array(
            ' ',
            '>',
            '<',
            '><',
            '\\1',
            '',
            ''
        );
        return preg_replace($search, $replace, $compress);
    }

    public static function importer ( $path , $lang ){

        ob_start();

         include  $path;

       $fx ="compress_".$lang;

       print self::$fx( ob_get_clean() );

    }
    
}