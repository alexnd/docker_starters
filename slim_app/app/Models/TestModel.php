<?php
namespace App\Models;
use PDO;

class TestModel {
  private $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function test(): array
  {
    /*$statement = $this->connection->prepare("SHOW TABLES");
    $statement->execute();
    foreach ($statement as $row) {
      // do something with $row
    }
    //or
    while ($row = $statement->fetch()) {
      // do something with $row
    }*/
    $result = [];
    $dbresult = $this->connection->query('SHOW TABLES')->fetchAll();
    foreach ($dbresult as $row) {
      $result[] = $row['Tables_in_diploms_informstest'];
    }
    return $result;
  }

}
