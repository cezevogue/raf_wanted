<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'admin_category')]
    #[Route('/{id}', name: 'admin_category_update')]
    public function index(CategoryRepository $repository, Request $request, EntityManagerInterface $manager, $id = null): Response
    {
        // AFFICHAGE DES CATEGORIES
        // récupérer la liste des catégories (SELECT * FROM category)
        $categories = $repository->findAll();

        // MODIFICATION DU CATEGORIE
        if($id){
        $category = $repository->find($id);

        } else {
            $category = new Category();
        }
         // AJOUT DU CATEGORIE

         // génération du formulaire à partir de la classe CategoryType
         $form = $this->createForm(CategoryType::class, $category);

         // ici on va gérer la requête entrante
         $form->handleRequest($request);
 
         // on va vérifier si le formulaire a été soumis et est valide
         if ($form->isSubmitted() && $form->isValid()){
 
             // on crée une instance de la classe Category et à laquelle on passe ces valeurs
 
             // on récupère les valeurs du formulaire
             
             $category = $form->getData();
 
             // on persiste les valeurs
 
             $manager->persist($category);
 
             // on exécute la transaction
             $manager->flush();

             $this->addFlash('success', 'La catégorie a bien été ajoutée');
 
             // ensuite on redirige vers la route admin_category
             return $this->redirectToRoute('admin_category');
             }
           
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    // SUPPRESSION DES CATEGORIES
    #[Route('/delete/{id}', name: 'admin_category_delete')]
    public function delete(CategoryRepository $repository, EntityManagerInterface $manager, $id = null):Response
    {   if($id){
        $category = $repository->find($id);
        }
        $manager->remove($category);
        $manager->flush();
        
        return $this->redirectToRoute('admin_category');
    
    }
}


