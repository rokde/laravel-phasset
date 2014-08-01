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

		$targetFilesRepository = new TargetFilesRepository($sourceFilesRepository, getcwd());
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