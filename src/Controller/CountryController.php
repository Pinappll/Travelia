<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CountryController extends AbstractController
{
    #[Route('/country', name: 'country')]
    public function index(
        CountryRepository $countryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        // Créez un QueryBuilder pour la pagination
        $queryBuilder = $countryRepository->createQueryBuilder('c')->orderBy('c.name', 'ASC');

        // Utilisez le paginator pour paginer les résultats
        $countries = $paginator->paginate(
            $queryBuilder, // Passez le QueryBuilder ici
            $request->query->getInt('page', 1), // Numéro de la page actuelle
            12 // Nombre d'éléments par page
        );


        return $this->render('home/country.html.twig', [
            'countries' => $countries,
        ]);
    }
}