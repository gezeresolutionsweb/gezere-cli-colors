<?php
namespace gezere\tests\units;

require_once( dirname( __FILE__ ) . '/../atoum.phar' );

include dirname( __FILE__ ) . '/../../library/Table.php';
include dirname( __FILE__ ) . '/../../library/XmlElement.php';

use \mageekguy\atoum;
use \gezere;

class Table extends atoum\test {
    public function testSetGetEvenRowClass() {/*{{{*/
        /*
        $this->assertEquals( $this->object->getEvenRowClass(), '' );
        $this->object->setEvenRowClass( 'even' );
        $this->assertEquals( $this->object->getEvenRowClass(), 'even' );
        $this->object->setEvenRowClass( 'even2' );
        $this->assertEquals( $this->object->getEvenRowClass(), 'even2' );
        */
    }/*}}}*/
    public function testSetGetOddRowClass() {/*{{{*/
/*
        $this->assertEquals( $this->object->getOddRowClass(), '' );
        $this->object->setOddRowClass( 'odd' );
        $this->assertEquals( $this->object->getOddRowClass(), 'odd' );
        $this->object->setOddRowClass( 'odd2' );
        $this->assertEquals( $this->object->getOddRowClass(), 'odd2' );
*/
    }/*}}}*/
    public function testTableOpen() {/*{{{*/
/*
        $this->assertEquals( $this->object->tableOpen(), '<table>' . PHP_EOL );
        $this->assertEquals( $this->object->tableOpen( 'myId' ), '<table id="myId">' . PHP_EOL );
*/
    }/*}}}*/
    public function testTableClose() {/*{{{*/
/*
        $this->assertEquals( $this->object->tableClose(), '</table>' . PHP_EOL );
*/
    }/*}}}*/
    public function testHeadOpen() {/*{{{*/
/*
        $this->assertEquals( $this->object->headOpen(), '<thead>' . PHP_EOL );
*/
    }/*}}}*/
    public function testHeadClose() {/*{{{*/
/*
        $this->assertEquals( $this->object->headClose(), '</thead>' . PHP_EOL );
*/
    }/*}}}*/
    public function testBodyOpen() {/*{{{*/
/*
        $this->assertEquals( $this->object->bodyOpen(), '<tbody>' . PHP_EOL );
*/
    }/*}}}*/
    public function testBodyClose() {/*{{{*/
/*
        $this->assertEquals( $this->object->bodyClose(), '</tbody>' . PHP_EOL );
*/
    }/*}}}*/
    public function testRowOpen() {/*{{{*/
/*
        $this->assertEquals( $this->object->rowOpen(), '<tr>' . PHP_EOL );
        $this->object->setEvenRowClass( 'even' );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="even">' . PHP_EOL );
        $this->assertEquals( $this->object->rowOpen(), '<tr>' . PHP_EOL );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="even">' . PHP_EOL );
        $this->object->setEvenRowClass( '' );
        $this->object->setOddRowClass( 'odd' );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="odd">' . PHP_EOL );
        $this->assertEquals( $this->object->rowOpen(), '<tr>' . PHP_EOL );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="odd">' . PHP_EOL );
        $this->object->setEvenRowClass( 'even' );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="even">' . PHP_EOL );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="odd">' . PHP_EOL );
        $this->assertEquals( $this->object->rowOpen(), '<tr class="even">' . PHP_EOL );
*/
    }/*}}}*/ 
    public function testRowClose() {/*{{{*/
/*
        $this->assertEquals( $this->object->rowClose(), '</tr>' . PHP_EOL );
*/
    }/*}}}*/
    public function testCell() {/*{{{*/
/*
        $this->assertEquals( $this->object->cell(), '<td>&nbsp;</td>' . PHP_EOL );
        $this->assertEquals( $this->object->cell( '' ), '<td>&nbsp;</td>' . PHP_EOL );
        $this->assertEquals( $this->object->cell( 'Cell content' ), '<td>Cell content</td>' . PHP_EOL );
        $this->assertEquals( $this->object->cell( 'Cell content', 'myClass' ), '<td class="myClass">Cell content</td>' . PHP_EOL );
*/
    }/*}}}*/
    public function testHeadCell() {/*{{{*/
/*
        $this->assertEquals( $this->object->headCell(), '<th>&nbsp;</th>' . PHP_EOL );
        $this->assertEquals( $this->object->headCell( '' ), '<th>&nbsp;</th>' . PHP_EOL );
        $this->assertEquals( $this->object->headCell( 'My header' ), '<th>My header</th>' . PHP_EOL );
        $this->assertEquals( $this->object->headCell( 'My header', '5' ), '<th colspan="5">My header</th>' . PHP_EOL );
        $this->assertEquals( $this->object->headCell( 'My header', '5', 'myId' ), '<th id="myId" colspan="5">My header</th>' . PHP_EOL );
*/
    }/*}}}*/
}
