<?php
/**
 * phasset
 *
 * @author rok
 * @since 15.08.14
 */

namespace Rokde\Phasset\Observers;


use Rokde\Phasset\Commands\BaseCommand;

class SourceFileProcessingNotifier extends BaseObserver {

	public function observe()
	{
		/** @var BaseCommand $self */
		$self = $this->command;
		/** @var \Symfony\Component\Console\Helper\ProgressHelper $progress */
		$progress = $this->command->getHelper('progress');

		$this->events->listen('phasset.source.processing', function ($sourceFileCount) use ($self, $progress) {
			$progress->start($self->getOutput(), $sourceFileCount);
		});
		$this->events->listen('phasset.source.processing.file', function ($file, $step, $sourceFileCount) use ($progress) {
			$progress->advance();
		});
		$this->events->listen('phasset.source.processed', function ($sourceFileCount) use ($self, $progress) {
			$progress->finish();
		});
	}
}