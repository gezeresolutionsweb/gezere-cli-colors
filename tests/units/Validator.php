<?php

namespace gezere\tests\units;

require_once( dirname( __FILE__ ) . '/../atoum.phar' );

include dirname( __FILE__ ) . '/../../library/Validator.php';

use \mageekguy\atoum;
use \gezere;

class Validator extends atoum\test {
    public function testIsValidDateTime() {/*{{{*/
        $validator = new gezere\Validator();
        $this->assert
            ->integer( $validator->isValidDateTime( '2012-03-08 12:00:01' ) )
            ->isEqualTo( 1 );

        $this->assert
            ->integer( $validator->isValidDateTime( '1900-01-01 00:00:00' ) )
            ->isEqualTo( 1 );

        $this->assert
            ->integer( $validator->isValidDateTime( '00:00:00 2005-09-03' ) )
            ->isEqualTo( 0 );

        $this->assert
            ->integer( $validator->isValidDateTime( '16-02-1979 12:00:00' ) )
            ->isEqualTo( 0 );

        $this->assert
            ->integer( $validator->isValidDateTime( '2009-02-16 32:00:00' ) )
            ->isEqualTo( 0 );
    }/*}}}*/
    public function testIsValidEmail() {    /*{{{*/
        $validator = new gezere\Validator();

        // Test good email.
        $this->assert
            ->integer( $validator->isValidEmail( 'test@agrimarche.qc.ca' ) )
            ->isEqualTo( 1 );

        // Test second good email
        $this->assert
            ->integer( $validator->isValidEmail( 'test@agri-marche.qc.ca' ) )
            ->isEqualTo( 1 );

        // Test email with only numbers before @.
        $this->assert
            ->integer( $validator->isValidEmail( '123@agri-marche.qc.ca' ) )
            ->isEqualTo( 1 );

        // Test another gouv type email 
        $this->assert
            ->integer( $validator->isValidEmail( 'SKJDHKSHDK@agri-marche.gouv.qc.ca' ) )
            ->isEqualTo( 1 );

        // Test bad email addresse with no @
        $this->assert
            ->integer( $validator->isValidEmail( 'SKJDHKSHDKagri-marche.gouv.qc.ca' ) )
            ->isEqualTo( 0 );
    }/*}}}*/
    public function testIsValidDate() {/*{{{*/
        $validator = new gezere\Validator();
        $this->assert
            ->integer( $validator->isValidDate( '2012-03-08' ) )
            ->isEqualTo( 1 );

        $this->assert
            ->integer($validator->isValidDate( '1900-10-27' ))
            ->isEqualTo( 1 );

        $this->assert
            ->integer($validator->isValidDate( '3009-10-27' ))
            ->isEqualTo( 0 );

        $this->assert
            ->integer($validator->isValidDate( '18-06-2009' ))
            ->isEqualTo( 0 );

        $this->assert
            ->integer($validator->isValidDate( '' ))
            ->isEqualTo( 0 );
    }/*}}}*/
    public function testIsValidUrl() {/*{{{*/
        $validator = new gezere\Validator();
        $this->assert->integer($validator->isValidUrl( 'http://www.gezere.com' ))->isEqualTo(1);
        $this->assert->integer($validator->isValidUrl( 'http:asdf//www.gezere.com' ))->isEqualTo(0);
    }/*}}}*/
    public function testIsValidRfc2822Email() {/*{{{*/
        $validator = new gezere\Validator();
        $this->assert->integer($validator->isValidRfc2822Email( 'gezere.com' ))->isEqualTo(0);
    }/*}}}*/
}
