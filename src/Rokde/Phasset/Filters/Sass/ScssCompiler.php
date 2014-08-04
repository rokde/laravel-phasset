<?php
/**
 * phasset
 *
 * @author rok
 * @since 04.08.14
 */

namespace Rokde\Phasset\Filters\Sass;


use Rokde\Phasset\Filters\Filterable;
use scssc;
use scss_formatter_compressed;

class ScssCompiler implements Filterable {

	/**
	 * less compiler
	 *
	 * @var scssc
	 */
	private $scssc;

	/**
	 * instantiates less compiler
	 */
	public function __construct()
	{
		$this->scssc = new scssc();
		$this->scssc->setFormatter('scss_formatter_compressed');
		$this->scssc->addImportPath(getcwd());
	}

	/**
	 * filters given string
	 *
	 * @param string $string
	 * @return string
	 */
	public function filter($string)
	{
		return $this->scssc->compile($string);
	}

	/**
	 * is given file filterable
	 *
	 * @param string $file
	 * @return bool
	 */
	public function isFilterable($file)
	{
		$this->scssc->addImportPath(dirname($file));

		return ends_with($file, '.scss');
	}
}