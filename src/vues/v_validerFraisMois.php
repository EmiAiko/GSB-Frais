<div id="contenu">
    <?php if (isset($_REQUEST['modif'])): ?>
        <p class="maj">Informations Mises à Jour</p>
    <?php endif; ?>
    <h2>Validation des fiches de frais</h2>
    <form method="post" action="index.php?uc=validerFrais&action=getInfos">
        <fieldset class="valider">
            <legend><b>Visiteur et Mois à selectionner</b></legend>
            <span>Visiteur:</span>
            <select name="id">
                <option value="default_visiteur">Selectionner Visiteur</option>
                <?php foreach ($visiteurs as $unVisi): ?>
                    <?php echo $unVisi; ?>
                <?php endforeach; ?>
            </select><br>
            <span class="valider-mois">Mois:</span>
            <select class="valider-marge-select" name="mois">
                <option value="default_mois">Selectionner Mois</option>
                <?php foreach ($mois as $unMois): ?>
                    <?php echo $unMois; ?>
                <?php endforeach; ?>
            </select><br>
            <input class="boutton_jolie btn_valider" type="reset" value="Effacer" />
            <input class="boutton_jolie btn_valider" type="submit" value="Valider"/>
        </fieldset>
    </form>
