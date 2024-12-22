<?php

namespace App\Controller;

use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TripController extends AbstractController
{
    #[Route('/trip', name: 'trip')]
    public function index(
        TripRepository $tripRepository
    ): Response
    {
        $trips = $tripRepository->findAll();

        return $this->render('home/trip.html.twig', [
            'trips' => $trips,
        ]);
    }
}
