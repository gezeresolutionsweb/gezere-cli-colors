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
 * @package   Gezere_Matrix
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Matrix.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

class Gezere_Matrix {
    function rotateClockwise90( $matrix ) {
        $newMatrix = array();

        foreach( $matrix as $line ) {
            foreach( $line as $k => $v ) {
                if( !isset( $newMatrix[ $k ] ) ) {
                    $newMatrix[ $k ] = array();
                }
                array_unshift( $newMatrix[ $k ], $v );
            }

        }
        return $newMatrix;
    }

    function rotateClockwise180( $matrix ) {
        return $this->rotateClockwise90( $this->rotateClockwise90( $matrix ) );
    }

    function rotateClockwise270( $matrix ) {
        return $this->rotateClockwise180( $this->rotateClockwise90( $matrix ) );
    }
}
