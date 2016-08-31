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
 * @package   Gezere_Server
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Proftpd.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

/**
 * @see Gezere_Server_Proftpd_Exception
 */
require_once( 'Gezere/Server/Proftpd/Exception.php' );

/**
 * Proftpd config file manager (generator).
 *
 * @category Gezere
 * @package  Gezere_Server
 * @author   Sylvain Lévesque <slevesque@gezere.com>
 */
class Gezere_Server_Proftpd
{
    private $passwdFilePath;

	public function __construct( $passwdFilePath )/*{{{*/
	{
        $this->passwdFilePath = $passwdFilePath;
	}/*}}}*/

	public function getUsers()/*{{{*/
	{
		$passwdFilePath = $this->passwdFilePath;
		if( !file_exists( $passwdFilePath ) )
		{
			throw new Gezere_Server_Proftpd_Exception( 'Passwd file ' . $passwdFilePath . ' doesn\'t exists !' );
		}

		return $this->parsePasswdFile( $passwdFilePath );
	}/*}}}*/

	private function parsePasswdFile( $file )/*{{{*/
	{
		$users = array();

		$lines = file( $file );

		foreach( $lines as $line )
		{
			$user = explode( ':', $line );
			
			array_push( $users, array(
				'uid' => $user[ 2 ],
				'name' => $user[ 0 ],
				'gecos' => $user[ 4 ],
				'encryptPassword' => $user[ 1 ],
				'home' => $user[ 5 ],
				'shell' => $user[ 6 ],
				'gid' => $user[ 3 ]
			) );
		}

		return $users;
	}/*}}}*/
}
