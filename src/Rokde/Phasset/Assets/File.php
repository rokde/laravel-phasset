<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Assets;


abstract class File {

	/**
	 * base path
	 *
	 * @var string
	 */
	protected $basePath = '';

	/**
	 * filename
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * initializes with a filename
	 *
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * returns filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->basePath . $this->filename;
	}

	/**
	 * sets base path
	 *
	 * @param string $basePath
	 */
	public function setBasePath($basePath)
	{
		$this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
}