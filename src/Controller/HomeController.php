<?php

namespace App\Controller;

use App\Repository\HeaderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $productRepository, HeaderRepository $headerRepository): Response
    {
        $products = $productRepository->findByIsBest(1);
        $headers  = $headerRepository->findall();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headers'   => $headers
        ]);
    }
}
