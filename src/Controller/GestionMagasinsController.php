<?php
namespace GestionDuStock\Controller;

use DateTime;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\PhpExecutableFinder;
use PrestaShopBundle\Entity\Magasin;
use PrestaShopBundle\Entity\StockMagasin;
use GestionDuStock\Form\MagasinType;
use GestionDuStock\Controller\GestionStockController;
use Configuration;
use Db;
use Context; 
use Currency;
use Product;
use Validate;

class GestionMagasinsController extends FrameworkBundleAdminController
{

    public function editAction(Request $request)
    {
        $magasins = $this->getDoctrine()->getRepository(Magasin::class)->findAll();

        return $this->render(
            '@Modules/gestiondustock/src/Resources/magasins/all_magasins.html.twig',
            array(
                'magasins' => $magasins,
                'layoutTitle' => 'Toute Les Magasins Du LEGY'
            )
        );
    }

    public function addOrUpdateMagasins(Request $request, Magasin $magasin = null)
    {
        $addMagasins = false;

        if (!$magasin) {
            $magasin = new Magasin();
            $addMagasins = true;
        }

        $magasinForm = $this->createForm(MagasinType::class, $magasin);

        $magasinForm->handleRequest($request);
        if ($magasinForm->isSubmitted() && $magasinForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($magasin);
            $entityManager->flush();

            // Ajouter un message flash de succès
            if($addMagasins){
                $this->addFlash('success', 'Le magasin a été créé avec succès !');
            }else{
                $this->addFlash('success', 'Le magasin a été modifier avec succès !');
            }
            

            // Redirection vers une autre page après traitement du formulaire
            return $this->redirectToRoute('gestiondustock_gestion_magasins');
        }
        return $this->render(
            '@Modules/gestiondustock/src/Resources/magasins/add_update_magasins.html.twig',
            array(
                'addMagasins' => $addMagasins,
                'magasinForm' => $magasinForm->createView(),
                'layoutTitle' => $addMagasins?'Ajouter Magasin':'Update Magasin'
            )
        );
    }

    public function editMagasins(Magasin $magasin) 
    {
        $gestionStockController = new GestionStockController();
        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $stockMagasins = $this->getDoctrine()->getRepository(StockMagasin::class)->findBy(["magasin"=>$magasin->getId()]);
        $products = [];
        // Récupérer l'URL de l'image pour chaque produit
        foreach ($stockMagasins as $stockMagasin) {
            $product = new Product($stockMagasin->getProduct()); 
           
            if (Validate::isLoadedObject($product)) {
                $products [] = [
                    'id_product' => $stockMagasin->getProduct(),
                    'image_url' => $gestionStockController->getImageUrl($stockMagasin->getProduct()),
                    'product_name' => $product->name[$defaultLanguageId],
                    'price' => $product->price,
                    'quantityTotal' => Product::getQuantity($stockMagasin->getProduct()),
                    'reference' => $product->reference,
                    'dateexpiration' => $stockMagasin->getDateexpiration(),
                    'stockMagasinId' => $stockMagasin->getId(),
                    'quantity' => $stockMagasin->getQuantite()
                ];
            }
        }

        $context = Context::getContext();

        // Récupérer l'objet représentant la devise active
        $currency = new Currency($context->currency->id);

        // Obtenez le symbole de la devise
        $currencySymbol = $currency->getSymbol();
        return $this->render(
            '@Modules/gestiondustock/src/Resources/magasins/edit_magasin.html.twig',
            array(
                'magasin' => $magasin,
                'currencySymbol' => $currencySymbol,
                'products' => $products,
                'layoutTitle' => 'Magasin '
            )
        );
    }

    public function deleteMagasins(Request $request, Magasin $magasin)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($magasin);
        $entityManager->flush();

        $this->addFlash('success', 'Le magasin a été supprimé avec succès !');
    
        return $this->redirectToRoute('gestiondustock_gestion_magasins');
    }

      

    
}