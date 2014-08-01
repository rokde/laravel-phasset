<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Assets;


interface Readable {

	/**
	 * reads a file
	 *
	 * @return string
	 */
	public function read();
}