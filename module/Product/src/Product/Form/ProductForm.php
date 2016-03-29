<?php

namespace Product\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ProductForm extends Form implements ServiceLocatorAwareInterface, InputFilterAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $services;
    protected $inputFilter;

    function initialize()
    {
        $translator = $this->getServiceLocator()
                           ->get('MVCTranslator');

        $this->add(
            [
                'name'    => 'id',
                'type'    => 'hidden',
            ]
        );
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
                'name' => 'csrf',
                'type' => 'Zend\Form\Element\Csrf'
            ]
        );
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|static
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                [
                    'name'     => 'id',
                    'required' => false,
                    'filters'  => [
                        ['name' => 'Int'],
                    ],
                ]
            );

            $inputFilter->add(
                [
                    'name'       => 'name',
                    'required'   => true,
                    'filters'    => [
                        ['name' => 'StripTags'],
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min'      => 1,
                                'max'      => 255,
                            ],
                        ],
                    ],
                ]
            );

            $inputFilter->add(
                [
                    'name'     => 'category',
                    'required' => true,
                    'filters'  => [
                        ['name' => 'Int'],
                    ],
                ]
            );


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}