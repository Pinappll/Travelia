<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trip;
use App\Form\CommentType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/trips/{id}', name: 'trip_show', methods: ['GET', 'POST'])]
    public function show(
        Trip $trip,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        // Création d'un nouveau commentaire
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        // Gestion de la soumission du formulaire
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setPublisher($this->getUser()); // Assurez-vous que l'utilisateur est connecté
            $comment->setTrip($trip);

            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('trip_show', ['id' => $trip->getId()]);
        }

        // Récupérer les commentaires liés
        $comments = $trip->getComments();

        return $this->render('trip/trip_show.html.twig', [
            'trip' => $trip,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }
}
