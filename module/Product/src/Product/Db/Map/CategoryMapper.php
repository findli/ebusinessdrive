<?php

namespace Product\Db\Map;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryMapper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    protected $serviceLocator;
    protected $em;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getEntityManager()
    {
        if (is_null($this->em)) {
            $this->em = $this->getServiceLocator()
                             ->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }

    public function getAllCategories()
    {

        $repository = $this->getEntityManager()
                           ->getRepository('Product\Entity\Category');
        $all        = $repository->findAll();
        $result     = array_reduce(
            $all, function ($result, $item) {
            $result[$item->id] = $item->name;

            return $result;
        }
        );

        return $result;
    }
}