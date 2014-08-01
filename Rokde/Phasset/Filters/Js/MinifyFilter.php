<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Filters\Js;


use JSMin;
use Rokde\Phasset\Filters\Filterable;

class MinifyFilter implements Filterable {

	/**
	 * filters given string
	 *
	 * @param string $string
	 * @return string
	 */
	public function filter($string)
	{
		return JSMin::minify($string);
	}

	/**
	 * is given file filterable
	 *
	 * @param string $file
	 * @return bool
	 */
	public function isFilterable($file)
	{
		return ! ends_with($file, '.min.js') && ends_with($file, '.js');
	}
}