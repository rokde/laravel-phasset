<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Repositories;


class DirectoryNotExistsException extends \InvalidArgumentException {

	/**
	 * creates a directory not exists exception
	 *
	 * @param string $path
	 * @param \Exception|null $previous
	 */
	public function __construct($path, \Exception $previous = null) {
		parent::__construct('Directory ' . $path . ' does not exists', 0, $previous);
	}
}