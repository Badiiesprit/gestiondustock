<?php

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class AdminGestionDuStockController extends ModuleAdminController
{
    public function init()
    {
        $sfContainer = SymfonyContainer::getInstance();
        if (!is_null($sfContainer)) {
            $sfRouter = $sfContainer->get('router');
            Tools::redirectAdmin($sfRouter->generate('gestiondustock_gestion_magasins'));
        }
        return parent::init();
    }

}