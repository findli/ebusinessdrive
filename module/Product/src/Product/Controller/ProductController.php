<?php

namespace Product\Controller;

use Product\Db\Map\CategoryMapper;
use Product\Db\Map\ProductMapper;
use Product\Form\ProductForm;
use Product\Entity\Product;
use Helper\Controller\DefaultController;
use Zend\View\Model\ViewModel;

class ProductController extends DefaultController
{
    public function indexAction()
    {
        $this->redirect()
             ->toRoute('product');

    }

    /**
     * @todo add paginator
     * @return ViewModel
     */
    public function listAction()
    {
        $category_id = (isset($_GET['category'])) ? $_GET['category'] : '';
        if (ctype_digit($category_id)) {
            $products = (new ProductMapper($this->getServiceLocator()))->getBy(['category_id' => $category_id]);
        } else {
            $products = (new ProductMapper($this->getServiceLocator()))->getAllProducts();
        }
        $allCategories = (new CategoryMapper($this->getServiceLocator()))->getAllCategories();
        $allCategories = ['' => 'Все'] + $allCategories;

        return new ViewModel(
            [
                'products'        => $products,
                'categories'      => $allCategories,
                'currentCategory' => $category_id,
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new ProductForm();
        $form->setServiceLocator($this->getServiceLocator());
        $form->initialize();
        $pm = new CategoryMapper($this->getServiceLocator());
        $ac = $pm->getAllCategories();
        $form->get('category')
             ->setValueOptions($ac);
        $form->get('submit')
             ->setValue($this->translate('Добавить'));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $product = new Product();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data                = $form->getData();
                $data['category_id'] = $this->getEntityManager()->getRepository('Product\Entity\Category')->findOneBy(['id' => $data['category']]);
                foreach ($data as $key => $value) {
                    $product->$key = $value;
                }
                $this->getEntityManager()
                     ->persist($product);
                $this->getEntityManager()
                     ->flush();

                return $this->redirect()
                            ->toRoute('product', ['action' => 'index']);
            }
        }

        return ['form' => $form];
    }

    public function editAction()
    {
        $id = (int)$this->params()
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
                                'action' => 'list'
                            ]
                        );
        }
        $productArr             = $product->getArrayCopy();
        $productArr['category'] = $productArr['category_id']->id;
        unset($productArr['category_id']);
        $form = new ProductForm();
        $form->setServiceLocator($this->getServiceLocator());
        $form->initialize();
        $pm = new CategoryMapper($this->getServiceLocator());
        $ac = $pm->getAllCategories();
        $form->get('category')
             ->setValueOptions($ac);
        $form->get('submit')
             ->setAttribute('value', $this->translate('Сохранить'));
        $form->setData($productArr);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data                = $request->getPost();
            $data['category_id'] = $this->getEntityManager()->getRepository('Product\Entity\Category')->findOneBy(['id' => $data['category']]);
            foreach ($data as $key => $value) {
                $product->$key = $value;
            }

            if ($form->isValid()) {
                $this->getEntityManager()
                     ->flush();

                return $this->redirect()
                            ->toRoute('product', ['action' => 'index']);
            }
        }

        return [
            'id'   => $id,
            'form' => $form,
        ];
    }

    public function deleteAction()
    {
        $id = (int)$this->params()
                        ->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()
                        ->toRoute('product');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id      = (int)$request->getPost('id');
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
