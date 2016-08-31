<?php
namespace gezere\tests\units;

require_once( dirname( __FILE__ ) . '/../atoum.phar' );

include dirname( __FILE__ ) . '/../../library/Form.php';
include dirname( __FILE__ ) . '/../../library/XmlElement.php';

use \mageekguy\atoum;
use \gezere;
class Form extends atoum\test {
    public function testFormOpen() {/*{{{*/
        $form = new Form();
        $this->assert
            ->string( $form->formOpen() )
            ->isEqualTo( '<form name="frmGezereForm" action="#" method="post">' . PHP_EOL );

/*
        $this->assertEquals( $this->object->formOpen( 'myAction.php' ), '<form name="frmGezereForm" action="myAction.php" method="post">' . PHP_EOL );
        $this->assertEquals( $this->object->formOpen( 'myAction.php', Gezere_Form::METHOD_GET ), '<form name="frmGezereForm" action="myAction.php" method="' . Gezere_Form::METHOD_GET . '">' . PHP_EOL );
        $this->assertEquals( $this->object->formOpen( 'myAction.php', Gezere_Form::METHOD_GET, 'frmName' ), '<form name="frmName" action="myAction.php" method="' . Gezere_Form::METHOD_GET . '">' . PHP_EOL );
        $this->assertEquals( $this->object->formOpen( 'myAction.php', Gezere_Form::METHOD_GET, 'frmName', Gezere_Form::ENCTYPE_URLENCODED ), '<form name="frmName" action="myAction.php" method="' . Gezere_Form::METHOD_GET . '" enctype="' . Gezere_Form::ENCTYPE_URLENCODED . '">' . PHP_EOL );
        $this->assertEquals( $this->object->formOpen( 'myAction.php', Gezere_Form::METHOD_GET, 'frmName', Gezere_Form::ENCTYPE_URLENCODED, 'myId' ), '<form name="frmName" action="myAction.php" method="' . Gezere_Form::METHOD_GET . '" enctype="' . Gezere_Form::ENCTYPE_URLENCODED . '" id="myId">' . PHP_EOL );
        $this->assertEquals( $this->object->formOpen( 'myAction.php', Gezere_Form::METHOD_GET, 'frmName', Gezere_Form::ENCTYPE_URLENCODED, 'myId', 'myClass' ), '<form name="frmName" action="myAction.php" method="' . Gezere_Form::METHOD_GET . '" enctype="' . Gezere_Form::ENCTYPE_URLENCODED . '" id="myId" class="myClass">' . PHP_EOL );
*/
    }/*}}}*/
    public function testLabel() {/*{{{*/
/*
        $this->assertEquals( $this->object->label( 'myFor', 'myValue' ), '<label for="myFor">myValue</label>' . PHP_EOL );
*/
    }/*}}}*/
    public function testInput() {/*{{{*/
/*
        $this->assertEquals( $this->object->input( 'myName' ), '<input type="text" name="myName"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue' ), '<input type="text" name="myName" value="myValue"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', 'myType' ), '<input type="myType" name="myName" value="myValue"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', 'text', 40 ), '<input type="text" name="myName" value="myValue" size="40"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', 'text', 40, 200 ), '<input type="text" name="myName" value="myValue" size="40" maxlength="200"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', 'text', 40, 200, 'myClass' ), '<input type="text" name="myName" value="myValue" size="40" maxlength="200" class="myClass"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', 'text', 40, 200, 'myClass', 'myId' ), '<input type="text" name="myName" value="myValue" size="40" maxlength="200" class="myClass" id="myId"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', 'text', 40, 200, 'myClass', 'myId', true ), '<input type="text" name="myName" value="myValue" size="40" maxlength="200" class="myClass" id="myId"/>' . PHP_EOL );
        $this->assertEquals( $this->object->input( 'myName', 'myValue', Gezere_Form::INPUT_CHECKBOX, 40, 200, 'myClass', 'myId', true ), '<input type="' . Gezere_Form::INPUT_CHECKBOX . '" name="myName" value="myValue" class="myClass" id="myId" checked="checked"/>' . PHP_EOL );
*/
    }/*}}}*/
    public function testTextarea() {/*{{{*/
/*
        $this->assertEquals( $this->object->textarea( 'myTextarea' ), '<textarea name="myTextarea"/>' . PHP_EOL );
        $this->assertEquals( $this->object->textarea( 'myTextarea', 'myValue' ), '<textarea name="myTextarea">myValue</textarea>' . PHP_EOL );
        $this->assertEquals( $this->object->textarea( 'myTextarea', 'myValue', 30 ), '<textarea name="myTextarea" cols="30">myValue</textarea>' . PHP_EOL );
        $this->assertEquals( $this->object->textarea( 'myTextarea', 'myValue', 30, 15 ), '<textarea name="myTextarea" cols="30" rows="15">myValue</textarea>' . PHP_EOL );
        $this->assertEquals( $this->object->textarea( 'myTextarea', 'myValue', 30, 15, 'myId' ), '<textarea name="myTextarea" cols="30" rows="15" id="myId">myValue</textarea>' . PHP_EOL );
        $this->assertEquals( $this->object->textarea( 'myTextarea', 'myValue', 30, 15, 'myId', 'myClass' ), '<textarea name="myTextarea" cols="30" rows="15" id="myId" class="myClass">myValue</textarea>' . PHP_EOL );
*/
    }/*}}}*/
    public function testSelect() {/*{{{*/
/*
        $this->assertEquals( $this->object->select( 'mySelect' ), '<select name="mySelect"/>' . PHP_EOL );
        $this->assertEquals( $this->object->select( 'mySelect', array( '32' => 'TrenteDeux', '34' => 'TrenteQuatre' ) ), '<select name="mySelect"><option value="32">TrenteDeux</option><option value="34">TrenteQuatre</option></select>' . PHP_EOL );
        $this->assertEquals( $this->object->select( 'mySelect', array( 'pays' => array( 'CA' => 'Canada', 'US' => 'United States' ), 'provinces' => array( 'QC' => 'Quebec', 'ON' => 'Ontario' ), 'other' => 'Other' )), '<select name="mySelect"><optgroup label="pays"><option value="CA">Canada</option><option value="US">United States</option></optgroup><optgroup label="provinces"><option value="QC">Quebec</option><option value="ON">Ontario</option></optgroup><option value="other">Other</option></select>' . PHP_EOL );
        $this->assertEquals( $this->object->select( 'mySelect', array( 'pays' => array( 'CA' => 'Canada', 'US' => 'United States' ), 'provinces' => array( 'QC' => 'Quebec', 'ON' => 'Ontario' ), 'other' => 'Other' ), 'US'), '<select name="mySelect"><optgroup label="pays"><option value="CA">Canada</option><option value="US" selected="selected">United States</option></optgroup><optgroup label="provinces"><option value="QC">Quebec</option><option value="ON">Ontario</option></optgroup><option value="other">Other</option></select>' . PHP_EOL );
        $this->assertEquals( $this->object->select( 'mySelect', array( 'pays' => array( 'CA' => 'Canada', 'US' => 'United States' ), 'provinces' => array( 'QC' => 'Quebec', 'ON' => 'Ontario' ), 'other' => 'Other' ), '', true ), '<select name="mySelect" multiple="multiple"><optgroup label="pays"><option value="CA">Canada</option><option value="US">United States</option></optgroup><optgroup label="provinces"><option value="QC">Quebec</option><option value="ON">Ontario</option></optgroup><option value="other">Other</option></select>' . PHP_EOL );
        $this->assertEquals( $this->object->select( 'mySelect', array( 'pays' => array( 'CA' => 'Canada', 'US' => 'United States' ), 'provinces' => array( 'QC' => 'Quebec', 'ON' => 'Ontario' ), 'other' => 'Other' ), '', true, 5 ), '<select name="mySelect" multiple="multiple" size="5"><optgroup label="pays"><option value="CA">Canada</option><option value="US">United States</option></optgroup><optgroup label="provinces"><option value="QC">Quebec</option><option value="ON">Ontario</option></optgroup><option value="other">Other</option></select>' . PHP_EOL );
*/
    }/*}}}*/
    public function testFieldsetOpen() {/*{{{*/
/*
        $this->assertEquals( $this->object->fieldsetOpen(), '<fieldset>' . PHP_EOL );
*/
    }/*}}}*/
    public function testFieldsetClose() {/*{{{*/
/*
        $this->assertEquals( $this->object->fieldsetClose(), '</fieldset>' . PHP_EOL );
*/
    }/*}}}*/
    public function testLegend() {/*{{{*/
/*
        $this->assertEquals( $this->object->legend( 'myLegend' ), '<legend>myLegend</legend>' . PHP_EOL );
        $this->assertEquals( $this->object->legend( 'myLegend', 'center' ), '<legend align="center">myLegend</legend>' . PHP_EOL );
*/
    }/*}}}*/
    public function testFormClose() {/*{{{*/
/*
        $this->assertEquals( $this->object->formClose(), '</form>' . PHP_EOL );
*/
    }/*}}}*/
}
