<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]

class PersonneController extends AbstractController
{

    #[Route('/', name: 'personne.list')]
    public function displayPersonne(ManagerRegistry $doctrine, Request $request): Response
    {
        $personne = new Personne(); //initialisation d'un constructeur 
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $entiteManager = $doctrine->getManager();
            $personne->setIsCheckedTodo(false);
            $entiteManager->persist($personne);
            
            $entiteManager->flush();
            $this->addFlash('success', 'La personne a été ajoutée avec succès');
            return $this->redirectToRoute('personne.list');
        }
        
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'formPassed'=> $form->createView()
        ]);
    }


}

