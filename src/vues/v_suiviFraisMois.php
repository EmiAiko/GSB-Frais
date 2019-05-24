<div id="contenu">
    <fieldset class="valider">
        <legend><b>Visiteur et Mois à selectionner</b></legend>
        <form action="index.php?uc=suiviFrais&action=getInfos" method="post">
            <span>Visiteur:</span>
            <select name="idMois">
                <option value="default">Selectionner Visiteur</option>
                <?php foreach ($visiteurMois as $tabVisi): ?>
                    <option value="<?php echo $tabVisi['id'] . "_" . $tabVisi['mois']; ?>"><?php echo substr($tabVisi['mois'], 4) . '/' . substr($tabVisi['mois'], 0, 4) . " - " . $tabVisi['nom'] . " " . $tabVisi['prenom'] ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button class="boutton_jolie btn_valider" type="submit">Valider</button>
        </form>
    </fieldset>