<fieldset>
    <legend>
        <b>Fiche de frais du mois <?php echo $moisComplet; ?></b>
    </legend>
    <p>Etat: Validée et mise en paiement depuis le: <?php echo $date; ?>
        <br>Montant validé: <b><?php echo $montantValide; ?> €</b></p>
    <table>
        <tr>
            <th class="tab_fond">Forfait Etape</th>
            <th class="tab_fond">Frais Kilométrique</th>
            <th class="tab_fond">Nuitée Hôtel</th>
            <th class="tab_fond">Repas Restaurant</th>
        </tr>
        <tr>
            <?php foreach ($fraisForfait as $unFrais): ?>
                <td><?php echo $unFrais['quantite'] ?></td>
            <?php endforeach; ?>
        </tr>
    </table>
    <p>Descriptif des éléments hors forfait: <?php echo $nbJustificatifs; ?> reçu(s)</p>
    <table>
        <tr>
            <th>Date</th>
            <th>Libellé</th>
            <th>Montant</th>
        </tr>
        <?php foreach ($fraisHorsForfait as $unFrais): ?>
            <tr style="<?php echo explode(':', $unFrais['libelle'])[0] == 'REFUSE' ? 'display: none;' : '' ?>">
                <td><?php echo $unFrais['date']; ?></td>
                <td><?php echo $unFrais['libelle']; ?></td>
                <td><?php echo $unFrais['montant']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <form action="index.php?uc=suiviFrais&action=validationSuivi" method="post">
        <input type="hidden" name="id" value="<?php echo $postId; ?>" />
        <input type="hidden" name="mois" value="<?php echo $postMois; ?>" />
        <button class="boutton_jolie btn_valider valider-marge-select" type="submit">Valider la Fiche</button>
    </form>
</fieldset>
</div>