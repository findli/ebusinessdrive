<?php

namespace Applcation\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\Db\NoRecordExists;

class ProductForm extends Form implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $services;

    function initialize() {
        $translator = $this->getServiceLocator()
                           ->getServiceLocator()
                           ->get('MVCTranslator');

        $this->add(
            [
                'name'    => 'name',
                'type'    => 'text',
                'options' => [
                    'label' => $translator->translate('Название товара'),
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'category',
                'type'       => 'select',
                'attributes' => [
                    'id' => 'category',
                ],
                'options'    => [
                    'label' => $translator->translate('Категория'),
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'submit',
                'attributes' => [
                    'type'  => 'submit',
                    'value' => $translator->translate('Сохранить')
                ],
            ]
        );
        $this->add(
            [
                'name' => 'security',
                'type' => 'Zend\Form\Element\Csrf'
            ]
        );
    }
}