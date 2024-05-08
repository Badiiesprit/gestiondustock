<?php

namespace GestionDuStock\Repository;

use Doctrine\ORM\EntityRepository;
use GestionDuStock\Traits\RepositoryTrait;


class OrdersMagasinRepository extends EntityRepository
{
    use RepositoryTrait;

}