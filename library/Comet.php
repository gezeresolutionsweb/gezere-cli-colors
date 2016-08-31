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
 * @package   Gezere_Comet
 * @author    Sylvain Lévesque <slevesque@gezere.com>
 * @copyright 2006-2013 Gezere Solutions Web (www.gezere.com)
 * @license   http://www.gnu.org/licenses/ GPLv3 license
 * @version   SVN: $Id: Comet.php 25989 2016-08-31 19:27:35Z slevesque $
 * @link      http://www.gezere.com/
 */

namespace gezere;

/** Gezere_Comet class 
 *
 * php comet client implementing Bayeux protocol.
 *
 * @author  Sylvain Lévesque
 * @package comet
 *
 * <code>
 *  require_once( 'Gezere/Comet.php' );
 *  
 *  // Instance the Comet object
 *  $objComet = new Comet();
 *  
 *  // Handshake  with the server
 *  $objComet->handshake();
 *  
 *  // Connect to the server
 *  $objComet->connect();
 *  
 *  // Subscribe to a channel (but you won't receive events from this channel, so this is useless for now)
 *  $objComet->subscribe( '/path/to/channel' );
 *  
 *  // Send a message to the channel
 *  $objComet->publish( '/path/to/channel' , array( 'message' => 'Salut la compagnie', 'module' => 'chat' ) );
 *
 *  // Unsubscribing to a channel
 *  $objComet->unsubscribe( '/path/to/channel' );
 *  
 * </code>
 */
class Comet
{
     // Private class members/*{{{*/
     /** $server 
      *
      * Server address.
      *
      * @access private
      * @var    string
      */
     private $server;

     /** $port 
      *
      * Server port.
      *
      * @access private
      * @var    string
      */
     private $port;

     /** $isHandshaked 
      *
      * Handshaking is done ?
      *
      * @access private
      * @var    boolean
      */
     private $isHandshaked = false;

     /** $clientId 
      *
      * Client id.
      *
      * @access private
      * @var    string
      */
     private $clientId;

     /** $supportedConnectionTypes 
      *
      * Contain server supported connection types
      *
      * @access private
      * @var    integer
      */
     private $supportedConnectionTypes;

     /** $connectionTypes 
      *
      * Connection types associative array.
      *
      * @access private
      * @var    array
      */
     private $connectionTypes = array();

     private $connection = null;
     private $maxlen = 8192;
     /*}}}*/

     // Supported connection type constants/*{{{*/
     public static $COMET_IFRAME = 1;
     public static $COMET_IE_MESSAGE_BLOCK = 2;
     public static $COMET_MIME_MESSAGE_BLOCK = 4;
     public static $COMET_CALLBACK_POLLING = 8;
     public static $COMET_LONG_POLLING = 16;
     public static $COMET_HTTP_POLLING = 32;
     public static $COMET_ALL_SUPPORTED_CONNECTION_TYPES = 63;
     /*}}}*/

     private $messages = array();
     public function getOutstandingMessages()/*{{{*/
     {
          $messages = $this->messages;
          $this->messages = array();
          return $messages;
     }/*}}}*/

     private $seqId = 0;
     private function getSequenceId()/*{{{*/
     {
          return (string) $this->seqId++;
     }/*}}}*/

     /** __construct {{{
      *
      * Class constructor.
      *
      * @access public
      * @author Sylvain Lévesque
      * @param  string $server Comet server address.
      * @param  integer $port Comet server port.
      *//*}}}*/
     public function __construct( $server = '127.0.0.1', $port = 8080 )/*{{{*/
     {
          $this->server = $server;
          $this->port   = $port;

          $this->connectionTypes['iframe']             = self::$COMET_IFRAME;
          $this->connectionTypes['ie-message-block']   = self::$COMET_IE_MESSAGE_BLOCK;
          $this->connectionTypes['mime-message-block'] = self::$COMET_MIME_MESSAGE_BLOCK;
          $this->connectionTypes['callback-polling']   = self::$COMET_CALLBACK_POLLING;
          $this->connectionTypes['long-polling']       = self::$COMET_LONG_POLLING;
          $this->connectionTypes['http-polling']       = self::$COMET_HTTP_POLLING;

          $this->handshake();
          $this->connect();
     }/*}}}*/

     /** handshake {{{
      *
      * Do the handshake with the comet server.
      *
      * @access public
      * @author Sylvain Lévesque
      * @param  integer $supportedConnectionType Supported connection list.
      *
      * <code>
      * $this->handshake( self::$COMET_IFRAME | self::$COMET_LONG_POLLING | self::$COMET_HTTP_POLLING );
      * </code>
      *
      * @return TRUE if handshake successfull
      * @throw  Exception
      *//*}}}*/
     public function handshake( $supportedConnectionTypes = null )/*{{{*/
     {
          if ($supportedConnectionTypes === null)
          {
               $supportedConnectionTypes = self::$COMET_LONG_POLLING;
          }

          if( $this->isHandshaked )
          {
               throw new Exception( 'Already handshaked !' );
          }

          if( !$this->isValidSupportedConnectionTypes( $supportedConnectionTypes ) )
          {
               throw new Exception( 'Supported connection types not valid !' );
          }

          $json = array();
          $json['id'] = $this->getSequenceId();
          $json['version']        = '1.0';
          $json['minimumVersion'] = '1.0';
          $json['channel']        = '/meta/handshake';
          $json['supportedConnectionTypes'] = $this->supportedConnectionTypesInteger2Array( $supportedConnectionTypes );

          // Decoding json string.
          $response = $this->sendSocketMessage( $json );

          // Validate if handshake successful.
          if( $response[0]['successful'] != true && $response[0]['channel'] === '/meta/handshake' )
          {
               throw new Exception('Error handshaking comet !');
          }

          // Validation to see if clientId has been generated properly.
          if( empty( $response[0]['clientId'] ) )
          {
               throw new Exception('Generated client id is empty !');
          }

          // Setting some member variables.
          $this->isHandshaked             = true;
          $this->clientId                 = $response[0]['clientId'];
          $this->supportedConnectionTypes = $this->supportedConnectionTypesArray2Integer( $response[0]['supportedConnectionTypes'] );

          return true;
     }/*}}}*/

     /** connect {{{
      *
      * Do the connection with the comet server.
      *
      * @access public
      * @author Sylvain Lévesque
      * @param  interger $connectionType Connection type to use with this connection.
      * @return TRUE is connection successful.
      *//*}}}*/
     public function connect( $connectionType = null )/*{{{*/
     {		
          if ( $connectionType === null)
          {
               $connectionType = self::$COMET_LONG_POLLING;
          }

          if( !$this->isHandshaked )
          {
               throw new Exception('You must first do a handshake !');
          }

          if( !$this->isValidSupportedConnectionTypes( $connectionType ) )
          {
               throw new Exception( 'Connection type is not valid !' );
          }

          if( !$this->isSupportedConnectionType( $connectionType ) )
          {
               throw new Exception( 'Connection type not supported by the server !' );
          }

          $json = array();
          $json['id'] = $this->getSequenceId();
          $json['channel'] = '/meta/connect';
          $json['clientId'] = $this->clientId;
          $json['connectionType'] = $this->getConnectionTypeAsString( $connectionType );

          $response  = $this->sendSocketMessage( $json );

          // Validate if connect successful.
          if( $response[0]['successful'] != true && $response[0]['channel'] === '/meta/connect' )
          {
               throw new Exception('Error connecting comet !');
          }

          // Remember messages passed from the Cometd server
          for ($i = 1; $i < count($response); $i++)
          {
               $this->messages[] = $response[$i];
          }

          return true;
     }/*}}}*/

     /** close {{{
      *
      * Close the socket connection.
      *
      * @access public
      * @author Sylvain Lévesque
      * @return TRUE if success.
      *//*}}}*/
     public function close()/*{{{*/
     {
          // Verify if we are connected before disconnecting
          if( !$this->isHandshaked )
          {
               //throw new Exception( 'Can\'t close connection, you\'re not connected !' );
                return false;
          }

          $json = array();
          $json['id'] = $this->getSequenceId();
          $json['channel'] = '/meta/disconnect';
          $json['clientId'] = $this->clientId;

          $response  = $this->sendSocketMessage( $json );

          // Validate if disconnect successful.
          if( $response[0]['successful'] != true && $response[0]['channel'] === '/meta/disconnect' )
          {
               //throw new Exception('Error disconnecting comet !');
                return false;
          }

          $this->isHandshaked = false;

          return true;
     }/*}}}*/

     /** subscribe {{{
      *
      *
      * This function permit to subscribe to a comet channel.
      *
      * @access public
      * @author Sylvain Lévesque
      * @param  string $channel Channel name.
      * @todo   Bayeux protocol supposed to be able to suscribe to multiple channels in one shot
      *         by passing an array of channels but never get it to work.
      * @true   TRUE is success.
      *//*}}}*/
     public function subscribe( $channel )/*{{{*/
     {
          // is a string ?
          if( !is_string( $channel ) )
          {
               //throw new Exception( 'Channel must be a string !' );
                return false;
          }

          if( !$this->isValidChannel( $channel ) || preg_match( '/^\/meta\//', $channel ) )
          {
               //throw new Exception( 'Channel ' . $channel . ' is not a valid format !' );
                return false;
          }

          $json = array();
          $json['id'] = $this->getSequenceId();
          $json['channel']      = '/meta/subscribe';
          $json['clientId']     = $this->clientId;
          $json['subscription'] = $channel;

          $response = $this->sendSocketMessage( $json );

          if( $response[0]['successful'] !== true && $response[0]['channel'] === '/meta/subscribe' )
          {
               //throw new Exception( 'Error subscribing channel !' );
                return false;
          }

          return true;
     }/*}}}*/

     /** unsubscribe {{{
      *
      * This function permit to unsubscribe from a channel.
      *
      * @access public
      * @author Sylvain Lévesque
      * @param  string $channel Channel name to unsubscribe.
      * @return True if success.
      * @todo   Cometd crash when trying to unsubscribe a channel.
      *//*}}}*/
     public function unsubscribe( $channel )/*{{{*/
     {
          // Is a string ?
          if( !is_string( $channel ) )
          {
               //throw new Exception( 'Channel is not a string !' );
                return false;
          }

          // Channel is in valid format ?
          if( !$this->isValidChannel( $channel ) || preg_match( '/^\/meta\//', $channel ) )
          {
               //throw new Exception( 'Channel ' . $channel . ' is not a valid format !' );
                return false;
          }

          $json = array();
          $json['id'] = $this->getSequenceId();
          $json['channel']      = '/meta/unsubscribe';
          $json['clientId']     = $this->clientId;
          $json['subscription'] = $channel;

          $response = $this->sendSocketMessage( $json );

          if( $response[0]['successful'] != true && $response[0]['channel'] === '/meta/unsubscribe' )
          {
               //throw new Exception( 'Error unsubscribing channel !' );
                return false;
          }

          return true;
     }/*}}}*/

     /** publish {{{
      *
      * This function permit to sent an event to a channel.
      *
      * @access public
      * @author Sylvain Lévesque
      * @param  string $channel Channel name.
      * @param  array $message Array of event to be transform in json string.
      * @return TRUE if success.
      *//*}}}*/
     public function publish( $channel, $message )/*{{{*/
     {
          if( !is_string( $channel ) )
          {
               //throw new Exception( 'Channel is not a string !' );
                return false;
          }

          if( !$this->isValidChannel( $channel ) )
          {
               //throw new Exception( 'Channel ' . $channel . ' is not a valid format !' );
                return false;
          }

          if( !is_array( $message ) )
          {
               //throw new Exception( 'Json is not an array !' );
                return false;
          }

          $json = array();
          $json['id'] = $this->getSequenceId();
          $json['channel']  = $channel;
          $json['clientId'] = $this->clientId;
          $json['data']     = $message;
          $json['id']       = uniqid();

          $response = $this->sendSocketMessage( $json );

          if( isset($response[0]['successfull']) && $response[0]['successfull'] !== true && $response[0]['channel'] === $channel )
          {
               //throw new Exception( 'Error publishing to comet !' );
                return false;
          }

          return true;
     }/*}}}*/

     private function socketConnect()/*{{{*/
     {
          if( !$this->connection = fsockopen( $this->server, $this->port ) )
          {
               //throw new Exception( 'Error opening the socket !' );
                return false;
          }
     }/*}}}*/
     private function parseAnswer( $answer )/*{{{*/
     {
          // Taking only the json answer part.
          if( !preg_match( '/\[\{.*\}\]/', $answer, $matches ) )
          {
               //throw new Exception('Invalid response string !');
                return false;
          }

          // Decoding json string.
          return json_decode( $matches[0], true );
     }/*}}}*/

     /** sendSocketMessage {{{
      *
      * This function send network message through an open socket.
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  array $json Array of element to be transform in json in the comet message to be send.
      * @return string Json message.
      *//*}}}*/
     private function sendSocketMessage( $json )/*{{{*/
     {
          $content = '['. json_encode( $json ) ."]\r\n\r\n";

          $message = "POST /cometd/cometd/ HTTP/1.1\r\n";

          $message .= 'Host: ' . $this->server . ':' . $this->port . "\r\n";
          $message .= "Connection: Keep-Alive\r\n";
          $message .= "Content-Type: text/json;charset=UTF-8\r\n";
          $message .= "Accept-Encoding: chunked\r\n";
          $message .= 'Content-Length: '. strlen($content) ."\r\n";
          $message .= "User-Agent: PHP-CometD\r\n";
          $message .= "\r\n";

          $message .= $content;

          if ($this->connection === null)
          {
               $this->socketConnect();
          }

          // Writing the message to the socket.
          if(!fputs( $this->connection , $message ))
          {
               //throw new Exception('Error writing to socket !');
                return false;
          }

          $headers = fgets($this->connection, $this->maxlen);

          if (!$headers) { // if disconnected meanwhile
               $this->socketConnect();
               fputs($this->connection, $message);
               $headers = fgets($this->connection, $this->maxlen);
          }

          preg_match('|^HTTP.+? (\d+?) |', $headers, $matches);

          $status = $matches[1];

          $type = '';
          $connection = '';
          $encoding = '';

          while ($line = fgets($this->connection, $this->maxlen)) {
               if ($line == "\r\n") { break; }

               if (preg_match('/^Content-Length: (.+)/', $line, $matches)) {
                    $length = (int) trim($matches[1]);
               }

               if (preg_match('/^Content-Type: (.+)/', $line, $matches)) {
                    $type = strtolower(trim($matches[1]));
               }

               if (preg_match('/^Connection: (.+)/', $line, $matches)) {
                    $connection = strtolower(trim($matches[1]));
               }

               if (preg_match('/^Transfer-Encoding: (.+)/', $line, $matches)) {
                    $encoding = strtolower(trim($matches[1]));
               }

               $headers .= $line;
          }

          $body = '';
          if ($connection == 'close') {
               while (!feof($this->connection)) {
                    $body .= fread($this->connection, $this->maxlen);
               }
               fclose($this->connection);
               $this->connection = null; // reset to initial state
               return $this->parseAnswer($body);
          }

          if (isset($length) && strpos($encoding, 'chunked') === false) {
               $body = fread($this->connection, $length);
               return $this->parseAnswer($body);
          }

          // chunked encoding
          $length = rtrim(fgets($this->connection, $this->maxlen));
          $length = hexdec($length);

          while (true) {
               if ($length < 1) { break; }
               $body .= fread($this->connection, $length);


               fgets($this->connection, $this->maxlen);
               $length = rtrim(fgets($this->connection, $this->maxlen));
               $length = hexdec($length);
          }

          fgets($this->connection, $this->maxlen);

          return $this->parseAnswer($body);

     }/*}}}*/

     /** supportedConnectionTypesInteger2Array {{{
      *
      * This function convert supported connection types integer as string.
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  integer $supportedConnectionTypes Supported connection types as integer.
      * @return array Supported connection types array.
      *//*}}}*/
     private function supportedConnectionTypesInteger2Array( $supportedConnectionTypes )/*{{{*/
     {
          if(!$this->isValidSupportedConnectionTypes( $supportedConnectionTypes ))
          {
               //throw new Exception('Supported connection types is invalid !');
                return false;
          }

          $elements = array();

          if( ( $supportedConnectionTypes & self::$COMET_IFRAME ) === self::$COMET_IFRAME )
          {
               array_push( $elements , 'iframe' );
          }

          if( ( $supportedConnectionTypes & self::$COMET_IE_MESSAGE_BLOCK ) === self::$COMET_IE_MESSAGE_BLOCK )
          {
               array_push( $elements , 'ie-message-block' );
          }

          if( ( $supportedConnectionTypes & self::$COMET_MIME_MESSAGE_BLOCK ) === self::$COMET_MIME_MESSAGE_BLOCK )
          {
               array_push( $elements , 'mime_message_block' );
          }

          if( ( $supportedConnectionTypes & self::$COMET_CALLBACK_POLLING ) === self::$COMET_CALLBACK_POLLING )
          {
               array_push( $elements , 'callback-polling' );
          }

          if( ( $supportedConnectionTypes & self::$COMET_LONG_POLLING ) === self::$COMET_LONG_POLLING )
          {
               array_push( $elements , 'long-polling' );
          }

          if( ( $supportedConnectionTypes & self::$COMET_HTTP_POLLING ) === self::$COMET_HTTP_POLLING )
          {
               array_push( $elements , 'http-polling' );
          }

          return $elements;
     }/*}}}*/

     /** supportedConnectionTypesArray2Integer {{{
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  array $supportedConnectionTypes Supported connection types array.
      * @return integer Supporter connection types as integer.
      *//*}}}*/
     private function supportedConnectionTypesArray2Integer( $supportedConnectionTypes )/*{{{*/
     {
          if( !is_array( $supportedConnectionTypes ) )
          {
               //throw new Exception('Supported connection types not an array !');
                return false;
          }
     }/*}}}*/

     /** getConnectionTypeAsString {{{
      *
      * This function return the connection type as string.
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  integer $connectionType Connection type as integer
      * @return string Connection type as string.
      *//*}}}*/
     private function getConnectionTypeAsString( $connectionType )/*{{{*/
     {
          $arrConnectionTypes = array_flip( $this->connectionTypes );
          return $arrConnectionTypes[ $connectionType ];
     }/*}}}*/

     /** isValidSupportedConnectionTypes  {{{
      *
      * This function verify is the supported connection types interger passed is valid.
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  interger $supportedConnectionTypes Supported connection types as interger.
      * @retrurn TRUE if valid else false.
      *//*}}}*/
     private function isValidSupportedConnectionTypes( $supportedConnectionTypes )/*{{{*/
     {
          if( !is_integer( $supportedConnectionTypes ) || $supportedConnectionTypes < 1 || $supportedConnectionTypes > 63 )
          {
               return false;
          }

          return true;
     }/*}}}*/

     /** isSupportedConnectionType {{{
      *
      * Retourn if a connection type is supported by the server.
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  integer $connectionType Connection type as integer.
      * @return TRUE if supported else false.
      *//*}}}*/
     private function isSupportedConnectionType( $connectionType )/*{{{*/
     {
          if( ( self::$COMET_ALL_SUPPORTED_CONNECTION_TYPES & $connectionType ) === $connectionType )
          {
               return true;
          }

          return false;
     }/*}}}*/

     /** isValidChannel {{{
      *
      * Valid the channel string.
      *
      * @access private
      * @author Sylvain Lévesque
      * @param  string $channel Channel string.
      * @return boolean TRUE if valid else false.
      *//*}}}*/
     public function isValidChannel( $channel )/*{{{*/
     {
          // @todo : Validate the regular expression here
          return true ;
          return preg_match( '/^\/[\da-zA-Z_\/]+([\da-zA-Z_]|\/\*{1,2})$/', $channel );
     }/*}}}*/
}
