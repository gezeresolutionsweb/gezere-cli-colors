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
 * @version   SVN: $Id: Flickr.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

class Gezere_Service_Flickr
{ 
    private $apiKey = 'YOUR_API_KEY'; 

    public function __construct() {/*{{{*/
    } /*}}}*/

    public function search($query = null) { /*{{{*/
        $search = 'http://flickr.com/services/rest/?method=flickr.photos.search&api_key=' . $this->apiKey . '&text=' . urlencode($query) . '&per_page=12&format=php_serial'; 
        $result = file_get_contents($search); 
        $result = unserialize($result); 
        return $result; 
    } /*}}}*/
}

/* 
if (!empty($_GET['search'])) {
    $Flickr = new Flickr; 
    $data = $Flickr->search(stripslashes($_GET['search'])); 
    $html = '';
    if (!empty($data['photos']['total'])) {
        $html = '<p>Total '.$data['photos']['total'].' photo(s) for this keyword.</p>'; 
        foreach($data['photos']['photo'] as $photo) { 
            $html .=  '
            <img src="http://farm' . $photo["farm"] . '.static.flickr.com/' . $photo["server"] . '/' . $photo["id"] . '_' . $photo["secret"] . '_s.jpg" alt="" />'; 
        }
    } else {
        $html = '<p>There are no photos for this keyword.</p>';
    }
    echo $html;
}
?>
*/
