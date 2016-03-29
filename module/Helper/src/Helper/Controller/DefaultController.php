<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Helper\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Doctrine\ORM\EntityManager;

class DefaultController extends AbstractActionController
{
    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;
    public $translator;

    public function getEntityManager() {
        if (is_null($this->em)) {
            $this->em =
                $this->getServiceLocator()
                     ->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }

    public function translate($text) {
        if (is_null($this->translator)) {
            $this->translator = $this->getServiceLocator()
                                     ->get('MVCTranslator');
        }

        return $this->translator->translate($text);
    }
}
