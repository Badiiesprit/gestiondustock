<?php

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class AdminGestionDuStockMagasinsController extends ModuleAdminController
{
    public function init()
    {
        $sfContainer = SymfonyContainer::getInstance();
        if (!is_null($sfContainer)) {
            $sfRouter = $sfContainer->get('router');
            Tools::redirectAdmin($sfRouter->generate('gestiondustock_gestion_stock_magasins'));
        }

        return parent::init();
    }

}
