<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 30.07.14
 */

namespace Rokde\Phasset\Watching;


use Illuminate\Events\Dispatcher;
use Rokde\Phasset\Repositories\Contracts\FilterableRepository;
use Rokde\Phasset\Repositories\FilesWatchingRepository;

class Watcher {

	/**
	 * internal repository to work on
	 *
	 * @var FilesWatchingRepository
	 */
	private $filesRepository;

	/**
	 * counting events watched
	 *
	 * @var array
	 */
	private $counter = [];

	/**
	 * events dispatcher
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	private $events;

	/**
	 * @param FilesWatchingRepository $filesRepository
	 * @param Dispatcher $events
	 */
	public function __construct(FilesWatchingRepository $filesRepository, Dispatcher $events)
	{
		$this->filesRepository = $filesRepository;

		$this->events = $events;

		$this->counter = array(
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_WATCHED => 0,
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_CREATED => 0,
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_MODIFIED => 0,
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_REMOVED => 0,
		);

		$self = $this;
		$this->on(FilesWatchingRepository::EVENT_PREFIX . '.*', function () use ($self, $events) {
			$self->updateCounter($events->firing());
		});
		$this->on(FilesWatchingRepository::EVENT_PREFIX_TYPES . '.*', function () use ($self, $events) {
			$self->updateCounter($events->firing());
		});
	}

	/**
	 * adds a watch folder
	 *
	 * @param string $path
	 * @return $this
	 */
	public function addWatchFolder($path)
	{
		$this->filesRepository->addPath($path);

		return $this;
	}

	/**
	 * adds a filter on a path
	 *
	 * @param string $path
	 * @param string|array $filter
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function addFilter($path, $filter)
	{
		if (! $this->filesRepository instanceof FilterableRepository)
			throw new \InvalidArgumentException('No filters allowed on given file repository');

		if (! is_array($filter))
			$filter = [$filter];

		$this->filesRepository->setFilter($path, $filter);

		return $this;
	}

	/**
	 * number of files being watched now
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->filesRepository->count();
	}

	/**
	 * watches / updates the internal hashes and fires events on modifications
	 *
	 * @return $this
	 */
	public function watch()
	{
		$this->filesRepository->watch();

		return $this;
	}

	/**
	 * adds a listener to the events
	 *
	 * @param string|array $events
	 * @param callable|\Closure $closure
	 * @return $this
	 */
	public function on($events, $closure)
	{
		$this->events->listen($events, $closure);

		return $this;
	}

	/**
	 * updates internal events fired counter
	 *
	 * @param string $event
	 */
	protected function updateCounter($event)
	{
		if (!array_key_exists($event, $this->counter))
			$this->counter[$event] = 0;

		$this->counter[$event]++;
	}

	/**
	 * returns counter per event watched
	 *
	 * @return array
	 */
	public function getStatistics()
	{
		return $this->counter;
	}
}