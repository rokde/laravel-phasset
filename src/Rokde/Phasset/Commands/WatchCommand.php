<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 29.07.14
 */

namespace Rokde\Phasset\Commands;


use App;
use Event;
use Illuminate\Support\Collection;
use Rokde\Phasset\Repositories\FilesWatchingRepository;
use Rokde\Phasset\Repositories\FilterRepository;
use Rokde\Phasset\Repositories\SourceFilesRepository;
use Rokde\Phasset\Repositories\TargetFilesRepository;
use Rokde\Phasset\Watching\Watcher;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'phasset:watch';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Watches a folder';

	/**
	 * fires the command
	 */
	public function fire()
	{
		$config = $this->laravel['config']->get('phasset::watcher');
		$interval = $this->getIntervalInMicroseconds();
		
		/** @var \Illuminate\Events\Dispatcher $events */
		$events = App::make('events');
		
		// setting up event listener
		$targetFileWrittenNotifier = new TargetFileWrittenNotifier($this, $events, OutputInterface::VERBOSITY_VERBOSE);
		

		/** @var FilterRepository $filterRepository */
		$filterRepository = $this->laravel->make('Rokde\Phasset\Repositories\FilterRepository');
		$filterRepository->setFilter($config['filters']);

		$sourceFilesRepository = new SourceFilesRepository($filterRepository, getcwd());
		$sourceFilesRepository->setSources($config['sources']);


		$this->laravel->bind(
			'Rokde\Phasset\Repositories\FilesWatchingRepository',
			'Rokde\Phasset\Repositories\FilteredFilesWatchingRepository'
		);

		/** @var Watcher $watcher */
		$watcher = $this->laravel->make('Rokde\Phasset\Watching\Watcher');

		foreach ($config['watchFolder'] as $path => $filter)
		{
			$this->info($path . ': ' . implode(', ', $filter));
			//	adds filter and ...
			$watcher->addFilter($path, $filter)
				//	initialize hashes for each file in watch folder
				->addWatchFolder($path);
		}

		$watcher->on([
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_CREATED,
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_MODIFIED,
			FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_REMOVED,
		], function ($file) {
			$event = Event::firing();

			$prefix = '';
			switch ($event)
			{
				case FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_CREATED:
					$prefix = '+ ';
					break;
				case FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_MODIFIED:
					$prefix = '* ';
					break;
				case FilesWatchingRepository::EVENT_PREFIX . '.' . FilesWatchingRepository::EVENT_REMOVED:
					$prefix = '- ';
					break;
			}
			$this->info($prefix . $file);
		});

		$targetFilesRepository = new TargetFilesRepository($sourceFilesRepository, getcwd(), $events);
		$targetFilesRepository->setTargets($config['targets']);

		$watcher->on(FilesWatchingRepository::EVENT_PREFIX_TYPES.'.*', function ($file, $event) use ($targetFilesRepository) {

			$targetFilesRepository->updateTargetsWithSourceFile($file);

		});

		$this->info('watching ' . $watcher->count() . ' files', OutputInterface::VERBOSITY_VERBOSE);
		$this->comment('hit CTRL+C to stop watching');

		//	loop infinite and recheck hashes
		while (true)
		{
			$watcher->watch();
			usleep($interval);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['interval', null, InputOption::VALUE_OPTIONAL, 'Loop interval in seconds', 1],
		];
	}

	/**
	 * returns update interval in microseconds
	 *
	 * @return int
	 */
	private function getIntervalInMicroSeconds()
	{
		$interval = floatval($this->option('interval'));
		return round($interval * 1000 * 1000);
	}
}
