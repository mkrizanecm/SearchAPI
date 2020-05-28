<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\{TextType};


class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search")
     */
    public function index(Request $response)
    {
        // Check if there is a POST request
        $search_term = $response->request->get('form');

        if (!empty($search_term)) {
            $term = $search_term['term'];
        }

        $form = $this->createFormBuilder(null)
            ->add('term', TextType::class)
            ->getForm();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'form' => $form->createView(),
        ]);
    }

    public static function call()
    {
        $connection = $this->getDoctrine()->getManager();
    }
}
