<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Modifier le statut de la commande</h2>
        <form id="orderStatusForm" action="#" method="post">
            <input type="hidden" name="orderId" value="{$orderId}">
            <input type="hidden" name="newStatus" value="{$newStatus}">
            <label for="comment">Commentaire :</label>
            <textarea id="comment" name="comment" rows="4" cols="50"></textarea>
            <br>
            <button type="submit">Soumettre</button>
        </form>
    </div>
</div>