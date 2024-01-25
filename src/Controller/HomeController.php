<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('home/home.html.twig', [
            'products' => $products
        ]);
    }

    // AFFICHAGE DES DETAILS DES PRODUITS
    #[Route('/product/{id}', name: 'product_details')]
    public function product_details(ProductRepository $productRepository, $id = null):Response
    {   

            $product_details = $productRepository->find($id);
            // dd($product_details);
            //return $this->redirectToRoute('product_details');


        return $this->render('home/product.html.twig', [
            'product_details' => $product_details
        ]);
    
    }
}
