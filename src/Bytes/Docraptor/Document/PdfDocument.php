<?php

namespace Bytes\Docraptor\Document;

class PdfDocument extends AbstractDocument
{
	/**
	 * Set content
	 *
	 * @param string $content The (HTML) content to be converted
	 * @return PdfDocument
	 */
	public function setContent($content)
	{
		$this->_content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return string The (HTML) content to be converted
	 */
	public function getContent()
	{
		return $this->_content;
	}

	/**
	 * Construct an array of parameter-value pairs for this document
	 *
	 * @return array Multi-dimensional array of parameters
	 */
	public function getParameters()
	{
		return array_replace_recursive(array(
			'doc' => array(
				'document_type' => 'pdf',
				'document_content' => $this->getContent()
			)
		), parent::getParameters());
	}
}