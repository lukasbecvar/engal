<?php

namespace App\Helper;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/*
    Entity helper with 'main' CRUD actions
*/

class EntityHelper
{

    private $errorHelper;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ErrorHelper $errorHelper)
    {
        $this->errorHelper = $errorHelper;
        $this->entityManager = $entityManager;
    }

    public function insertEntity($entity): void
    {
        // set new entity row
        $this->entityManager->persist($entity);

        // try insert row
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
        }
    }

    public function isEntityExist(array $arr, $entity): bool
    {
        // default state
        $state = false;

        // init entity repository
        $repository = $this->entityManager->getRepository($entity::class);
        
        // try find value by column name
        try {
            $result = $repository->findOneBy($arr);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        // check if found
        if ($result !== null) {
            $state = true;
        } 

        return $state;
    }

    public function getEntityValue(array $arr, $entity) {
        
        // default value
        $value = null;

        // init entity repository
        $repository = $this->entityManager->getRepository($entity::class);
                
        // try find value by column name
        try {
            $result = $repository->findOneBy($arr);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }
        
        // check if found
        if ($result !== null) {
            $value = $result;
        } 
        
        return $value;
    }
}
