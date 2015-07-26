<?php

use AsyncPHP\Doorman\Expires;
use AsyncPHP\Doorman\Process;
use AsyncPHP\Doorman\Task;

class DoormanQueuedJobTask implements Task, Expires, Process {
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var QueuedJobDescriptor
	 */
	protected $descriptor;

	/**
	 * @inheritdoc
	 *
	 * @return null|int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @inheritdoc
	 *
	 * @param int $id
	 *
	 * @return $this
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return QueuedJobDescriptor
	 */
	public function getDescriptor()
	{
		return $this->descriptor;
	}

	/**
	 * @param QueuedJobDescriptor $descriptor
	 */
	public function __construct(QueuedJobDescriptor $descriptor) {
		$this->descriptor = $descriptor;
	}

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize(array(
			'descriptor' => $this->descriptor->ID,
		));
	}

	/**
	 * @inheritdoc
	 */
	public function unserialize($serialized) {
		$data = unserialize($serialized);

		if(!isset($data['descriptor'])) {
			throw new InvalidArgumentException('Malformed data');
		}

		$descriptor = QueuedJobDescriptor::get()
			->filter('ID', $data['descriptor'])
			->first();

		if(!$descriptor) {
			throw new InvalidArgumentException('Descriptor not found');
		}

		$this->descriptor = $descriptor;
	}

	/**
	 * @return string
	 */
	public function getHandler() {
		return 'DoormanQueuedJobHandler';
	}

	/**
	 * @return array
	 */
	public function getData() {
		return array(
			'descriptor' => $this->descriptor,
		);
	}

	/**
	 * @return bool
	 */
	public function ignoresRules() {
		if (method_exists($this->descriptor, 'ignoreRules')) {
			return $this->descriptor->ignoreRules();
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function stopsSiblings() {
		if (method_exists($this->descriptor, 'stopsSiblings')) {
			return $this->descriptor->stopsSiblings();
		}

		return false;
	}

	/**
	 * @inheritdoc
	 *
	 * @return int
	 */
	public function getExpiresIn() {
		if (method_exists($this->descriptor, 'getExpiresIn')) {
			return $this->descriptor->getExpiresIn();
		}

		return -1;
	}

	/**
	 * @inheritdoc
	 *
	 * @param int $startedAt
	 *
	 * @return bool
	 */
	public function shouldExpire($startedAt) {
		if (method_exists($this->descriptor, 'shouldExpire')) {
			return $this->descriptor->shouldExpire($startedAt);
		}

		return true;
	}
}
