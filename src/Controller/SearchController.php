<?php

namespace App\Controller;

use App\Entity\Records;
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
            $term = trim($search_term['term']);

            // First check if term already exists in database
            $exist_term = $this->getDoctrine()->getRepository('App\Entity\Records')->findOneBy([
                'term' => $term,
            ]);

            $term_result = 0;
            // If term already exists, just get results for the specific term
            if (!empty($exist_term->getTerm())) {
                $term_result = $exist_term->getResults();
            } else {
                $response = $this->call($term);
                if (!empty($response->total_count)) {
                    $this->save_record($term, $response->total_count);
                    $term_result = $response->total_count;
                }
            }

            // Get exist term result sum
            $entity = $this->getDoctrine()->getManager();
            $total_records_query = $entity->createQueryBuilder();
            $total_records_query = $total_records_query->select( 'SUM(r.results) as results' )
            ->from('App\Entity\Records', 'r' )	       
            ->getQuery();
             
            $total_records = $total_records_query->getOneOrNullResult();
            $total_records = (int)$total_records['results'];
        }

        $form = $this->createFormBuilder(null)
            ->add('term', TextType::class)
            ->getForm();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'form' => $form->createView(),
        ]);
    }

    public function save_record($term, $results)
    {
        // Save a new record
        $entity = $this->getDoctrine()->getManager();

        $record = new Records();

        $record->setTerm($term);
        $record->setResults($results);
        $record->setDatetimeCreated(new \DateTime('@'.strtotime('now')));

        $entity->persist($record);
        $entity->flush();
    }

    public function call($term)
    {
        // Call API
        $url = "https://api.github.com/search/issues?q={$term}";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }    
}
