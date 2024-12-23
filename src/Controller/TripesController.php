<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tripes')]
final class TripesController extends AbstractController
{
    #[Route(name: 'app_tripes_index', methods: ['GET'])]
    public function index(TripRepository $tripRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupération des données avec pagination
        $query = $tripRepository->createQueryBuilder('t')->getQuery();
        $trips = $paginator->paginate(
            $query,                                // Requête Doctrine
            $request->query->getInt('page', 1),   // Numéro de la page actuelle
            10                                    // Nombre d'éléments par page
        );

        return $this->render('tripes/index.html.twig', [
            'trips' => $trips,
        ]);
    }

    #[Route('/new', name: 'app_tripes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();

            return $this->redirectToRoute('app_tripes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tripes/new.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tripes_show', methods: ['GET'])]
    public function show(Trip $trip): Response
    {
        return $this->render('tripes/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tripes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tripes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tripes/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tripes_delete', methods: ['POST'])]
    public function delete(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tripes_index', [], Response::HTTP_SEE_OTHER);
    }
}
