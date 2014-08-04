<?php
/**
 * phasset
 *
 * @author rok
 * @since 04.08.14
 */

namespace Rokde\Phasset\Filters\Less;


use Rokde\Phasset\Filters\Filterable;
use lessc;
use lessc_formatter_compressed;

class LessCompiler implements Filterable
{
	/**
	 * less compiler
	 *
	 * @var lessc
	 */
	private $lessc;

	/**
	 * instantiates less compiler
	 */
	public function __construct()
	{
		$this->lessc = new lessc();
		$this->lessc->setFormatter('compressed');
		$this->lessc->addImportDir(getcwd());
	}

	/**
	 * filters given string
	 *
	 * @param string $string
	 * @return string
	 */
	public function filter($string)
	{
		return $this->lessc->compile($string);
	}

	/**
	 * is given file filterable
	 *
	 * @param string $file
	 * @return bool
	 */
	public function isFilterable($file)
	{
		$this->lessc->addImportDir(dirname($file));

		return ends_with($file, '.less');
	}
}