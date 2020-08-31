<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(ProduitRepository $produitRepository)
    {
        $produits = $produitRepository->findAll();


        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'listeProduits' => $produits,
        ]);
    }
}
