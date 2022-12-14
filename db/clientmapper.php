<?php

namespace OCA\TimeManager\Db;

use OCP\IDBConnection;

/**
 * Class ItemMapper
 *
 * @package OCA\TimeManager\Db
 * @method Client insert(Client $entity)
 */
class ClientMapper extends ObjectMapper {
	protected $projectMapper;

	public function __construct(IDBConnection $db, CommitMapper $commitMapper, ProjectMapper $projectMapper) {
		parent::__construct($db, $commitMapper, "timemanager_client");
		$this->projectMapper = $projectMapper;
	}

	public function deleteChildrenForEntityById($uuid, $commit) {
		$this->projectMapper->deleteWithChildrenByClientId($uuid, $commit);
	}

	/**
	 * Gets the number of projects for a given object.
	 *
	 * @param string $userId the user id to filter
	 * @return Client[] list if matching items
	 */
	public function countProjects($uuid) {
		$projects = $this->projectMapper->getActiveObjectsByAttributeValue("client_uuid", $uuid, "created", true);
		return count($projects);
	}

	public function getHours($uuid) {
		$projects = $this->projectMapper->getActiveObjectsByAttributeValue("client_uuid", $uuid, "created", true);
		$sum = 0;
		if (count($projects) > 0) {
			foreach ($projects as $project) {
				$sum += $this->projectMapper->getHours($project->getUuid());
			}
		}
		return $sum;
	}
	
	/**
	 * remove all clients with status deleted
	 *
	 */
	public function cleanUp() {
		$qb = $this->db->getQueryBuilder();
		$qb
			->delete($this->tableName)
			->where($qb->expr()->eq('status', $qb->createNamedParameter('deleted')));
		$qb->executeStatement();
	}
}
