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
 * @package   Gezere_Crypto
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Crypto.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

/**
 * Gezere_Crypto
 *
 * This is a mcrypt wrapper class that simplify the use. This class is base
 * on the works of Dustin Whittle in his phpFreaCrypto class.
 *
 * @author   Sylvain Lévesque <slevesque@gezere.com>
 * @category gezere
 * @package  crypto
 *
 * <code>
 *   $crypto = new Gezere_Crypto();
 *   $original = 'Hello world !';
 *   $encrypted = $crypto->encrypt( $original );
 *   $decrypted = $crypto->decrypt( $encrypted );
 *
 *   echo 'Original: ' . $original . PHP_EOL;
 *   echo 'Encrypted: ' . $encrypted . PHP_EOL;
 *   echo 'Decrypted: ' . $decryoted . PHP_EOL;
 * </code>
 */
class Gezere_Crypto
{
    /**
     * td
     *
     * @var mixed
     * @access private
     */
    private $td;

    /**
     *
     *
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @param string $key Private key string.
     * @param mixed $iv Private iv vector.
     * @param string $algorithm Algorythme to use.
     * @param string $mode Mode to use.
     */
    public function __construct( $key = 'MyRandomStringThatWillAlwaysBeTheSame', $iv = false, $algorithm = 'tripledes', $mode = 'ecb') { /*{{{*/
        if(extension_loaded( 'mcrypt' ) === FALSE) {
            throw Exception( 'mcrypt module is not loaded !' );
        }

        /**
         *  the iv must remain the same from encryption to decryption and is usually
         *  passed into the encrypted string in some form, but not always.
         */
        if( $mode !== 'ecb' && $iv === false ) {
            die('In order to use encryption modes other then ecb, you must specify a unique and consistent initialization vector.');
        }

        // set mcrypt mode and cipher
        $this->td = mcrypt_module_open( $algorithm, '', $mode, '' );

        // Unix has better pseudo random number generator then mcrypt, so if it is available lets use it!
        if( strstr( PHP_OS, "WIN") ) {
            $random_seed = MCRYPT_RAND;
        } else {
            $random_seed = MCRYPT_DEV_RANDOM;
        }

        // if initialization vector set in constructor use it else, generate from random seed
        if( $iv === false ) {
            $iv = mcrypt_create_iv( mcrypt_enc_get_iv_size( $this->td ), $random_seed );
        } else {
            $iv = substr( $iv, 0, mcrypt_enc_get_iv_size( $this->td ) );
        }

        // get the expected key size based on mode and cipher
        $expected_key_size = mcrypt_enc_get_key_size( $this->td );

        // we dont need to know the real key, we just need to be able to confirm a hashed version
        $key = substr( md5( $key ), 0, $expected_key_size );

        // initialize mcrypt library with mode/cipher, encryption key, and random initialization vector
        mcrypt_generic_init( $this->td, $key, $iv );
    } /*}}}*/

    /**
     * Encrypt a plain string.
     *
     * Encrypt string using mcrypt and then encode any special characters
     * and then return the encrypted string .
     *
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @param mixed $plain_string Plain string to encrypt.
     * @return string Encrypted string
     */
    function encrypt( $plain_string ) /*{{{*/
    {
        return base64_encode( urlencode( mcrypt_generic( $this->td, $plain_string ) ) );

    } /*}}}*/

    /**
     * Decrypt an encrypted string.
     *
     * Remove any special characters then decrypt string using mcrypt and then trim null padding
     * and then finally return the encrypted string .
     *
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     * @param mixed $encrypted_string Encrypted string.
     * @return string Decrypted string.
     */
    function decrypt( $encrypted_string ) /*{{{*/
    {
        return trim( mdecrypt_generic( $this->td, urldecode( base64_decode( $encrypted_string ) ) ) );
    } /*}}}*/

    /**
     * Class destructor .
     *
     * @access public
     * @author Sylvain Lévesque <slevesque@gezere.com>
     */
    public function __destruct() { /*{{{*/
        // shutdown mcrypt
        mcrypt_generic_deinit($this->td);

        // close mcrypt cipher module
        mcrypt_module_close($this->td);
    } /*}}}*/
}
