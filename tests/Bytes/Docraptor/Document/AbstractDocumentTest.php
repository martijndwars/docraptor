<?php

namespace Bytes\Docraptor\Tests\Docraptor\Document;

use Mockery;
use Bytes\Docraptor\Document\PdfDocument;

class PdfDocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the object gets initialized correctly
     */
    public function testInitialization()
    {
        $document = new PdfDocument('Document name');

        $this->assertInstanceOf('Bytes\Docraptor\Document\PdfDocument', $document);
        $this->assertInstanceOf('Bytes\Docraptor\Document\AbstractDocument', $document);
        $this->assertInstanceOf('Bytes\Docraptor\Document\DocumentInterface', $document);
    }

    /**
     * Test exception on invalid name (e.g. empty or non-string)
     */
    public function testValidName()
    {
    	$document = new PdfDocument('Sample');

    	$this->assertEquals('Sample', $document->getName());
    }

    /**
     * Test exception on invalid name (e.g. empty)
     *
     * @expectedException InvalidArgumentException
     */
    public function testInvalidEmptyName()
    {
    	$document = new PdfDocument('');
    }

    /**
     * Test exception on invalid name (e.g. non-string)
     *
     * @expectedException InvalidArgumentException
     */
    public function testInvalidNonstringName()
    {
    	$document = new PdfDocument(123);
    }

    /**
     * Test setting content
     */
    public function testSetContent()
    {
    	$document = new PdfDocument('Sample');

    	$document->setContent('Sample content');

    	$this->assertEquals('Sample content', $document->getContent());
    }

    /**
     * Test getting parameters
     */
    public function testParameters()
    {
    	$document = new PdfDocument('Sample');
    	$document->setContent('abc');

    	$this->assertEquals(array(
    		'doc' => array(
    			'document_type' => 'pdf',
    			'document_content' => 'abc',
    			'name' => 'Sample'
    		)
    	), $document->getParameters());
    }
}