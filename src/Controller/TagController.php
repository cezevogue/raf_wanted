<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tag')]
class TagController extends AbstractController
{
    #[Route('/', name: 'admin_tag')]
    #[Route('/{id}', name: 'admin_tag_update')]
    public function index(TagRepository $repository, Request $request, EntityManagerInterface $manager, $id = null): Response
    {
        // récupérer la liste des tags (SELECT * FROM tag)
        $tags = $repository->findAll();

        // MODIFICATION DU PRODUIT
        if($id){
            $tag = $repository->find($id);
    
            } else {
                $tag = new Tag();
            }
        // AJOUT DU CATEGORIE

        // génération du formulaire à partir de la classe tagType
         $form = $this->createForm(TagType::class, $tag);

         // ici on va gérer la requête entrante
         $form->handleRequest($request);
 
         // on va vérifier si le formulaire a été soumis et est valide
         if ($form->isSubmitted() && $form->isValid()){
 
             // on crée une instance de la classe tag et à laquelle on passe ces valeurs

             // on récupère les valeurs du formulaire
             
             $tag = $form->getData();
 
             // on persiste les valeurs
 
             $manager->persist($tag);
 
             // on exécute la transaction
             $manager->flush();

             $this->addFlash('success', 'Le tag a bien été ajouté');
 
             // ensuite on redirige vers la route admin_tag
             return $this->redirectToRoute('admin_tag');
             }


        return $this->render('tag/index.html.twig', [
            'controller_name' => 'TagController',
            "tags" => $tags,
            'form' => $form->createView()
        ]);
    }
    // SUPPRESSION DES TAGS
    #[Route('/delete/{id}', name: 'admin_tag_delete')]
    public function delete(TagRepository $repository, EntityManagerInterface $manager, $id = null):Response
    {   if($id)
        {
            $tag = $repository->find($id);
        }
        $manager->remove($tag);
        $manager->flush();
        
        return $this->redirectToRoute('admin_tag');
    
    }
}
