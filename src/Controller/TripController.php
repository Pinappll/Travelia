<?php

namespace App\Controller;

use App\Repository\TripRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TripController extends AbstractController
{
    #[Route('/trip', name: 'trip')]
    public function index(
        TripRepository $tripRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        // Créez un QueryBuilder pour la pagination
        $query = $tripRepository->createQueryBuilder('t')->getQuery();
        $pagination = $paginator->paginate(
            $query, // Requête Doctrine ou tableau
            $request->query->getInt('page', 1), // Numéro de page
            5// Nombre d'éléments par page
        );

        return $this->render('home/trip.html.twig', [
            'trips' => $pagination, // Passe l'instance paginée au template
        ]);
    }
}
