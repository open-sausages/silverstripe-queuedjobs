<?php

use AsyncPHP\Doorman\Manager\ProcessManager;

class DoormanProcessManager extends ProcessManager
{
	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	protected function getWorker() {
		return BASE_PATH . "/framework/cli-script.php dev/tasks/ProcessJobQueueChildTask";
	}
}
