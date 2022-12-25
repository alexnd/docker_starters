<?php
namespace App\Services;
use PDO;

/*
CREATE TABLE `testTask` (
  `taskId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `taskOrderId` int(11) unsigned NOT NULL,
  `taskNum` int(11) unsigned NOT NULL,
  `taskOrderId_Num` text,
  `taskPerformerId` int(11) unsigned DEFAULT NULL,
  `taskPerformerBlocked` int(1) NOT NULL DEFAULT '0',
  `taskSubjectId` int(11) DEFAULT NULL,
  `taskTypeWorkId` int(11) DEFAULT NULL,
  `taskTheme` text,
  `taskWishes` text,
  `taskCapacity` text,
  `taskFile` text,
  `taskReady` enum('false','true') DEFAULT 'false',
  `taskPaymentFull` float(9,2) unsigned DEFAULT NULL,
  `taskPaymentPerformer` float(9,2) unsigned DEFAULT NULL,
  `taskDatePerformerBegin` date DEFAULT NULL,
  `taskDatePerformer1` date DEFAULT NULL,
  `taskDatePerformer2` date DEFAULT NULL,
  `taskDatePerformerPlan` date DEFAULT NULL,
  `taskDatePerformerEnd` date DEFAULT NULL,
  `taskPaymentPerformerDate` date DEFAULT NULL,
  `taskPaymentPerformerMake` float(9,2) unsigned NOT NULL DEFAULT '0.00',
  `taskPlan` varchar(20) DEFAULT NULL,
  `taskZalog` tinyint(1) NOT NULL,
  PRIMARY KEY (`taskId`),
  KEY `Ref_03` (`taskSubjectId`),
  KEY `Ref_10` (`taskPerformerId`),
  KEY `taskOrderId` (`taskOrderId`),
  KEY `taskDatePerformerBegin` (`taskDatePerformerBegin`),
  KEY `taskDatePerformer1` (`taskDatePerformer1`),
  KEY `taskDatePerformer2` (`taskDatePerformer2`),
  KEY `taskDatePerformerPlan` (`taskDatePerformerPlan`),
  KEY `taskPaymentPerformerDate` (`taskPaymentPerformerDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

class TaskService {
 
  private $connection;
  private $module_prefix;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
    $this->module_prefix = session('module', '');
  }

  /**
   * Get All Tasks List
   */
  public function getAll(): array
  {
    $result = [];
    try {
      $rows = $this->connection
        ->query("SELECT * FROM {$this->module_prefix}Task")
        ->fetchAll(PDO::FETCH_OBJ);
      $result['tasks'] = $rows;
    } catch (\PDOException $e) {
      $result['tasks'] = [];
      $result['error'] = $e->getMessage();
    } finally {
      return $result;
    }
  }

  /**
   * Get One Customer
   */
  public function get(int $id): object {
    $result = [];
    try {
      $rows = $this->connection
        ->query("SELECT * FROM {$this->module_prefix}Task WHERE id = $id")
        ->fetchAll(PDO::FETCH_OBJ);
      $result['task'] = $rows[0];
    } catch (\PDOException $e) {
      $result['task'] = [];
      $result['error'] = $e->getMessage();
    } finally {
      return $result;
    }
  }

  /**
   * Find Task by parameters
   */
  public function find(string $param, string $name) {
      $result = [];
      try {
          $rows = $this->connection
              ->query("SELECT * FROM {$this->module_prefix}Task WHERE `$param` LIKE '$name'")
              ->fetchAll(PDO::FETCH_OBJ);
          $result['task'] = $rows[0];
      } catch (\PDOException $e) {
          $result['task'] = [];
          $result['error'] = $e->getMessage();
      } finally {
          return $result;
      }
  }

  /**
   * Create Task
   */
  public function create(array $data)
  {
      $result = [];
      try {
          $this->connection
              ->prepare("INSERT INTO {$this->module_prefix}Task (
                       taskOrderId,
                       taskNum,
                       taskOrderId_Num,
                       taskPerformerId,
                       taskPerformerBlocked,
                       taskSubjectId,
                       taskTypeWorkId,
                       taskTheme,
                       taskWishes,
                       taskCapacity,
                       taskFile,
                       taskReady,
                       taskPaymentFull,
                       taskPaymentPerformer,
                       taskDatePerformerBegin,
                       taskDatePerformer1,
                       taskDatePerformer2,
                       taskDatePerformerPlan,
                       taskDatePerformerEnd,
                       taskPaymentPerformerDate,
                       taskPaymentPerformerMake,
                       taskPlan,
                       taskZalog
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute($data);
      } catch (\PDOException $e) {
          $result['error'] = $e->getMessage();
      } finally {
          return $result;
      }
  }

  /**
   * Update Task
   */
  public function update(int $id, array $data) {
      $result = [];
      try {
          $rows = $this->connection
              ->query("SELECT * FROM {$this->module_prefix}Task WHERE id = $id")
              ->fetchAll(PDO::FETCH_OBJ);
          $result['task'] = $rows[0];
      } catch (\PDOException $e) {
          $result['task'] = [];
          $result['error'] = $e->getMessage();
      } finally {
          return $result;
      }
  }
}
