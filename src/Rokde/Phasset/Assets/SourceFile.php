<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Assets;


use Rokde\Phasset\Filters\Filterable;

class SourceFile extends File implements Readable {

	/**
	 * filters for source file
	 *
	 * @var array|Filterable[]
	 */
	private $filters = [];

	/**
	 * adds a filter to the source file
	 *
	 * @param Filterable $filter
	 * @return SourceFile
	 */
	public function addFilter(Filterable $filter)
	{
		$this->filters[] = $filter;

		return $this;
	}

	/**
	 * reads the source file in
	 *
	 * @return string
	 */
	public function read()
	{
		if (! file_exists($this->getFilename()))
			return '';

		$string = file_get_contents($this->getFilename());

		/** @var Filterable $filter */
		foreach ($this->filters as $filter)
		{
			if (! $filter->isFilterable($this->getFilename()))
				continue;

			$string = $filter->filter($string);
		}

		return $string;
	}
}