<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/




if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
use PrestaShopBundle\Entity\Magasin;
use PrestaShopBundle\Entity\StockMagasin;
use Doctrine\ORM\EntityManager;

class GestionDuStock extends Module
{
    public static $kernel = null;
    public static $doctrine = null;
    private $tab_class = 'AdminGestionDuStockParent';
    private $admin_tab_classes = [
        'AdminGestionDuStock' => 'Gestion Du Stock LEGY',
        'AdminGestionDuStockMagasins' => 'Magasins LEGY'
    ];
    
    public function __construct()
    {
        
        $this->name = 'gestiondustock';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'BADII Abdelkhalak';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Gestion du stock par magasins.');
        $this->description = $this->l('Gestion du stock par magasins.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '8.0');
    }

    /**
     * Don't forget to create update methods if needed:
     */
    public function install()
    {
        $entity_manager = $this->get('doctrine.orm.entity_manager');

        if (!parent::install()
            //|| !$entity_manager->getRepository(Magasin::class)->createTable()
            //|| !$entity_manager->getRepository(StockMagasin::class)->createTable()
            || !$this->registerHook('header')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionOrderStatusPostUpdate')
        ) {
            return false;
        }
        /** @var \PrestaShopBundle\Entity\Repository\TabRepository $tabRepository */
        $tabRepository = self::getDoctrine()->getRepository('PrestaShopBundle:Tab');
        if (empty($tabRepository->findOneIdByClassName($this->tab_class))) {
             if (!$this->addTabs($this->tab_class, 0)) {
                 return false;
             }
        }
        return true;
    }

    public function uninstall()
    {
        $entity_manager = $this->get('doctrine.orm.entity_manager');

        if (//!$entity_manager->getRepository(Magasin::class)->dropTable() ||
            //!$entity_manager->getRepository(StockMagasin::class)->dropTable() ||
            !$this->removeTabs($this->tab_class) ||
            !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    public function hookActionOrderStatusPostUpdate($params)
    {        
        // Récupérer l'ID de la commande
        $orderId = (int) $params['id_order'];
        $newStatus = (int) $params['newOrderStatus']->id;

        // Charger la commande à partir de son ID
        $order = new Order($orderId);

        // Vérifier si la commande est chargée avec succès
        if (Validate::isLoadedObject($order)) {
            // Récupérer les produits de la commande avec leurs quantités
            $products = $order->getProducts();

            // Parcourir la liste des produits de la commande
            foreach ($products as $product) {
                $productId = (int) $product['product_id'];
                $productName = $product['product_name'];
                $productQuantity = (int) $product['product_quantity'];

                // Faites ce que vous voulez avec les informations du produit
                // Par exemple, enregistrer dans un fichier de journal
                $this->logProductInfo($orderId, $productId, $productName, $productQuantity);
            }
        }

        // Ajouter un script JavaScript pour afficher le pop-up avec le formulaire
        $this->context->smarty->assign(array(
            'orderId' => $orderId,
            'newStatus' => $newStatus,
        ));

        error_log($this->display(__FILE__, 'views/templates/admin/modal_form.tpl'), 3, _PS_ROOT_DIR_ . '/var/logs/order_product_info.log');
        return $this->display(__FILE__, 'views/templates/admin/modal_form.tpl');

    }
    
//     public function hookActionOrderStatusPostUpdate($params)
// {
//     $orderId = (int) $params['id_order'];
//     $newStatus = (int) $params['newOrderStatus']->id;

//     // Enregistrer les données de la commande dans les variables JavaScript
//     $this->context->smarty->assign(array(
//         'orderId' => $orderId,
//         'newStatus' => $newStatus,
//     ));
// }

    private function logProductInfo($orderId, $productId, $productName, $productQuantity)
    {
        $logMessage = sprintf(
            'Order ID %d - Product ID %d (%s) - Quantity: %d',
            $orderId,
            $productId,
            $productName,
            $productQuantity
        );

        // Exemple : enregistrer le message dans un fichier de journal
        error_log($logMessage, 3, _PS_ROOT_DIR_ . '/var/logs/order_product_info.log');
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }


    private function addTabs($tab_class, $id_parent)
    {
        $parentTab = new Tab();
        $parentTab->class_name = $tab_class;
        $parentTab->id_parent = $id_parent;
        $parentTab->module = $this->name;
        $parentTab->position = 1;
        $parentTab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Gestion Du Stock LEGY');
        $parentTab->add();

        /** @var \PrestaShopBundle\Entity\Repository\TabRepository $tabRepository */
        $tabRepository = self::getDoctrine()->getRepository('PrestaShopBundle:Tab');
        $id_parent = $tabRepository->findOneIdByClassName($tab_class);

        foreach ($this->admin_tab_classes as $tabClass => $tabName) {
            $subTab = new Tab();
            $subTab->class_name = $tabClass;
            $subTab->id_parent = $id_parent;
            $subTab->module = $this->name;
            $subTab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l($tabName);
            $subTab->add();
        }
        return true;
    }

    private function removeTabs($tab_class)
    {
        /** @var \PrestaShopBundle\Entity\Repository\TabRepository $tabRepository */
        $tabRepository = self::getDoctrine()->getRepository('PrestaShopBundle:Tab');

        $idTab = $tabRepository->findOneIdByClassName($tab_class);
        if (!empty($idTab)) {
            try {
                $tab = new Tab($idTab);
                $tab->delete();
            } catch (Exception $e) {

            }
        }

        foreach ($this->admin_tab_classes as $tabClass => $tabName) {
            $idTab = $tabRepository->findOneIdByClassName($tabClass);
            if (!empty($idTab)) {
                try {
                    $tab = new Tab($idTab);
                    $tab->delete();
                } catch (Exception $e) {

                }
            }
        }
        return true;
    }

    public static function getKernel()
    {
        if (!self::$kernel) {
            global $kernel;
            if ($kernel) {
                self::$kernel = $kernel;
            } else {
                require_once _PS_ROOT_DIR_ . '/app/AppKernel.php';
                self::$kernel = new \AppKernel(_PS_MODE_DEV_ ? 'dev' : 'prod', _PS_MODE_DEV_);
                self::$kernel->boot();
            }
        }
        return self::$kernel;
    }

    public static function getDoctrine()
    {
        if (!self::$doctrine) {
            self::$doctrine = self::getKernel()->getContainer()->get('doctrine')->getManager();
        }
        return self::$doctrine;
    }

    public static function getService($service)
    {
        return self::getKernel()->getContainer()->get($service);
    }
}
