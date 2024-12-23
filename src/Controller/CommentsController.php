<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Comment1Type;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CommentsController extends AbstractController
{
    #[Route('/admin/comments',name: 'app_comments_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $commentRepository->createQueryBuilder('c')->getQuery();
        $pagination = $paginator->paginate(
            $query,                                // Requête Doctrine
            $request->query->getInt('page', 1),   // Numéro de la page actuelle
            10                                    // Nombre d'éléments par page
        );

        return $this->render('comments/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/admin/comments/new', name: 'app_comments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(Comment1Type::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/admin/comments/{id}', name: 'app_comments_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/admin/comments/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Comment1Type::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/admin/comments/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/my-comments', name: 'app_user_comments', methods: ['GET'])]
    public function myComments(
        CommentRepository $commentRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Crée une requête pour récupérer les commentaires de l'utilisateur courant
        $query = $commentRepository->createQueryBuilder('c')
            ->where('c.publisher = :user')
            ->setParameter('user', $user)
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,                                // Requête Doctrine
            $request->query->getInt('page', 1),   // Numéro de la page actuelle
            10                                    // Nombre d'éléments par page
        );

        return $this->render('comments/my_comments.html.twig', [
            'pagination' => $pagination,
        ]);
    }

}
