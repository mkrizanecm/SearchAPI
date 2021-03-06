<?php

namespace App\Controller;

use App\Entity\Records;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\{TextType};
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType};


class SearchController extends AbstractController
{
    /**
     * @Route("", name="search")
     */
    public function index(Request $response)
    {
        $final_result = '';
        $form = $this->createFormBuilder(null)
            ->add('term', TextType::class)
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'GitHub API' => 'api.github.com',
                    // Here we can add other sites if they have similiar APIs (Twitter etc.)
                ],
            ])
            ->getForm();

        // Check if there is a POST request
        $search_term = $response->request->get('form');

        if (!empty($search_term)) {
            $term = trim($search_term['term']);
            if (!empty($term)) {
                $term_category = $search_term['category'];

                // First check if term already exists in database
                $exist_term = $this->getDoctrine()->getRepository('App\Entity\Records')->findOneBy([
                    'term' => $term,
                ]);
    
                $term_result = 0;
                // If term already exists, just get results for the specific term else save it to database
                if (!empty($exist_term)) {
                    $term_result = $exist_term->getResults();
                } else {
                    $response = $this->call($term_category, $term);
                    if (!empty($response->total_count)) {
                        $this->save_record($term, $response->total_count, $term_category);
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
                
                // Calculate term popularity
                if ($term_result == 0) {
                    $term_score = 0;
                } else {
                    $term_score = 100 - (1 - $term_result / $total_records) * 100;
                    $term_score = $term_score / 10;   
                    $term_score = number_format((float)$term_score, 2, '.', '');
                }
    
                $final_result = [
                    'term' => $term,
                    'score' => $term_score,
                ];
                $final_result = json_encode($final_result);
            }
        }

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'form' => $form->createView(),
            'final_result' => $final_result,
        ]);
    }

    public function save_record($term, $results, $category)
    {
        // Save a new record
        $entity = $this->getDoctrine()->getManager();

        $record = new Records();

        $record->setTerm($term);
        $record->setResults($results);
        $record->setCategory($category);
        $record->setDatetimeCreated(new \DateTime('@'.strtotime('now')));

        $entity->persist($record);
        $entity->flush();
    }
    
    public function call($term_category, $term)
    {
        // Call API
        $url = "https://$term_category/search/issues?q={$term}";

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
