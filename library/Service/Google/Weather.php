<?php
/**
 * Gezere Llibrary
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
 * @package   Gezere_Service_Google_Weather
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Weather.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */


/** 
 * Gezere_Service_Google_Weather 
 *
 * This class is a wrapper aroun the Google Weather API.
 * 
 * @category  Gezere
 * @package   Gezere_Service_Google_Weather
 * @author    Sylvain Lévesque <slevesque@gezere.com> 
 * @copyright 2009 Gezere Solution Web
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 *
 * <code>
 * $gweather = new Gezere_Service_Google_Weather('quebec','fr'); // "en" also work
 * if($gweather->isFound()) {
 *   echo '<pre>'; print_r($gweather->getCity); echo '</pre>';
 *   echo '<pre>'; print_r($gweather->getCurrent()); echo '</pre>';
 *   echo '<pre>'; print_r($gweather->getForecast()); echo '</pre>';
 * }
 * </code>
 */
class Gezere_Service_Google_Weather {
    /** 
     * _cityCode 
     *
     * City code input
     * 
     * @var string
     * @access private
     */
    private $_cityCode = '';

    /** 
     * city 
     *
     * City label get on the google webservice.
     * 
     * @var string
     * @access private
     */
    private $_city = '';

    /** 
     * domain 
     *
     * Domain of the google website.
     * 
     * @var string
     * @access private
     */
    private $_domain = 'www.google.com';

    /** 
     * _prefixImages 
     *
     * Prefix of the img link.
     * 
     * @var string
     * @access private
     */
    private $_prefixImages = '';

    /** 
     * _currentConditions 
     *
     * Array with current weather.
     * 
     * @var array
     * @access private
     */
    private $_currentConditions = array();

    /** 
     * _forecastConditions 
     *
     * Array with forecast weather.
     * 
     * @var array
     * @access private
     */
    private $_forecastConditions = array();

    /** 
     * _isFound 
     *
     * If the city was found.
     * 
     * @var mixed
     * @access private
     */
    private $_isFound = true;

    /** 
     * response 
     *
     * The HTML response send by the service.
     * 
     * @var mixed
     * @access private
     */
    private $_response;

    /** 
     * Class constructor.
     *
     * @param mixed $_cityCode Label of the city.
     * @param string $lang Lang of the return weather labels.
     * 
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @return return
     */
    public function __construct ($_cityCode,$lang='fr') {/*{{{*/
        $this->_cityCode = $_cityCode;
        $this->_prefixImages = 'http://'.$this->domain;
        $this->url = 'http://'.$this->domain.'/ig/api?weather='.urlencode($this->_cityCode).'&hl='.$lang;

        $getContentCode = $this->getContent($this->url);

        if($getContentCode == 200) {

            $content = utf8_encode($this->response);

            $xml = simplexml_load_string($content);

            if(!isset($xml->weather->problem_cause)) {

                $xml = simplexml_load_string($content);

                $this->city = (string)$xml->weather->forecast_information->city->attributes()->data;

                $this->_currentConditions['condition'] = (string)$xml->weather->_currentConditions->condition->attributes()->data;
                $this->_currentConditions['temp_f'] = (string)$xml->weather->_currentConditions->temp_f->attributes()->data;
                $this->_currentConditions['temp_c'] = (string)$xml->weather->_currentConditions->temp_c->attributes()->data;
                $this->_currentConditions['humidity'] = (string)$xml->weather->_currentConditions->humidity->attributes()->data;
                $this->_currentConditions['icon'] = $this->_prefixImages.(string)$xml->weather->_currentConditions->icon->attributes()->data;
                $this->_currentConditions['wind_condition'] = (string)$xml->weather->_currentConditions->wind_condition->attributes()->data;

                foreach($xml->weather->_forecastConditions as $this->_forecastConditions_value) {
                    $this->_forecastConditions_temp = array();
                    $this->_forecastConditions_temp['day_of_week'] = (string)$this->_forecastConditions_value->day_of_week->attributes()->data;
                    $this->_forecastConditions_temp['low'] = (string)$this->_forecastConditions_value->low->attributes()->data;
                    $this->_forecastConditions_temp['high'] = (string)$this->_forecastConditions_value->high->attributes()->data;
                    $this->_forecastConditions_temp['icon'] = $this->_prefixImages.(string)$this->_forecastConditions_value->icon->attributes()->data;
                    $this->_forecastConditions_temp['condition'] = (string)$this->_forecastConditions_value->condition->attributes()->data;
                    $this->_forecastConditions []= $this->_forecastConditions_temp;
                }
            } else {
                $this->_isFound = false;
            }

        } else {
            trigger_error('Google results parse problem : http error '.$getContentCode,E_USER_WARNING);
            return null;
        }
    }/*}}}*/

    /** 
     * Get URL content using cURL. 
     *
     * @param mixed $url The url.
     * 
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @return string the html code.
     */
    public function getContent($url)/*{{{*/
    {
        if (!extension_loaded('curl')) {
            throw new Exception('curl extension is not available');
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, $url);
        $this->response = curl_exec($curl);
        $infos = curl_getinfo($curl);
        curl_close ($curl);
        return $infos['http_code'];
    }/*}}}*/

    /** 
     * Get the city. 
     * 
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @return string The city.
     */
    function getCity() {/*{{{*/
        return $this->city;
    }/*}}}*/

    /** 
     * Get the current weather.
     * 
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @return array Current weather.
     */
    function getCurrent() {/*{{{*/
        return $this->_currentConditions;
    }/*}}}*/

    /** 
     * Get the forecast weather.
     * 
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @return array Forecast weather.
     */
    function getForecast() {/*{{{*/
        return $this->_forecastConditions;
    }/*}}}*/

    /** 
     * If the city was found. 
     * 
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @return boolean TRUE if found else FALSE.
     */
    function isFound() {/*{{{*/
        return $this->_isFound;
    }/*}}}*/
}
