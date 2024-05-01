<?php
namespace GestionDuStock\Controller;

use DateTime;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\PhpExecutableFinder;


class GestionStockController extends FrameworkBundleAdminController
{
    public function editAction(Request $request)
    {
        return $this->render(
            '@Modules/gestiondustock/src/Resources/gestionstock/all_magasins.html.twig',
            array(
                'layoutTitle' => 'Toute Les Magasins Du LEGY'
            )
        );
    }
}