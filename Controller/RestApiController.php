<?php

namespace ReginaSharaeva\RestApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RestApiController extends Controller {

    public function createForm($type = null, $data = null, array $options = array())
    {
        $form = $this->container->get('form.factory')->createNamed(
            null, 
            $type,
            $data,
            $options
        );
        return $form;
    }

}