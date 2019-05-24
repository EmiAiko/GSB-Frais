    <!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">
        <?php foreach (getSommaire($_SESSION['statut']) as $ligne): ?>
            <?php echo $ligne; ?>
        <?php endforeach; ?>
    </div>  
</div>
