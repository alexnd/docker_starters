<?php

namespace App\Services;

use PDO;

/*
CREATE TABLE `degriOrder` (
`orderId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderCustomerId` int(11) unsigned NOT NULL DEFAULT '0',
  `orderTaskNum` int(11) unsigned DEFAULT NULL,
  `orderReady` enum('false','true') DEFAULT 'false',
  `orderDeposit` float(9,2) DEFAULT NULL,
  `orderDepositType` enum('nal','beznal','other') NOT NULL DEFAULT 'other',
  `orderPaymentFull` float(9,2) DEFAULT NULL,
  `orderPaymentAdd` int(11) NOT NULL DEFAULT '0',
  `orderDateBegin` date DEFAULT NULL,
  `orderDateEnd` date DEFAULT NULL,
  `orderClose` enum('false','true') DEFAULT NULL,
  `orderDateProtection` date DEFAULT NULL,
  `orderDirector` varchar(50) DEFAULT NULL,
  `orderWhere` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`orderId`),
  KEY `Ref_09` (`orderCustomerId`),
  KEY `Ref_11` (`orderTaskNum`),
  KEY `orderDateBegin` (`orderDateBegin`),
  KEY `orderDateEnd` (`orderDateEnd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

class OrderService
{

    private $connection;
    private $module_prefix;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->module_prefix = session('module', '');
    }

    /**
     * Get All Orders List
     */
    public function getAll(): array
    {
        $result = [];
        try {
            $rows = $this->connection
                ->query("SELECT * FROM `{$this->module_prefix}Order`")
                ->fetchAll(PDO::FETCH_OBJ);
            $result['orders'] = $rows;
        } catch (\PDOException $e) {
            $result['orders'] = [];
            $result['error'] = $e->getMessage();
        } finally {
            return $result;
        }
    }

    /**
     * Get One Order
     */
    public function get(int $id): array
    {
        $result = [];
        try {
            $rows = $this->connection
                ->query("SELECT * FROM `{$this->module_prefix}Order` WHERE orderId = $id")
                ->fetchAll(PDO::FETCH_OBJ);
            $result['order'] = $rows[0];
        } catch (\PDOException $e) {
            $result['order'] = [];
            $result['error'] = $e->getMessage();
        } finally {
            return $result;
        }
    }

    /**
     * Returns Predicted new Order id
     */
    public function getNextId(): int
    {
        $sql = <<<SQL
SELECT AUTO_INCREMENT
FROM information_schema.tables
WHERE table_name = '{$this->module_prefix}Order'
AND table_schema = DATABASE()
SQL;
        $result = $this->connection->query($sql)->execute();
        # _log_truncate();_log($result, 'getNextId()');
        return (empty($result)) ? -1 : $result;
    }

    /**
     * Create Order
     */
    public function create(array $data)
    {
        $result = [];
        try {
            $this->connection
                ->prepare("INSERT INTO `{$this->module_prefix}Order` (
                      orderCustomerId,
                      orderTaskNum,
                      orderDeposit,
                      orderDepositType,
                      orderPaymentFull,
                      orderPaymentAdd,
                      orderDateBegin,
                      orderDateEnd,
                      orderClose,
                      orderDateProtection,
                      orderDirector,
                      orderWhere
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute($data);
        } catch (\PDOException $e) {
            $result['error'] = $e->getMessage();
        } finally {
            return $result;
        }
    }

    /**
     * Update Order
     */
    public function update(int $id, array $data)
    {

    }
}
