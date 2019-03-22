<?php
/**
 * Created by PhpStorm.
 * User: rcarranza
 * Date: 3/22/2019
 * Time: 7:12 AM
 */

namespace AppBundle\Util;


class Slugger
{
    static public function getSlug($cadena, $separador = '-')
    {
    // Código copiado de http://cubiq.org/the-perfect-php-clean-url-generator
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separador));
        $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);
        return $slug;
    }
}