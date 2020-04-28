<?php

namespace App\Repository;

use PDO;
use PDOExecption;

class ShopRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get($shop)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM shops WHERE shop=:shop LIMIT 1"); 
        $stmt->bindParam(':shop', $shop);
        $stmt->execute(); 

        return $stmt->rowCount() > 0 ? $stmt->fetch() : null;
    }

    public function insert($data)
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO shops (shop, token) VALUES (:shop1, :token1) ON DUPLICATE KEY UPDATE token = :token2");
            foreach ($data as $key => $value) {
                $stmt->bindValue(sprintf(":%s1", $key), $value);
            }
            $stmt->bindValue("token2", $data['token']);
            $stmt->execute();
            $this->pdo->commit();

            return $this->pdo->lastInsertId();
        } catch(PDOExecption $e) {
            $this->pdo->rollback();
            return null;
        }
    }
}