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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/




// Script JavaScript pour afficher le pop-up avec le formulaire
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le pop-up modal
    var modal = document.getElementById('myModal');

    // Récupérer le bouton pour fermer le pop-up
    var closeButton = document.getElementsByClassName('close')[0];

    // Afficher le pop-up modal lorsque le hook est déclenché
    modal.style.display = 'block';

    // Fermer le pop-up lorsqu'on clique sur le bouton de fermeture
    closeButton.onclick = function() {
        modal.style.display = 'none';
    };

    // Soumettre le formulaire lorsqu'on clique sur le bouton Soumettre
    var form = document.getElementById('orderStatusForm');
    form.onsubmit = function(event) {
        event.preventDefault(); // Empêcher l'envoi du formulaire par défaut

        // Ajouter ici la logique pour traiter le formulaire
        // Par exemple, envoyer une requête Ajax pour traiter les données du formulaire
        // Vous pouvez utiliser XMLHttpRequest ou une bibliothèque JavaScript comme Axios
        // pour envoyer les données du formulaire à votre backend PHP
        console.log('Formulaire soumis !');
        // Fermer le pop-up après la soumission du formulaire
        modal.style.display = 'none';
    };
});

