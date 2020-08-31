<?php


namespace App\Helpers;


use App\Entity\Produit;

class TvaHelper
{

    const TVA = 0.2;

    public function getTva20(){
        return self::TVA * 100;
    }

    public function getPriceTTC(Produit $produit){
        return $produit->getPrixProduit() * (1 + self::TVA);
    }
}