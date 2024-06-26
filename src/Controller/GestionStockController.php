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
use PrestaShopBundle\Entity\OrdersMagasin;
use StockAvailable;
use Validate;
use PriceFormatter;
use Currency;
use Db;
use Configuration;
use Context; 
use Image;
use ImageType;
use Product;

class GestionStockController extends FrameworkBundleAdminController
{
    
    public function gestionStocks(Request $request)
    {
        // Récupérer l'ID de la langue par défaut
        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        // Récupérer la page courante depuis la requête
        $currentPage = (int) $request->query->get('page', 0);
        $search = $request->request->get('search', "");
        $itemsPerPage = 20; // Nombre d'éléments par page
        // Construire la requête SQL pour récupérer tous les produits avec le préfixe de table et la langue par défaut
        $sql = "SELECT p.id_product, p.reference, pl.name AS product_name, p.price, i.id_image, s.quantity
            FROM " . _DB_PREFIX_ . "product p
            JOIN " . _DB_PREFIX_ . "product_lang pl ON p.id_product = pl.id_product
            LEFT JOIN " . _DB_PREFIX_ . "image i ON p.id_product = i.id_product
            LEFT JOIN " . _DB_PREFIX_ . "stock_available s ON p.id_product = s.id_product
            WHERE pl.id_lang = " . $defaultLanguageId." AND p.active=1";
        if(isset($search) && !empty($search)){
            $sql .= " AND (pl.name LIKE '%".$search."%' OR p.reference LIKE '%".$search."%') ";
        }

        $offset = 0;
        if($currentPage>0){
            $offset = $currentPage - 1;
        }
        $sql .= " LIMIT " .$itemsPerPage." OFFSET " .$offset;

        // Exécuter la requête SQL avec la classe Db de PrestaShop
        $products = Db::getInstance()->executeS($sql);

        // Récupérer l'URL de l'image pour chaque produit
        foreach ($products as &$product) {
            if ($product['id_image']) {
                $imageId = $product['id_product'];
                $imageUrl = $this->getImageUrl($imageId);
                $product['image_url'] = $imageUrl;
            } else {
                // Si l'image n'existe pas, définissez une URL par défaut ou laissez vide
                $product['image_url'] = ''; // Définissez une URL par défaut ici si nécessaire
            }
        }

        $context = Context::getContext();

        // Récupérer l'objet représentant la devise active
        $currency = new Currency($context->currency->id);

        // Obtenez le symbole de la devise
        $currencySymbol = $currency->getSymbol();

        $sql = "SELECT COUNT(*) as sizep
            FROM " . _DB_PREFIX_ . "product p
            JOIN " . _DB_PREFIX_ . "product_lang pl ON p.id_product = pl.id_product
            WHERE pl.id_lang = " . $defaultLanguageId." AND p.active=1";
        if(isset($search) && !empty($search)){
            $sql .= " AND (pl.name LIKE '%".$search."%' OR p.reference LIKE '%".$search."%') ";
        }
        // Exécuter la requête SQL avec la classe Db de PrestaShop
        $size_products = Db::getInstance()->executeS($sql);
        $paginator = 0;
        if(isset($size_products[0]["sizep"]) && $size_products[0]["sizep"]>$itemsPerPage){
            $paginator = $quotient = intdiv( $size_products[0]["sizep"], $itemsPerPage);
            if( ($size_products[0]["sizep"] % $itemsPerPage) !=0)$paginator++;
        }
        $pages = [];
        for ($p=0; $p < $paginator ; $p++) { 
            $pages [] = $p;
        }
        return $this->render(
            '@Modules/gestiondustock/src/Resources/gestionstock/all_products.html.twig',
            array(
                'currencySymbol' => $currencySymbol,
                'products' => $products,
                'currentPage' => $currentPage,
                'paginator' => $paginator,
                'pages' => $pages,
                'search' => $search,
                'layoutTitle' => 'Toute Les Products Du LEGY'
            )
        );
    }

    public function editStocks(Request $request , $productid)
    {
        $product = new Product($productid);
        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
 
        if($product && isset($product->id) && $product->id == $productid){
            $magasins = $this->getDoctrine()->getRepository(Magasin::class)->findAll();
            $productImageUrl = $this->getImageUrl($productid);
            $stockMagasins = [];
            foreach ($magasins as $key => $magasin) {
                $stockMagasins [$magasin->getId()] = $this->getDoctrine()->getRepository(StockMagasin::class)->findOneBy(['product'=>$productid , 'magasin'=>$magasin->getId()]);
            }
            $stockProduct = Product::getQuantity($productid);
            return $this->render(
                '@Modules/gestiondustock/src/Resources/gestionstock/edit_magasins.html.twig',
                array(
                    'defaultLanguageId' => $defaultLanguageId,
                    'productImageUrl' => $productImageUrl,
                    'magasins' => $magasins,
                    'product' => $product,
                    'stockMagasins' => $stockMagasins,
                    'stockProduct' => $stockProduct,
                    'layoutTitle' => 'Gestion Du Stock LEGY'
                )
            );
        }else{
            $this->addFlash('danger', 'Le Product n\'existe pas !');
            return $this->redirectToRoute('gestiondustock_gestion_stock_magasins');
        }
        
    }

    public function updateOrdersAjax(Request $request)
    {
        $content = urldecode($request->getContent());
        // Initialiser un tableau vide pour stocker les données
        $data = [];
        // Décoder les données de la requête dans un tableau associatif
        parse_str($content, $data);
        if(
            isset($data["product"]) &&
            isset($data["magasin"]) &&
            isset($data["order"]) &&
            isset($data["quantity"])
        ){
            $updateOrders = true;
            $entityManager = $this->getDoctrine()->getManager();

            // Récupérer ou créer une nouvelle instance de OrdersMagasin
            $ordersMagasin = $this->getDoctrine()->getRepository(OrdersMagasin::class)->findOneBy(["orderId" => $data["order"], "productId" => $data["product"]]);

            if (!$ordersMagasin) {
                $ordersMagasin = new OrdersMagasin();
                $updateOrders = false;
            }

            // Mettre à jour les propriétés de OrdersMagasin
            $ordersMagasin->setOrderId($data["order"]);
            $ordersMagasin->setProductId($data["product"]);
            $ordersMagasin->setQuantite($data["quantity"]);
            $ordersMagasin->setMagasinId($data["magasin"]);

            // Persiste l'entité si ce n'est pas déjà fait
            if (!$updateOrders) {
                $ordersMagasin->setStatus(0);
                $entityManager->persist($ordersMagasin);
            }

            // Flush pour effectuer les modifications dans la base de données
            $entityManager->flush();
            $response = [
                'status' => 'success',
                'message' => 'Le Commande a été mis à jour avec succès.',
            ];
        }else{
            $response = [
                'status' => 'erreur',
                'message' => 'Data Not Fund!.',
            ];
        }

        // Retourner une réponse JSON
        return new JsonResponse($response);
    }


    public function updateStocksAjax(Request $request)
    {

        
        // Récupérer des données de la requête AJAX (si nécessaire)
        $data = json_decode($request->getContent(), true);
        $productid = $data["productid"];
        if($data["stockId"] != 0 ){
            $stockMagasin = $this->getDoctrine()->getRepository(StockMagasin::class)->find($data["stockId"]);
        }else{
            $stockMagasin =    $this->getDoctrine()
                                    ->getRepository(StockMagasin::class)
                                    ->findOneBy([
                                        "product"=>$productid,
                                        "magasin"=>$data["magasin"]
                                    ]);
            if(!$stockMagasin){
                $stockMagasin = new StockMagasin();
            }
        }
        
        $stockMagasin->setProduct($productid);
        $stockMagasin->setMagasin($data["magasin"]);
        $stockMagasin->setQuantite($data["quantite"]);
        $stockMagasin->setDateexpiration($data["date_expiration"]);
        $entityManager = $this->getDoctrine()->getManager();
        if($data["stockId"] == 0 ){
            $entityManager->persist($stockMagasin);
        }
        $entityManager->flush();

        $stockProduct = $this->updateStockByProduct($productid);
        // Traiter les données et préparer la réponse
        $response = [
            'status' => 'success',
            'message' => 'Le stock a été mis à jour avec succès.',
            'stockProduct' => $stockProduct,
            'product' => $stockMagasin->getProduct(),
            'magasin' => $stockMagasin->getMagasin(),
            'stock' =>  $stockMagasin->getId()
        ];

        // Retourner une réponse JSON
        return new JsonResponse($response);
    }

    private function updateStockByProduct($id_product)
    {
        // Récupérer les informations sur le produit
        $product = new Product($id_product);

        $newStockQuantity = $product->quantity;
        $stockMagasins = $this->getDoctrine()->getRepository(StockMagasin::class)->findBy(["product"=>$id_product]);
        if (sizeof($stockMagasins)>0) {
            $newStockQuantity = 0;
            foreach ($stockMagasins as $key => $stock) {
                $newStockQuantity += $stock->getQuantite();
            }
        }
        // Mettre à jour la quantité de stock du produit
        $this->updateProductStockById($id_product,$newStockQuantity);

        return Product::getQuantity($id_product);
    }

    public function updateProductStockById($productId, $newQuantity)
    {
        $product = new Product($productId);
        if (Validate::isLoadedObject($product)) {
            // Mettre à jour la quantité dans la table ps_product
            $product->quantity = $newQuantity;
            $product->update();

            // Mettre à jour la quantité dans la table ps_stock_available
            StockAvailable::setQuantity($productId, null, $newQuantity);

            return true;
        } else {
            return false;
        }
    }

    // Fonction pour récupérer l'URL de l'image à partir de l'ID de l'image
    public function getImageUrl($id_product)
    {
        // Récupérer les informations sur le produit
        $product = new Product($id_product);
        if (Validate::isLoadedObject($product)) {
            $imageID = $product->getCoverWs();
            $image = new Image($imageID);
            $imagePath = $image->getExistingImgPath();
            if($imagePath!=""){
                $imageUrl = Context::getContext()->link->getImageLink($imageID, null);
                $imageUrl = str_replace("/".$imageID.".","img/p/".$imagePath.".",$imageUrl);
            }
            return $imageUrl;
        }else{
            return "";
        }
    }
}