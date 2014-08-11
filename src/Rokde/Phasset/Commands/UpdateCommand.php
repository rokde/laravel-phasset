<?php
/**
 * mitarbeiterbereich2
 *
 * @author rok
 * @since 01.08.14
 */

namespace Rokde\Phasset\Commands;


use Rokde\Phasset\Repositories\FilterRepository;
use Rokde\Phasset\Repositories\SourceFilesRepository;
use Rokde\Phasset\Repositories\TargetFilesRepository;

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
		$targetFilesRepository = new TargetFilesRepository($sourceFilesRepository, getcwd(), $events);
		$targetFilesRepository->setTargets($config['targets']);

		$events->listen('phasset.updating', function ($targetFileCount) {
			$this->info('updating ' . $targetFileCount . ' asset files...');
		});
		$events->listen('phasset.updated', function ($targetFileCount) {
			$this->info($targetFileCount . ' asset files updated');
		});

		$self = $this;
		/** @var \Symfony\Component\Console\Helper\ProgressHelper $progress */
		$progress = $this->getHelper('progress');

		$events->listen('phasset.reading', function ($sourceFileCount) use ($self, $progress) {
			$progress->start($self->output, $sourceFileCount);
		});
		$events->listen('phasset.read-step', function ($step, $sourceFileCount) use ($progress) {
			$progress->advance();
		});
		$events->listen('phasset.read', function ($sourceFileCount) use ($self, $progress) {
			$progress->finish();
		});

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