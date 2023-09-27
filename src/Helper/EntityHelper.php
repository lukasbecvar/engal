<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;

/*
    Entity helper with 'main' CRUD actions
*/

class EntityHelper
{

    private $entityManager;
    private $errorHelper;

    public function __construct(EntityManagerInterface $entityManager, ErrorHelper $errorHelper)
    {
        $this->entityManager = $entityManager;
        $this->errorHelper = $errorHelper;
    }

    // insert new entity row
    public function insertEntity($entity): void
    {
        // set new entity row
        $this->entityManager->persist($entity);

        try {

            // flush entity row
            $this->entityManager->flush();

        } catch (\Exception $e) {
            $this->errorHelper->handleError('flush error: '.$e->getMessage(), 500);
        }
    }

    // check if entity exist in database
    public function isEntityExist($arr, $entity): bool
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

    // get entity value by arr
    public function getEntityValue($arr, $entity) {
        
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
