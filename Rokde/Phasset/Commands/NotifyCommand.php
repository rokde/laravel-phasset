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
use Rokde\Phasset\Watching\Watcher;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'phasset:notify';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Notifies changes at a folder';

	/**
	 * fires the command
	 */
	public function fire()
	{
		$watchFolder = $this->argument('path');
		$interval = $this->getIntervalInMicroseconds();


		/** @var Watcher $watcher */
		$watcher = $this->laravel->make(Watcher::class);

		$self = $this;
		$this->registerShutdownHandler(function () use ($watcher, $self) {
			echo PHP_EOL;
			foreach ($watcher->getStatistics() as $event => $count)
			{
				$self->comment($event . ': ' . $count);
			}
			exit(0);
		});

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
			$this->info($prefix . $file, OutputInterface::VERBOSITY_NORMAL);
		});


		//	initialize hashes for each file in watch folder
		$watcher->addWatchFolder($watchFolder);

		$this->info('watching ' . $watcher->count() . ' files in ' . realpath($watchFolder), OutputInterface::VERBOSITY_VERBOSE);
		$this->comment('hit CTRL+C to stop watching', OutputInterface::VERBOSITY_NORMAL);

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
			['path', InputArgument::REQUIRED, 'The path to watch at']
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

	/**
	 * registers a shutdown handler on *nix based systems
	 *
	 * @param callable|\Closure $closure
	 */
	private function registerShutdownHandler($closure)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return;

		if (! is_callable($closure))
			return;

		if ($this->getOutput()->getVerbosity() === OutputInterface::VERBOSITY_QUIET)
			return;

		declare(ticks = 1);
		pcntl_signal(SIGINT, $closure);
	}
}