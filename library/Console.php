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
 * @package   Gezere_Console
 * @author    Michaël Gagnon <mgagnon@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Console.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

/**
 * This class print debug trace.
 *
 * @category gezere
 * @package  console
 * @author	 Michaël Gagnon <mgagnon@gezere.com>.
 */

class Gezere_Console {

	/**
	 * Print debugging trace. 
	 *
	 * @param  mixed   $data    Data to printing or debugging.
	 * @param  string  $comment Trace comment.
	 * @param  integer $level   Level.
	 */
	public static function log ( $data, $comment, $level = 0 ) {/*{{{*/
		if ( LOG_ENABLED & ( $level <= LOG_LEVEL && LOG_DESC ) ||
            ( $level === LOG_LEVEL && !LOG_DESC ) ||
            ( LOG_LEVEL === -1 ) ) {

			print_r('<xmp>');
			print_r('---------------------');
			print_r('</xmp>');

			if ( $comment ) {
				print_r('<xmp>');
				print_r($comment);
				print_r('</xmp>');
			}
		
			print_r('<xmp>');
			print_r($data);
			print_r('</xmp>');
		}
	}/*}}}*/
}
