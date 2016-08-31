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
 * @package   Gezere_Mail
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Mail.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

/**
 * Mail class
 *
 * This class is a wrapper around php mail() function.
 *
 * @category Gezere
 * @package  Gezere_Mail
 * @author   Sylvain Lévesque <slevesque@gezere.com>
 * @todo     Need documentation.
 * @todo     Chain object with Command chain Pattern
 *
 * <code>
 * $m = new Gezere_Mail();
 *
 * $m->setEncoding( Gezere_Mail::ENCODING_ISO8859_1 );
 * $m->setFrom( 'test@gezere.com' );
 * $m->setSubject( 'Test de mail' );
 * $m->setMessage( 'This is a new message !ééééé<h1>asdfasdf</h1>' );
 * $m->addTo( 'slevesque@gezere.com' );
 * $m->addTo( 'info@gezere.com' );
 * $m->addCc( 'slevesque@axya.org' );
 * $m->addCc( 'info@axya.org' );
 * $m->addBcc( 'slevesque@sylvainlevesque.com' );
 * $m->addBcc( 'info@sylvainlevesque.com' );
 * $m->setHtml();
 * $m->send();
 * </code>
 */
class Gezere_Mail
{
    const ENCODING_UTF8 = 'utf-8';
    const ENCODING_ISO8859_1 = 'iso-8859-1';

    const TYPE_HTML = 'text/html';
    const TYPE_PLAIN = 'text/plain';

    protected $to = array();
    protected $cc = array();
    protected $bcc = array();
    protected $from;
    protected $subject;
    protected $message;
    protected $headers = array();
    protected $type = self::TYPE_PLAIN;
    protected $encoding = self::ENCODING_UTF8;

    public function __construct() {/*{{{*/
    }/*}}}*/

    public function send() {/*{{{*/
        if( !empty( $this->cc ) ) {
            $this->addHeader( 'Cc', implode( ',', $this->cc ) );
        }

        if( !empty( $this->bcc ) ) {
            $this->addHeader( 'Bcc', implode( ',', $this->bcc ) );
        }

        if( !empty( $this->from ) ) {
            $this->addHeader( 'From', $this->from );
        }

        if( empty( $this->headers ) ) {
            return mail( implode( ',', $this->to ), $this->subject, $this->message );
        } else {
            return mail( implode( ',', $this->to ), $this->subject, $this->message, $this->headersToString() );
        }
    }/*}}}*/

    public function addTo( $to ) {/*{{{*/
        array_push( $this->to, $to );
    }/*}}}*/

    public function addTos( $tos ) {/*{{{*/
        foreach( $tos as $to ) {
            array_push( $this->to, $to );
        }
    }/*}}}*/

    public function getTo() {/*{{{*/
        return $this->to;
    }/*}}}*/

    public function addCc( $cc ) {/*{{{*/
        array_push( $this->cc, $cc );
    }/*}}}*/

    public function addCcs( $ccs ) {/*{{{*/
        foreach( $ccs as $cc ) {
            array_push( $this->cc, $cc );
        }
    }/*}}}*/

    public function addBcc( $bcc ) {/*{{{*/
        array_push( $this->bcc, $bcc );
    }/*}}}*/

    public function addBccs( $bccs ) {/*{{{*/
        foreach( $bccs as $bcc ) {
            array_push( $this->bcc, $bcc );
        }
    }/*}}}*/

    public function setSubject( $subject ) {/*{{{*/
        $this->subject = $subject;
    }/*}}}*/

    public function setMessage( $message ) {/*{{{*/
        $this->message = $message;
    }/*}}}*/

    public function addHeader( $name, $value ) {/*{{{*/
        $this->headers[ $name ] = $value;
    }/*}}}*/

    public function addHeaders( $headers ) {/*{{{*/
        foreach( $headers as $key => $value ) {
            $this->headers[ $key ] = $value;
        }
    }/*}}}*/

    private function headersToString()/*{{{*/
    {
        $headerString = '';
        foreach( $this->headers as $key => $value )
        {
            $headerString .= $key . ': ' . $value . PHP_EOL;
        }

        return $headerString;
    }/*}}}*/

    public function setFrom( $from, $name = null ) {/*{{{*/
        if( !empty( $name ) && !is_null( $name) ) {
            $this->from = $name . ' <' . $from . '>';
        } else {
            $this->from = $from;
        }
    }/*}}}*/

    public function setEncoding( $encoding ) {/*{{{*/
        if( $encoding !== self::ENCODING_UTF8 && $encoding !== self::ENCODING_ISO8859_1 ) {
            throw new InvalidArgumentException( 'Invalid encoding: ' . $encoding );
        }

        $this->encoding = $encoding;

        if( array_key_exists( 'Content-type', $this->headers ) ) {
            $this->setContentType();
        }
    }/*}}}*/

    public function setType( $type ) {/*{{{*/
        if( $type !== self::TYPE_HTML && $type !== self::TYPE_PLAIN ) {
            throw new InvalidArgumentException( 'Invalid type: ' . $type );
        }

        $this->type = $type;

        if( array_key_exists( 'Content-type', $this->headers ) ) {
            $this->setContentType();
        }
    }/*}}}*/

    private function setContentType( $type = null, $encoding = null ) {/*{{{*/
        $this->addHeader( 'Content-type', $this->type . '; charset=' . $this->encoding );
    }/*}}}*/

    private function setMimeVersion() {/*{{{*/
        $this->addHeader( 'Mime-version', '1.0' );
    }/*}}}*/

    public function setHtml() {/*{{{*/
        $this->setType( self::TYPE_HTML );
        $this->setMimeVersion();
        $this->setContentType( );
    }/*}}}*/
}
