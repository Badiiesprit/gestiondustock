
<!-- Script jQuery pour détecter le changement d'option -->
{if $disabled}
        {foreach from=$magasinOptions item=magasin}
                {if $magasin.selected}
                        <p>{$magasin.name}</p>
                {/if}
        {/foreach}
{else}
<script>
$(document).ready(function() {
        // Sélectionnez l'élément <select> par son ID
        $('#magasin_{$productId}').change(function() {
                // Récupérer la valeur de l'option sélectionnée
                var selectedMagasin = $(this).val();

                // Récupérer l'attribut data-order de l'option sélectionnée
                var selectedOrder = $(this).find('option:selected').data('order');
                var selectedQuantity = $(this).find('option:selected').data('quantity');
                var selectedProduct = $(this).find('option:selected').data('product');

                // Afficher la valeur et l'ordre dans la console (à des fins de débogage)
                console.log('Valeur sélectionnée:', selectedMagasin);
                console.log('data-order:', selectedOrder);
                console.log('data-quantity:', selectedQuantity);
                console.log('data-product:', selectedProduct);
                var postData = {
                        magasin: selectedMagasin,
                        order: selectedOrder,
                        product: selectedProduct,
                        quantity: selectedQuantity
                };

                $.ajax({
                        url: '{$urlAjax}',
                        method: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function(response) {
                                console.log('Réponse reçue :', response);

                                // Manipulation des données reçues (mise à jour du DOM, etc.)
                        },
                        error: function(xhr, status, error) {
                                console.error('Erreur AJAX :', error);
                        }
                });
        });
});
</script>
<select name="magasin" id="magasin_{$productId}" class="form-control">
        <option value="" data-order="0">Sélectionner le Magasin</option>
        {foreach from=$magasinOptions item=magasin}
                <option
                        value="{$magasin.id}"
                        data-order="{$magasin.orderId}"
                        data-quantity="{$magasin.quantity}"
                        data-product="{$productId}"
                        {if $magasin.selected }
                        selected
                        {/if}
                >{$magasin.name}({$magasin.stock})</option>
        {/foreach}
</select>
{/if}