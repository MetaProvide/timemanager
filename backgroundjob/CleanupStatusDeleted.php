<?php

declare(strict_types=1);

/**
 * Adminly Clients
 *
 * @copyright Copyright (C) 2022 Igor Oliveira <igoroliveira@metaprovide.org>
 *
 * @author Igor Oliveira <igoroliveira@metaprovide.org>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\TimeManager\BackgroundJob;

use OCA\DAV\CalDAV\Proxy\ProxyMapper;
use OCA\TimeManager\Db\ClientMapper;
use OCA\TimeManager\Db\ProjectMapper;
use OCA\TimeManager\Db\TaskMapper;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use OCP\IConfig;

class CleanupStatusDeleted extends TimedJob {

    /** @var ClientMapper */
	private $clientMapper;

    /** @var ProjectMapper */
	private $projectMapper;

    /** @var TaskMapper */
	private $taskMapper;

	/** @var IConfig */
	private $settings;

	/**
	 * @param ITimeFactory $time
	 * @param ClientMapper $clientMapper
	 * @param ProjectMapper $projectMapper
	 * @param TaskMapper $taskMapper
	 */
	public function __construct(ITimeFactory $time,
								ClientMapper $clientMapper,
								ProxyMapper $projectMapper,
								TaskMapper $taskMapper,
								IConfig $settings) {
		parent::__construct($time);
		$this->clientMapper = $clientMapper;
		$this->projectMapper = $projectMapper;
		$this->taskMapper = $taskMapper;
		$this->settings = $settings;

		// Run four times a day
		// $this->setInterval(6 * 60 * 60);
		$this->setInterval(10);
		$this->setTimeSensitivity(\OCP\BackgroundJob\IJob::TIME_INSENSITIVE);
	}

	protected function run($argument): void {
        $this->clientMapper->cleanUp();
        $this->projectMapper->cleanUp();
        $this->taskMapper->cleanUp();
	}
}