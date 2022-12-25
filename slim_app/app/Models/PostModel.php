<?php
namespace App\Models;
use PDO;

class PostModel {
  private $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function get(): array
  {
    //$result = [];
    //$dbresult =
	return    $this->connection->query('SELECT * FROM contents')->fetchAll();
    //foreach ($dbresult as $row) {
    //  $result[] = $row;
    //}
    //return $result;
  }

  public function create($data): array
  {
    $result = [];
     return $result;
  }
}
