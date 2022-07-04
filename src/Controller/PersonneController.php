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

    #[Route('/add', name: 'personne.add')]
    public function addPerson(ManagerRegistry $doctrine, Request $request): Response
    {
        $personne = new Personne(); //initialisation d'un constructeur 
        $repository = $doctrine->getRepository(Personne::class);

        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $entiteManager = $doctrine->getManager();
            $entiteManager->persist($personne);
            
            $entiteManager->flush();
            $this->addFlash('success', 'La personne a été ajoutée avec succès');
            return $this->redirectToRoute('personne.list');
        }
        
        return $this->render('personne/addPerson.html.twig', [
            'personne' => $personne,
            'formPassed'=> $form->createView(),
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

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
        // Pas nécessaire avec le param Convertor 
        // $repository = $doctrine->getRepository(Personne::class);
        // $personne = $repository->find($id);

        if(!$personne){
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
    if($personne){
        // suppression de la todo
        $manager = $doctrine->getManager();
        $manager->remove($personne);
        $manager->flush(); // exécute la transaction 
        $this->addFlash('success', 'La personne a bien été modifié');
    }
    //sinon
    else{
        //message d'erreur
        $this->addFlash('error', 'La personne ne peut pas être supprimé, vérifié son existence');
    }
        return $this->redirectToRoute('personne.list');  
    }

    #[Route('/udpate/{id}/{nom}/{prenom}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $nom, $prenom, $age): RedirectResponse
    {
    // Vérifier que la personne existe
        // si elle existe
    if($personne){
        // mise à jour de la personne
        $manager = $doctrine->getManager();

        $personne->setNom($nom);
        $personne->setPrenom($prenom);
        $personne->setAge($age);
        $manager->persist($personne); // si il y a un id elle sait que c'est une mise à jour sinon elle sait que c'est un ajout 
        
        $manager->flush(); // exécute la transaction 
        
        $this->addFlash('success', 'La personne a bien été supprimé');
    }
    //sinon
    else{
        //message d'erreur
        $this->addFlash('error', 'La personne ne peut pas être supprimé, vérifié son existence');
    }
        return $this->redirectToRoute('personne.list');  
    }
}

