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
 * @package   Gezere_Geolocator
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Geolocator.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

/**
 * @see Gezere_Geolocator_Exception
 */
require_once( 'Gezere/Geolocator/Exception.php' );

/**
 * Class that help implement geolocalization functionalities.
 *
 * @category Gezere
 * @package  Gezere_Geolocator
 * @author   Sylvain Lévesque <slevesque@gezere.com>
 */
class Gezere_Geolocator
{
	const UNIT_KILOMETER = 6371;
	const UNIT_MILES = 3693;
	const UNIT_NAUTICAL_MILES = 3444;

    private $databaseLink;
    private $postalCodesTable;
    private $dealersTable;

	public function __construct( $databaseLink, $postalCodesTable, $dealersTable )/*{{{*/
	{
        $this->databaseLink = $databaseLink;
        $this->postalCodesTable = $postalCodesTable;
        $this->dealersTable = $dealersTable;
	}/*}}}*/

	public function fetchPlaces( $postalCode, $radius = 25, $unit = self::UNIT_KILOMETER )/*{{{*/
	{
		$postalCode = preg_replace( '/\s/', '', strtoupper( $postalCode ) );
		$postalCode = substr( $postalCode, 0, 3 ) . ' ' . substr( $postalCode, 3, 3 );

		$sql = 'SELECT s.*, ( ' . $unit . ' * ACOS( COS( RADIANS( p2.latitude ) ) * COS( RADIANS( p.latitude ) ) * COS( RADIANS( p.longitude ) - RADIANS( p2.longitude ) ) + SIN( RADIANS( p2.latitude ) ) * SIN( RADIANS( p.latitude ) ) ) ) AS distance';
		$sql .= ' FROM ' . gTABLE_DEALERS . ' as s';
		$sql .= ' INNER JOIN ' . gTABLE_POSTAL_CODES . ' as p ON s.postalCode = p.postalCode';
		$sql .= ' INNER JOIN ' . gTABLE_POSTAL_CODES . ' as p2 ON p2.postalCode = "' . $postalCode . '"';
		$sql .= ' HAVING distance < "' . $radius . '"';
		$sql .= ' ORDER BY distance';
		return  $this->databaseLink->query( $sql );
	}/*}}}*/

	public function fetchDealers2( $postalCode, $radius = '25' )/*{{{*/
	{
		$postalCode = preg_replace( '/\s/', '', strtoupper( $postalCode ) );
		$postalCode = substr( $postalCode, 0, 3 ) . ' ' . substr( $postalCode, 3, 3 );

		$databaseLink = $this->databaseLink;
		$postalCodesTable = $this->postalCodesTable;
		$dealersTable = $this->dealersTable;

		// Fetch latitude and longitude of seaching postal code.

		$sql = 'SELECT latitude, longitude FROM ' . $postalCodesTable . ' WHERE postalCode  = "' . $postalCode . '"';

		$codes = $databaseLink->query( $sql );

		if( $codes === false || $databaseLink->numRows( $codes ) === 0 )
		{
			throw new Gezere_Geolocator_Exception( 'Unknown postal code !' );
		}

		$code = $databaseLink->fetchObject( $codes );

		$allPostalCodes = $this->getSpacialProximity( $code->latitude, $code->longitude, $radius );

		$sql = 'SELECT DISTINCT postalCode FROM %s';

		$sql = sprintf( $sql, $dealersTable );

		$allDealersPostalCodes = $databaseLink->query( $sql );

		while( $allDealersPostalCode = $databaseLink->fetchObject( $allDealersPostalCodes ) )
		{
			$arrAllDealersPostalCodes[] = $allDealersPostalCode->postalCode;
		}

		foreach( $allPostalCodes as $currentPostalCode )
		{
			if( in_array( $currentPostalCode[ 'postalCode' ], $arrAllDealersPostalCodes ) )
			{
				$dealersArray[] = $currentPostalCode;
			}
		}

		if( $dealersArray === null )
		{
			throw new Gezere_Geolocator_Exception( 'No dealer found !' );
		}


		foreach( $dealersArray as $dealerArray )
		{
			$arrDealersArray[] = $dealerArray[ 'postalCode' ];
		}


		if( count( $arrDealersArray ) > 1 )
		{
			$arrDealersArrayString = '"' . implode( '","', $arrDealersArray ) . '"';
			$sql = 'SELECT * FROM %s WHERE postalCode IN ( %s )';
			$sql = sprintf( $sql, $dealersTable, $arrDealersArrayString );
		}
		else
		{
			$sql = 'SELECT * FROM %s WHERE postalCode = "%s"';
			$sql = sprintf( $sql, $dealersTable, $arrDealersArray[ 0 ] );
		}

		foreach( $allPostalCodes as $code )
		{
			$distances[ $code[ 'postalCode' ] ] = $code[ 'distance' ];
		}

		$dealers = $databaseLink->query( $sql );

		while( $dealer = $databaseLink->fetchAssoc( $dealers ) )
		{
			$dealer[ 'distance' ] = $distances[ $dealer[ 'postalCode' ] ];
			$arrDealers[ ] = $dealer;
		}


		usort( $arrDealers, array( $this, 'dealerSort' ) );

		return $arrDealers;
	}/*}}}*/

/* 
 * @get cities within $distance 
 * @param int $latitude
 * @param int $longitude
 * @param int $distance, default 25
 * @param int $unit, default kilomenters
 * @return int
 */
 function getSpacialProximity( $latitude, $longitude, $distance=25, $unit='k')/*{{{*/
 {
	$postalCodesTable = $this->postalCodesTable;
	$databaseLink = $this->databaseLink;

    /*** distance unit ***/
    switch ($unit):
    /*** miles ***/
    case 'm':
        $unit = 3963;
        break;
    /*** nautical miles ***/
    case 'n':
        $unit = 3444;
        break;
    default:
        /*** kilometers ***/
        $unit = 6371;
    endswitch;


    /*** the sql ***/
    $sql = "SELECT postalCode, ( %s * ACOS( COS( RADIANS( %s ) ) * COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS( %s ) ) + SIN( RADIANS( %s ) ) * SIN( RADIANS( latitude ) ) ) ) AS distance FROM %s HAVING distance < %s ORDER BY distance";

	$sql = sprintf(
		$sql,
		$unit,
		$latitude,
		$longitude,
		$latitude,
		$postalCodesTable,
		$distance
	);

	$postalCodes = $databaseLink->query( $sql );

	$arrPostalCodes = array();

	while( $postalCode = $databaseLink->fetchAssoc( $postalCodes ) )
	{
		array_push( $arrPostalCodes, $postalCode );
	}

	return $arrPostalCodes;
}/*}}}*/

	private function dealerSort( $a, $b )/*{{{*/
	{
		if ( $a[ 'distance' ] == $b[ 'distance' ] )
		{
			return 0;
		}
		return ( $a[ 'distance' ] < $b[ 'distance' ] ) ? -1 : 1;
	}/*}}}*/
}
