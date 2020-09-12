<?php


namespace App\Controller;


use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     * @param ArticlesRepository $articlesRepository
     * @return Response
     */
    public function showArticles (ArticlesRepository $articlesRepository): Response
    {
     $allArticles = $articlesRepository->findAll();
     return $this->render('shop/shop.html.twig', ['allArticles' => $allArticles]);
    }
}