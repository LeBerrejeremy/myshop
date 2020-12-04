<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductController extends AbstractController
{
    /**
     * @Route("/produits", name="products")
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        
        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){

            $products = $productRepository->findWithSearch($search);
        }else{
            $products = $productRepository->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show(Product $product, ProductRepository $productRepository, $slug): Response
    {
        $product  = $productRepository->findOneBySlug($slug);
        $products = $productRepository->findByIsBest(1);

        if(!$product){
            return $this->redirectToRoute('products');
        }
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}
