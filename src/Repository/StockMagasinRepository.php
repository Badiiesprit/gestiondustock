<?php

namespace GestionDuStock\Repository;

use Doctrine\ORM\EntityRepository;
use GestionDuStock\Traits\RepositoryTrait;


class StockMagasinRepository extends EntityRepository
{
    use RepositoryTrait;

}