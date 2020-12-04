<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;

class Cart
{   
    private $session;
    private $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;

        }else{

            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
            
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if($cart[$id] === 1){
            unset($cart[$id]);

        }else{

            $cart[$id]--;
        }

        $this->session->set('cart', $cart);
            
    }

    public function getFull(){

        $fullCart = [];
        
        if( $this->get()){

            foreach($this->get() as $id=>$quantity){

                $product_object = $this->productRepository->findOneById($id);

                if(!$product_object){

                    $this->delete($id);
                    continue;
                }

                $fullCart[] = [
                    'product'=> $product_object,
                    'quantity'=> $quantity
                ];
            }
        }
        return $fullCart;
    }

}