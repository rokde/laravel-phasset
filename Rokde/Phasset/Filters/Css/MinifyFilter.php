<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Filters\Css;


use CSSmin;
use Rokde\Phasset\Filters\Filterable;

class MinifyFilter implements Filterable {


	/**
	 *
	 *
	 * @var \CSSmin
	 */
	private $cssMinifier;

	public function __construct(CSSmin $cssMinifier)
	{
		$this->cssMinifier = $cssMinifier;
	}

	/**
	 * filters given string
	 *
	 * @param string $string
	 * @return string
	 */
	public function filter($string)
	{
		return $this->cssMinifier->run($string);
	}

	/**
	 * is given file filteable
	 *
	 * @param string $file
	 * @return bool
	 */
	public function isFilterable($file)
	{
		return ! ends_with($file, '.min.css') && ends_with($file, '.css');
	}
}