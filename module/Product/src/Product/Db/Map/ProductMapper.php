<?php

namespace Product\Db\Map;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProductMapper implements ServiceLocatorAwareInterface
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

    public function getBy(array $param)
    {
        $repository = $this->getEntityManager()
                           ->getRepository('Product\Entity\Product');
        $all        = $repository->findBy($param);

        return $all;
    }

    public function getAllProducts()
    {
        $repository = $this->getEntityManager()
                           ->getRepository('Product\Entity\Product');
        $all        = $repository->findAll();

        return $all;
    }
}