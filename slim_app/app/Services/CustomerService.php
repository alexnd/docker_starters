<?php
namespace App\Services;
use PDO;

/*CREATE TABLE `Customer` (
  `customerId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customerLastName` varchar(512) DEFAULT NULL,
  `customerPhone` varchar(24) DEFAULT NULL,
  `customerMail` varchar(128) DEFAULT NULL,
  `customerKurs` varchar(100) NOT NULL,
  `customerGroup` varchar(100) NOT NULL,
  `customerFak` varchar(100) NOT NULL,
  `customerSpetc` varchar(100) NOT NULL,
  `customerKaf` varchar(100) NOT NULL,
  `customerUniverId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`customerId`),
  KEY `customerLastName` (`customerLastName`(255)),
  KEY `customerPhone` (`customerPhone`),
  KEY `customerMail` (`customerMail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

class CustomerService {
 
  private $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  /**
   * Get All Customers List
   */
  public function getAll(): array
  {
    $result = [];
    try {
      $rows = $this->connection
        ->query('SELECT * FROM Customer')
        ->fetchAll(PDO::FETCH_OBJ);
      $result['customers'] = $rows;
    } catch (\PDOException $e) {
      $result['customers'] = [];
      $result['error'] = $e->getMessage();
    } finally {
      return $result;
    }
  }

  /**
   * Get One Customer
   */
  public function get(int $id): array {
    $result = [];
    try {
      $rows = $this->connection
        ->query("SELECT * FROM Customer WHERE customerId = $id")
        ->fetchAll(PDO::FETCH_OBJ);
      $result['customer'] = $rows[0];
    } catch (\PDOException $e) {
      $result['customer'] = [];
      $result['error'] = $e->getMessage();
    } finally {
      return $result;
    }
  }

  /**
   * Find Customers by parameters
   */
  public function find(string $param, string $name) {
      $result = [];
      try {
          $rows = $this->connection
              ->query("SELECT * FROM {$this->module_prefix}Customer WHERE `$param` LIKE '$name'")
              ->fetchAll(PDO::FETCH_OBJ);
          #_log_truncate();
          #_log($rows);
          $result['customer'] = $rows[0];
      } catch (\PDOException $e) {
          $result['customer'] = [];
          $result['error'] = $e->getMessage();
      } finally {
          return $result;
      }
  }

  /**
   * Create order
   */
  public function create(array $data) {
      $result = [];
      try {
          $rows = $this->connection
              ->prepare("INSERT INTO {$this->module_prefix}Customer VALUES ()")
              ->execute($data);
          $result['task'] = $rows[0];
      } catch (\PDOException $e) {
          $result['task'] = [];
          $result['error'] = $e->getMessage();
      } finally {
          return $result;
      }
  }

  /**
   * Update order
   */
  public function update(int $id, array $data) {

  }
}
