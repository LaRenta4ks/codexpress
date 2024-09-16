<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/notes')] // Suffixe pour les routes du controller
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr): Response
    {
        return $this->render('note/all.html.twig', [
            'allNotes' => $nr->findBy(['is_public' => true], ['created_at' => 'DESC']),
        ]);
    }

    #[Route('/n/{slug}', name: 'app_note_show', methods: ['GET'])]
    public function show(string $slug, NoteRepository $nr): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $nr->findOneBySlug($slug),
        ]);
    }

    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(
        string $username,
        UserRepository $user, // On récupère le repository de l'entité User
    ): Response {
        $creator = $user->findOneByUsername($username); // On recherche l'utilisateur
        return $this->render('note/user.html.twig', [
            'creator' => $creator, // On envoie les données de l'utilisateur à la vue Twig
            'userNotes' => $creator->getNotes(), // On récupère les notes de l'utilisateur
        ]);
    }
    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(NoteType::class); // Chargement du formulaire
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $note = new Note();
            $note
                ->setTitle($form->get('title')->getData())
                ->setSlug($slugger->slug($note->getTitle()))
                ->setContent($form->get('content')->getData())
                ->setPublic($form->get('is_public')->getData())
                ->setCategory($form->get('category')->getData())
                ->setCreator($form->get('creator')->getData())
            ;
            $em->persist($note);
            $em->flush();

            dd($note); // Dump and die pour voir les données
        }
        return $this->render('note/new.html.twig', [
            'noteForm' => $form
        ]);
    }
    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); // On recherche la note à modifier
        return $this->render('note/edit.html.twig', []);
    }

    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); // On recherche la note à supprimer
        $this->addFlash('success', 'La note a bien été supprimée.'); // On ajoute un message de succès
       
       
        return $this->redirectToRoute('app_note_user'); // On redirige vers la page de l'utilisateur
    }
}