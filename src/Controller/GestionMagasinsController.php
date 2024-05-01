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

    public function addMagasins(Request $request)
    {

        $magasinForm = $this->createForm(MagasinType::class);

        $magasinForm->handleRequest($request);
        if ($magasinForm->isSubmitted() && $magasinForm->isValid()) {
            // Récupérer les données soumises du formulaire
            $data = $magasinForm->getData();

            // Créer une nouvelle instance de l'entité Magasin
            $magasin = new Magasin();
            
            // Définir les propriétés de l'entité avec les données du formulaire
            $magasin->setNom($data['nom']); // Exemple avec un champ 'nom' dans le formulaire
            
            // Enregistrer l'entité Magasin en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($magasin);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le magasin a été créé avec succès !');

            // Redirection vers une autre page après traitement du formulaire
            return $this->redirectToRoute('gestiondustock_gestion_magasins');
        }
        return $this->render(
            '@Modules/gestiondustock/src/Resources/magasins/add_magasins.html.twig',
            array(
                'magasinForm' => $magasinForm->createView(),
                'layoutTitle' => 'Ajouter Magasin'
            )
        );
    }

    
}