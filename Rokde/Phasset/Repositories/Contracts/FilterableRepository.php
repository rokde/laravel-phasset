<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Repositories\Contracts;


interface FilterableRepository {
	/**
	 * adds a path with optional filters
	 *
	 * @param string $path
	 * @param array $filters
	 * @return $this
	 */
	public function setFilter($path, array $filters = []);
}