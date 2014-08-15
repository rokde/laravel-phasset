<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 01.08.14
 */

namespace Rokde\Phasset\Commands;


use Rokde\Phasset\Observers\BasicStatusNotifier;
use Rokde\Phasset\Observers\SourceFileProcessingNotifier;
use Rokde\Phasset\Observers\TargetFileWrittenNotifier;
use Rokde\Phasset\Repositories\FilterRepository;
use Rokde\Phasset\Repositories\SourceFilesRepository;
use Rokde\Phasset\Repositories\TargetFilesRepository;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'phasset:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates configured target files';

	/**
	 * fires the command
	 */
	public function fire()
	{
		$config = $this->laravel['config']->get('phasset::watcher');

		/** @var FilterRepository $filterRepository */
		$filterRepository = $this->laravel->make('Rokde\Phasset\Repositories\FilterRepository');
		$filterRepository->setFilter($config['filters']);

		$sourceFilesRepository = new SourceFilesRepository($filterRepository, getcwd());
		$sourceFilesRepository->setSources($config['sources']);

		/** @var \Illuminate\Events\Dispatcher $events */
		$events = \App::make('events');

		$basicNotifier = new BasicStatusNotifier($this, $events, [OutputInterface::VERBOSITY_NORMAL, OutputInterface::VERBOSITY_VERBOSE, OutputInterface::VERBOSITY_VERY_VERBOSE]);
		$targetFileWrittenNotifier = new TargetFileWrittenNotifier($this, $events, OutputInterface::VERBOSITY_VERBOSE);
		$sourceFileProcessingNotifier = new SourceFileProcessingNotifier($this, $events, OutputInterface::VERBOSITY_VERY_VERBOSE);

		$targetFilesRepository = new TargetFilesRepository($sourceFilesRepository, getcwd(), $events);
		$targetFilesRepository->setTargets($config['targets']);

		$targetFilesRepository->update();
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
		];
	}
}