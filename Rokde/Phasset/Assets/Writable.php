<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Assets;


interface Writable {

	/**
	 * writes the file
	 *
	 * @return void
	 */
	public function write();
}