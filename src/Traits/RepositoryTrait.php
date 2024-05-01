<?php

namespace GestionDuStock\Traits;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

trait RepositoryTrait
{
    public function createTable(): bool
    {
        $schemaTool = new SchemaTool($this->getEntityManager());
        try {
            $schemaTool->createSchema([$this->getClassMetadata()]);

            return true;
        } catch (ToolsException $e) {
            return false;
        }
    }

    public function dropTable(): bool
    {
        $tableName = $this->getClassMetadata()->getTableName();
        $sql = 'DROP TABLE IF EXISTS ' . $tableName . ';';
        $connection = $this->getEntityManager()->getConnection();
        $connection->getConfiguration()->setSQLLogger();
        try {
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $stmt->closeCursor();

            return true;
        } catch (DBALException $e) {
            return false;
        }
    }

}