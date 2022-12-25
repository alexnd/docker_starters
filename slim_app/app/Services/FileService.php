<?php
namespace App\Services;
use PDO;

class FileService {
 
  private $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  /**
   * Get All Files List
   */
  public function getAll(): array
  {
    $result = [];
    try {
      $rows = $this->connection
        ->query("SELECT * FROM files")
        ->fetchAll(PDO::FETCH_OBJ);
      $result['files'] = $rows;
    } catch (\PDOException $e) {
      $result['files'] = [];
      $result['error'] = $e->getMessage();
    } finally {
      return $result;
    }
  }

  public function create($data): bool
  {
    $result = [];
    try {
      $this->connection
	   ->prepare("INSERT INTO `files` () VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")  
	   ->execute($data);
      $result['success'] = true;
    } catch (\PDOException $e) {
      $result['error'] = $e->getMessage();
    } finally {
      return $result;
    }
  }
}
