<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Produit;
use App\Helpers\TvaHelper;
use App\Repository\CardRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class CardController extends AbstractController
{
    /**
     * @Route("/card", name="card_index")
     */
    public function index(SessionInterface $session,
                          TvaHelper $tva,
                          ProduitRepository $produitRepository)
    {
        $panier = $session->get('panier', []);

        $panierAvecData = [];

        foreach ($panier as $id=>$quantite){

            $em = $this->getDoctrine()->getManager();
            $produits = $em->getRepository(Produit::class)->find($id);

            $panierAvecData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantite,
                'tva' => $tva->getTva20(),
                'prixTTC' => $tva->getPriceTTC($produits),
            ];
        }

        $totalTTC = 0;
        $totalHT = 0;
        $totalArticle = 0;

        foreach ($panierAvecData as $ligne){
            $totalLigneTTC = $ligne['prixTTC'] * $ligne['quantite'];
            $totalTTC += $totalLigneTTC;

            $totalLigneHT = $ligne['produit']->getPrixProduit() * $ligne['quantite'];
            $totalHT += $totalLigneHT;

            $totalArticle += $ligne['quantite'];

        }


        return $this->render('card/index.html.twig', [
            'controller_name' => 'CardController',
            'paniers' => $panierAvecData,
            'totalHT' => $totalHT,
            'totalPanier' => $totalTTC,
            'nbArticle' => $totalArticle,
            'totalTVA' => $totalHT * $tva->getTva20()/100,
        ]);
    }


    /**
     * @Route("/card/add/{id}", name="card_add")
     */
    public function add($id, SessionInterface $session){

        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;

        }else{
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('card_index');
    }

    /**
     * @Route("/card/ajaxaddquantity/{id}")
     */
    public function ajaxAdd($id, SessionInterface $session, ProduitRepository $produitRepository){

        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }

        $produits= $produitRepository->find($id);

        $session->set('panier', $panier);

        $response = new Response(json_encode(array(
            'info' => true,
            'quantite' => $panier[$id],
            'prix' => $produits->getPrixProduit(),
        )));
        $response->headers->set('Content-Type', 'application/html');

        return $response;
    }

    /**
     * @Route("/card/ajax-supp-quantity/{id}")
     */
    public function ajaxSupp($id, SessionInterface $session, ProduitRepository $produitRepository){

        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]--;
        }

        $produits= $produitRepository->find($id);

        $session->set('panier', $panier);

        $response = new Response(json_encode(array(
            'info' => true,
            'quantitesupp' => $panier[$id],
            'prix' => $produits->getPrixProduit(),
        )));
        $response->headers->set('Content-Type', 'application/html');

        return $response;
    }

    /**
     * @Route("/card/deleate/{id}", name="deleate_card")
     */
    public function deleate($id, SessionInterface $session){

        $panier = $session->get('panier', []);

        unset($panier[$id]);

        $session->set('panier', $panier);

        if(empty($panier)){
            return $this->redirectToRoute('produit');
        }else{
            return $this->redirectToRoute('card_index');
        }
    }

    /**
     * @Route("/card/deleate_all/", name="deleate_card_all")
     */
    public function deleateAll(SessionInterface $session){

        $session->set('panier', []);

        return $this->redirectToRoute('produit');
    }

    /**
     * @Route("/card/validation/", name="validation_card")
     */
    public function validationCard(Request $request,
                                   SessionInterface $session,
                                   ProduitRepository $produitRepository,
                                    CardRepository $cardRepository)
    {
        $panier = $session->get('panier');

        $em = $this->getDoctrine()->getManager();

        foreach ($panier as $id=>$quantite){
            $commande = new Card();
            $commande->setQuantity($quantite);
            $commande->setUser($this->getUser());

            $product = $produitRepository->findOneBy(['id' => $id]);
            $commande->setProduct($product);

            $em->persist($commande);
            $em->flush();
        }

        $session->set('panier', []);

        $user = $this->getUser();

        $commande = $cardRepository->findBy(['user' => $user]);


        return $this->render('card/validation.html.twig', [
            'controller_name' => 'CardController',
            'commandes' => $commande,
        ]);

    }

    /**
     * @Route("/admin/card/ma-commande/", name="my_card")
     */
    public function myCard(CardRepository $cardRepository)
    {
        $user = $this->getUser();
        $my_card = $cardRepository->findBy(['user' => $user]);

        return $this->render('admin/card/myCard.html.twig', array(
            'commandes' => $my_card,
        ));

    }
}
