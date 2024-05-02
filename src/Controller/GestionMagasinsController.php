<?php
namespace GestionDuStock\Controller;

use DateTime;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\PhpExecutableFinder;
use PrestaShopBundle\Entity\Magasin;
use GestionDuStock\Form\MagasinType;

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
        return $this->render(
            '@Modules/gestiondustock/src/Resources/magasins/edit_magasins.html.twig',
            array(
                'magasin' => $magasin,
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