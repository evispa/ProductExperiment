<?php

namespace Evispa\Resource\Api\ProductResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Evispa\Resource\Component\MultipartResource\Annotations\Resource;

class ProductsController extends Controller
{
    /**
     * Get a product by its identifier.
     *
     * @ApiDoc
     * @View(templateVar="product")
     */
    public function getProductAction($slug)
    {
        $data = new \Evispa\Resource\Api\ProductResourceBundle\Rest\ProductData();
        $data->setSlug('pav1');
        $data["name"] = "Pavadinimas";
        $data["attributes"] = array(
            "15",
        );

        return \FOS\RestBundle\View\View::create($data);
    }

    /**
     * Get list of all products.
     *
     * @ApiDoc
     * @View(templateVar="products")
     */
    public function getProductsAction() {
        $data = new \Evispa\Resource\Api\ProductResourceBundle\Rest\ProductData();
        $data->setSlug('pav1');
        $data["name"] = "Pavadinimas";
        $data["attributes"] = array(
            'test',
        );

        $data2 = new \Evispa\Resource\Api\ProductResourceBundle\Rest\ProductData();
        $data2->setSlug('pav2');
        $data2["name"] = "Pavadinimas 2";
        $data2["attributes"] = array(
            'test',
        );

        return \FOS\RestBundle\View\View::create(array(
            $data,
            $data2,
        ));
    }

    /**
     * Create a new product.
     *
     * @ApiDoc
     * @View(templateVar="product")
     * @Resource("product", action="create")
     */
    public function postProductsAction(\Symfony\Component\HttpFoundation\Request $request) {
        $data = new \Evispa\Resource\Api\ProductResourceBundle\Rest\ProductData();
        $data->setSlug('pav1');
        $data["name"] = "Pavadinimas";

        $fb = $this->createFormBuilder(null, array(
            'csrf_protection' => false
        ));
        $fb->add('name', 'text', array(
            'property_path' => '[name]'
        ));

        $form = $fb->getForm();
        $form->setData($data);

        $view = \FOS\RestBundle\View\View::create();

        if (false === $request->request->get('form', false)) {
            if ('html' !== $view->getFormat()) {
                $form->addError(new \Symfony\Component\Form\FormError('Submit form data based on specified parameters.'));
            }
        } else {
            $form->submit($request);
            $data = $form->getData();
            if ($form->isValid()) {
                return new \Symfony\Component\HttpFoundation\Response($this->get('serializer')->serialize($data, 'json'));
            }
        }

        $view->setData($form);
        $view->setStatusCode(400);

        return $view;
    }

    /**
     * Update a product.
     *
     * @ApiDoc
     * @Resource("product", action="update")
     */
    public function putProductAction(\Symfony\Component\HttpFoundation\Request $request, $slug) {
        $data = new \Evispa\Resource\Api\ProductResourceBundle\Rest\ProductData();
        $data->setSlug('pav1');
        $data["name"] = "Pavadinimas";

        $fb = $this->createFormBuilder(null, array(
            'csrf_protection' => false
        ));
        $fb->add('name', 'text', array(
            'property_path' => '[name]'
        ));

        $form = $fb->getForm();
        $form->setData($data);

        if (false !== $request->request->get('form', false)) {
            $form->submit($request);
            $data = $form->getData();
            if ($form->isValid()) {
                return new \Symfony\Component\HttpFoundation\Response($this->get('serializer')->serialize($data, 'json'));
            }
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }
}