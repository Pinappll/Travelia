<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        CountryRepository $countryRepository,
        TripRepository $tripRepository
    ): Response
    {
        // Get 4 random trips
        $trips = $tripRepository->findAll();
        shuffle($trips);
        $trips = array_slice($trips, 0, 4);

        // Get 8 random countries
        $countries = $countryRepository->findAll();
        shuffle($countries);
        $countries = array_slice($countries, 0, 8);
        return $this->render('home/index.html.twig', [
            'countries' => $countries,
            'trips' => $trips,
        ]);
    }
}
