<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
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

    #[Route('/cart/add/{id}/{target}', name: 'add_cart')]
    public function add_cart(CartService $cartService, $id, $target)
    {
        $cartService->add($id);
       // dd($cartService->getCartWithData());


        return $this->redirectToRoute($target);

    }

    #[Route('/cart/remove/{id}', name: 'remove_cart')]
    public function remove_cart(CartService $cartService, $id)
    {
        $cartService->remove($id);

        return $this->redirectToRoute('cart');

    }

    #[Route('/cart/delete/{id}', name: 'delete_cart')]
    public function delete_cart(CartService $cartService, $id)
    {
        $cartService->delete($id);

        return $this->redirectToRoute('cart');

    }

    #[Route('/cart/destroy', name: 'destroy_cart')]
    public function destroy_cart(CartService $cartService)
    {
        $cartService->destroy();

        return $this->redirectToRoute('cart');

    }


    #[Route('/cart', name: 'cart')]
    public function cart(CartService $cartService)
    {

        $cart=$cartService->getCartWithData();
        $total=$cartService->getTotal();

        return $this->render('home/cart.html.twig',[
            'total'=>$total,
            'cart'=>$cart
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
