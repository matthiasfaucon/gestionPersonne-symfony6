<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('personne')]

class PersonneController extends AbstractController
{

    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPerson(Personne $personne = null, ManagerRegistry $doctrine, Request $request): Response
    {
        $new = false;
        if (!$personne) {
            // mise à jour de la personne
            $new = true;
            $personne = new Personne();
        }

        $repository = $doctrine->getRepository(Personne::class);

        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entiteManager = $doctrine->getManager();
            $entiteManager->persist($personne);

            $entiteManager->flush();

            if ($new === true) {
                $message = "a été créé avec succès";
            } else {
                $message = "a été édité avec succès";
            }
            $this->addFlash('success', $personne->getNom() . " " . $personne->getPrenom() . " " . $message);
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/addPerson.html.twig', [
            'personne' => $personne,
            'formPassed' => $form->createView(),
        ]);
    }

    #[Route('/{page<\d+>?1}/{nbrPerPage<\d+>?12}', name: 'personne.list')]
    public function displayPersonne(ManagerRegistry $doctrine, Request $request, $page, $nbrPerPage): Response
    {
        $personne = new Personne(); //initialisation d'un constructeur 
        $repository = $doctrine->getRepository(Personne::class);
        $nbrPersonne = $repository->count([]);
        $nbrPages = ceil($nbrPersonne / $nbrPerPage);

        $personnes = $repository->findBy([], [], $nbrPerPage, ($nbrPerPage * ($page - 1)));

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrPages' => $nbrPages,
            'page' => $page,
            'nbrPerPage' => $nbrPerPage
        ]);
    }

    #[Route('/filter/{ageMin}/{ageMax}', name: 'personne.filter')]
    public function displayFilteredPersonne(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonByAgeInterval($ageMin, $ageMax);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    }

    #[Route('/stats/{ageMin}/{ageMax}', name: 'personne.stats')]
    public function displayFilteredPersonneStats(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->findPersonByAgeIntervalStatistic($ageMin, $ageMax);

        return $this->render('personne/personStat.html.twig', [
            'stats' => $stats[0],
            'ageMin' => $ageMin,
            'ageMax' => $ageMax
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
        // Pas nécessaire avec le param Convertor 
        // $repository = $doctrine->getRepository(Personne::class);
        // $personne = $repository->find($id);

        if (!$personne) {
            $this->addFlash('error', "la personne n'existe pas");
            return $this->redirectToRoute("personne.list");
        }

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deleteTodo(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        // On récupère la todo
        // si elle existe
        if ($personne) {
            // suppression de la todo
            $manager = $doctrine->getManager();
            $manager->remove($personne);
            $manager->flush(); // exécute la transaction 
            $this->addFlash('success', 'La personne a bien été modifié');
        }
        //sinon
        else {
            //message d'erreur
            $this->addFlash('error', 'La personne ne peut pas être supprimé, vérifiez son existence');
        }
        return $this->redirectToRoute('personne.list');
    }
}