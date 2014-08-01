<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 31.07.14
 */

namespace Rokde\Phasset\Repositories;


use Illuminate\Foundation\Application;
use Rokde\Phasset\Filters\Filterable;

class FilterRepository {

	/**
	 * filter registry
	 * - key is an identifier
	 * - value is a class definition or resolved class instance
	 *
	 * @var array|Filterable[]
	 */
	private $filters;

	/**
	 *
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	private $app;

	/**
	 * initializes with application ioc container
	 *
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * sets filter, overwrites all existing ones
	 *
	 * @param array|Filterable[] $filters
	 * @return FilterRepository
	 */
	public function setFilter(array $filters)
	{
		$this->filters = $filters;

		return $this;
	}

	/**
	 * @param string $key
	 * @return Filterable
	 * @throws \InvalidArgumentException
	 */
	public function getFilter($key)
	{
		if (! array_key_exists($key, $this->filters))
			throw new \InvalidArgumentException('Filter ' . $key . ' is unknown');

		$filter = $this->filters[$key];
		if (is_object($filter))
		{
			return $filter;
		}

		if (is_string($filter))
			$this->filters[$key] = $this->resolveFilter($filter);
		else
			$this->filters[$key] = $this->resolveFilter($filter['class'], $filter['options']);

		return $this->filters[$key];
	}

	private function resolveFilter($filter, array $params = [])
	{
		return $this->app->make($filter, $params);
	}
}