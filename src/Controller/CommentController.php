<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trip;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/trips/{id}/comment', name: 'comment_create', methods: ['POST'])]
    public function create(
        Trip $trip,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérification si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour poster un commentaire.');
            return $this->redirectToRoute('app_login');
        }

        // Création d'un nouveau commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        // Gestion du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer les données nécessaires
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setPublisher($this->getUser());
            $comment->setTrip($trip);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été publié.');

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('trip_show', ['id' => $trip->getId()]);
        }

        // Redirection en cas d'erreur
        $this->addFlash('error', 'Erreur lors de la publication du commentaire.');
        return $this->redirectToRoute('trip_show', ['id' => $trip->getId()]);
    }

}
