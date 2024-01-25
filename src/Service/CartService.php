<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{

    private $repository;

    private $session;

    // on créé le constructeur pour injecter automatiquement la session et le productrepository à l'injection du service dans nos controllers
    public function __construct(ProductRepository $r, RequestStack $session)
    {
        $this->repository=$r;
        $this->session=$session;


    }

    public function add(int $id): void
    {
        // on récupère la session
        $local=$this->session->getSession();
        $cart=$local->get('cart', []);
        //    [ 'id'=>'quantite' ]
        // si l'indice $id n'existe pas dans le tableau, le produit n'a pas été encore ajouté, on initialise donc la quantité à 1
          if (!isset($cart[$id])){
              $cart[$id]=1;

          }else{
            // sinon  on incrémente la quantité
              $cart[$id]++;
          }

          // on met à jour la session

        $local->set('cart', $cart);


    }

    public function remove(int $id): void
    {
        // on récupère la session
        $local=$this->session->getSession();
        $cart=$local->get('cart', []);

        // on verifie la présence de cet id en indice de tableau et que la valeur (donc la quantité) soit strictement supérieur à 1
        if (isset($cart[$id]) && $cart[$id]>1){
            $cart[$id]--;


        }else{
            // sinon on supprimera totalement l'entrée ayant cet id
           unset($cart[$id]);

        }


        // on met à jour la session
        $local->set('cart', $cart);

    }

    public function delete(int $id): void
    {
        // on récupère la session
        $local=$this->session->getSession();
        $cart=$local->get('cart', []);

           if (isset($cart[$id]))
           {
               unset($cart[$id]);
           }



        // on met à jour la session
        $local->set('cart', $cart);

    }

    public function destroy(): void
    {

        $this->session->getSession()->remove('cart');

    }

    public function getCartWithData(): array
    {
        // on récupère la session
        $local=$this->session->getSession();
        $cart=$local->get('cart', []);

        $cartWithData=[];

        foreach ($cart as $id=>$quantite)
        {
            $cartWithData[]=[
                'product'=>$this->repository->find($id),
                'quantity'=>$quantite
            ];

        }

        return $cartWithData;


    }

    public function getTotal(): float
    {
        $total=0;

        foreach ($this->getCartWithData() as $data)
        {
            $total+=$data['product']->getPrice()*$data['quantity'];


        }

        return $total;
    }




}