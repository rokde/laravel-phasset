<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Filters;


interface Filterable {

	/**
	 * filters given string
	 *
	 * @param string $string
	 * @return string
	 */
	public function filter($string);

	/**
	 * is given file filterable
	 *
	 * @param string $file
	 * @return bool
	 */
	public function isFilterable($file);
}