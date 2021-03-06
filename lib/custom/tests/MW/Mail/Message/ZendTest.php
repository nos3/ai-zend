<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2014
 */


class MW_Mail_Message_ZendTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_mock;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( !class_exists( 'Zend_Mail' ) ) {
			$this->markTestSkipped( 'Zend_Mail is not available' );
		}

		$this->_mock = $this->getMockBuilder( 'Zend_Mail' )->disableOriginalConstructor()->getMock();
		$this->_object = new MW_Mail_Message_Zend( $this->_mock );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}


	public function testAddFrom()
	{
		$this->_mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addFrom( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddTo()
	{
		$this->_mock->expects( $this->once() )->method( 'addTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addTo( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddCc()
	{
		$this->_mock->expects( $this->once() )->method( 'addCc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addCc( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddBcc()
	{
		$this->_mock->expects( $this->once() )->method( 'addBcc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addBcc( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddReplyTo()
	{
		$this->_mock->expects( $this->once() )->method( 'setReplyTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addReplyTo( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddHeader()
	{
		$this->_mock->expects( $this->once() )->method( 'addHeader' )
			->with( $this->stringContains( 'test' ), $this->stringContains( 'value' ) );

		$result = $this->_object->addHeader( 'test', 'value' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetSender()
	{
		$this->_mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->setSender( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetSubject()
	{
		$this->_mock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->_object->setSubject( 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetBody()
	{
		$this->_mock->expects( $this->once() )->method( 'setBodyText' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->_object->setBody( 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetBodyHtml()
	{
		$result = $this->_object->setBodyHtml( 'test' );
		$this->_object->getObject();

		$this->assertSame( $this->_object, $result );
	}


	public function testAddAttachment()
	{
		$partMock = $this->getMockBuilder( 'Zend_Mime_Part' )->disableOriginalConstructor()->getMock();

		$this->_mock->expects( $this->once() )->method( 'createAttachment' )
			->with( $this->stringContains( 'test' ), $this->stringContains( 'text/plain' ),
				$this->stringContains( 'inline' ), $this->stringContains( Zend_Mime::ENCODING_BASE64 ),
				$this->stringContains( 'test.txt' ) )
			->will( $this->returnValue( $partMock ) );

		$result = $this->_object->addAttachment( 'test', 'text/plain', 'test.txt', 'inline' );
		$this->assertSame( $this->_object, $result );
	}


	public function testEmbedAttachment()
	{
		$this->_mock->expects( $this->once() )->method( 'getBodyHtml' )
			->will( $this->returnValue( new stdClass() ) );

		$result = $this->_object->embedAttachment( 'test', 'text/plain', 'test.txt' );
		$this->_object->getObject();

		$this->assertInternalType( 'string', $result );
	}


	public function testEmbedAttachmentMultiple()
	{
		$object = new MW_Mail_Message_Zend( new Zend_Mail() );

		$object->setBody( 'text body' );
		$object->embedAttachment( 'test', 'text/plain', 'test.txt' );
		$object->embedAttachment( 'test', 'text/plain', 'test.txt' );

		$transport = new Test_Zend_Mail_Transport_Memory();
		$object->getObject()->send( $transport );

		$exp = '#Content-Disposition: inline; filename="test.txt".*Content-Disposition: inline; filename="1_test.txt"#smu';
		$this->assertRegExp( $exp, $transport->message );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( 'Zend_Mail', $this->_object->getObject() );
	}


	public function testGenerateMailAlternative()
	{
		$object = new MW_Mail_Message_Zend( new Zend_Mail() );

		$object->setBody( 'text body' );
		$object->setBodyHtml( 'html body' );

		$transport = new Test_Zend_Mail_Transport_Memory();
		$object->getObject()->send( $transport );

		$exp = '#Content-Type: multipart/alternative;.*Content-Type: text/plain;.*Content-Type: text/html;#smu';
		$this->assertRegExp( $exp, $transport->message );
	}


	public function testGenerateMailRelated()
	{
		$object = new MW_Mail_Message_Zend( new Zend_Mail() );

		$object->embedAttachment( 'embedded-data', 'text/plain', 'embedded.txt' );
		$object->setBodyHtml( 'html body' );

		$transport = new Test_Zend_Mail_Transport_Memory();
		$object->getObject()->send( $transport );

		$exp = '#Content-Type: multipart/related.*Content-Type: text/html;.*Content-Type: text/plain#smu';
		$this->assertRegExp( $exp, $transport->message );
	}


	public function testGenerateMailFull()
	{
		$object = new MW_Mail_Message_Zend( new Zend_Mail() );

		$object->addAttachment( 'attached-data', 'text/plain', 'attached.txt' );
		$object->embedAttachment( 'embedded-data', 'text/plain', 'embedded.txt' );
		$object->setBodyHtml( 'html body' );
		$object->setBody( 'text body' );

		$transport = new Test_Zend_Mail_Transport_Memory();
		$object->getObject()->send( $transport );

		$exp = '#Content-Type: multipart/mixed;.*Content-Type: multipart/alternative;.*Content-Type: text/plain;.*Content-Type: multipart/related.*Content-Type: text/html;.*Content-Type: text/plain.*Content-Type: text/plain#smu';
		$this->assertRegExp( $exp, $transport->message );
	}
}



if( !class_exists( 'Zend_Mail_Transport_Abstract' ) ) {
	return;
}

class Test_Zend_Mail_Transport_Memory extends Zend_Mail_Transport_Abstract
{
	public $message;

	protected function _sendMail()
	{
		$this->message = $this->header . "\r\n" . $this->body;
	}
}
