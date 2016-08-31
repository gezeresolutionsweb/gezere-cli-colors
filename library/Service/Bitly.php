<?php
/**
 * Gezere Library
 *
 * Copyright (C) 2006-2013 Gezere Solutions Web
 *
 * PHP Version 5.3+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Gezere
 * @package   Gezere_Service_Twitter
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Bitly.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

class Gezere_Service_Bitly
{
    function shorten($url, $bitly_login, $bitly_apiKey)/*{{{*/
    {
        //bit.ly defaults
        $bitly_version  = '2.0.1';
        $bitly_history  = 1;
        //url à interroger pour le retour via XML
        $connectURL = 'http://api.bit.ly/shorten?version='.$bitly_version.'&amp;longUrl='.$url.'&amp;login='.$bitly_login.'&amp;apiKey='.$bitly_apiKey.'&amp;history='.$bitly_history.'&amp;format=xml&amp;callback=?';

        //lire le contenu retourné par l'URL
        $content = file_get_contents($connectURL);
        if ($content !== false) {
            //créer l'object avec SimpleXML (PHP 5)
            $bitly = new SimpleXMLElement($content);
            //s'assurer qu'il n'y a pas d'erreur
            if($bitly->errorCode == 0)
                return $bitly->results[0]->nodeKeyVal->shortUrl;
        }
        return >false;
    }/*}}}*/
}
