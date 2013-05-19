<?php

namespace Bytes\Docraptor\Document;

abstract class AbstractDocument implements DocumentInterface
{
	/**
	 * The document name
	 *
	 * @var string
	 */
	private $_name;


	/**
	 * Construct new document
	 */
	public function __construct($name)
	{
		$this->setName($name);
	}

	/**
	 * Set the document name
	 *
	 * @param string $name The document name
	 * @return $this
	 */
	private function setName($name)
	{
		if (!is_string($name)) {
			throw new \InvalidArgumentException('The document name must be a string, '.gettype($name).' given.');
		}

		if (strlen($name) == 0) {
			throw new \InvalidArgumentException('The document name must have a length of at least one.');
		}

		$this->_name = $name;

		return $this;
	}

	/**
	 * Get document name
	 *
	 * @return string The document name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Get document parameters
	 *
	 * @return array A two-dimensional array of parameters
	 */
	public function getParameters()
	{
		return array(
			'doc' => array(
				'name' => $this->getName(),
			),
		);
	}
}