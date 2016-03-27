<?php

namespace Product\Controller;

use Applcation\Form\ProductForm;
use Product\Entity\Product;
use Helper\Controller\DefaultController;
use Zend\View\Model\ViewModel;

class ProductController extends DefaultController
{
    public function indexAction() {
        $this->redirect()
             ->toRoute('product');

    }

    public function listAction() {
        return new ViewModel(
            [
                'products' => $this->getEntityManager()
                                   ->getRepository('Product\Entity\Product')
                                   ->findAll(),
            ]
        );
    }

    public function addAction() {
        $form = new ProductForm();
        $form->get('submit')
             ->setValue($this->translate('Добавить'));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $product = new Product();
            $form->setInputFilter($product->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $product->exchangeArray($form->getData());
                $this->getEntityManager()
                     ->persist($product);
                $this->getEntityManager()
                     ->flush();

                return $this->redirect()
                            ->toRoute('product');
            }
        }

        return ['form' => $form];
    }

    public function editAction() {
        $id = (int) $this->params()
                         ->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()
                        ->toRoute(
                            'product', [
                                         'action' => 'add'
                                     ]
                        );
        }

        $product = $this->getEntityManager()
                        ->find('Product\Entity\Product', $id);
        if (!$product) {
            return $this->redirect()
                        ->toRoute(
                            'product', [
                                         'action' => 'index'
                                     ]
                        );
        }

        $form = new ProductForm();
        $form->bind($product);
        $form->get('submit')
             ->setAttribute('value', $this->translate('Сохранить'));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($product->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()
                     ->flush();

                return $this->redirect()
                            ->toRoute('product');
            }
        }

        return [
            'id'   => $id,
            'form' => $form,
        ];
    }

    public function deleteAction() {
        $id = (int) $this->params()
                         ->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()
                        ->toRoute('product');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id      = (int) $request->getPost('id');
                $product = $this->getEntityManager()
                                ->find('Product\Entity\Product', $id);
                if ($product) {
                    $this->getEntityManager()
                         ->remove($product);
                    $this->getEntityManager()
                         ->flush();
                }
            }

            return $this->redirect()
                        ->toRoute('product');
        }

        return [
            'id'      => $id,
            'product' => $this->getEntityManager()
                              ->find('Product\Entity\Product', $id)
        ];
    }
}
