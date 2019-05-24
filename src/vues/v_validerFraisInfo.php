<fieldset>
    <legend>
        <b>Fiche de frais du mois <?php echo $moisComplet; ?></b>
    </legend>
    <p>Etat: Saisie Cloturée depuis le <?php echo $date; ?>
        <br>Montant validé: <b><?php echo $montantValide; ?> €</b></p>
    <form action="index.php?uc=validerFrais&action=modifierFraisForfait" method="post">
        <table>
            <tr>
                <th class="tab_fond">Forfait Etape</th>
                <th class="tab_fond">Frais Kilométrique</th>
                <th class="tab_fond">Nuitée Hôtel</th>
                <th class="tab_fond">Repas Restaurant</th>
            </tr>
            <tr>
                <?php foreach ($fraisForfait as $unFrais): ?>
                    <td><input class="input-mask" type="text" name="<?php echo $unFrais['idFraisForfait'] ?>" value="<?php echo $unFrais['quantite'] ?>"/></td>
                <?php endforeach; ?>
            </tr>
        </table>
        <br>
        <input type="hidden" name="id" value="<?php echo $postId; ?>" />
        <input type="hidden" name="mois" value="<?php echo $postMois; ?>" />
        <input class="boutton_jolie btn_valider" type="reset" value="Effacer" />
        <button class="boutton_jolie btn_valider" type="submit">Valider</button>
    </form>
    <p>Descriptif des éléments hors forfait: <?php echo $nbJustificatifs; ?> reçu(s)</p>
    <table>
        <tr>
            <th>Date</th>
            <th>Libellé</th>
            <th>Montant</th>
            <th colspan="2">Action</th>
        </tr>
        <?php foreach ($fraisHorsForfait as $unFrais): ?>
            <tr class="<?php echo explode(':', $unFrais['libelle'])[0] == 'REFUSE' ? 'bgrouge' : '' ?>">
                <td><?php echo $unFrais['date']; ?></td>
                <td><?php echo $unFrais['libelle']; ?></td>
                <td><?php echo $unFrais['montant']; ?></td>
                <td>
                    <form action="index.php?uc=validerFrais&action=modifierFraisHorsForfait" method="post">
                        <input type="hidden" name="id" value="<?php echo $postId; ?>" />
                        <input type="hidden" name="mois" value="<?php echo $postMois; ?>" />
                        <button class="boutton_jolie" type="submit" name="modifierFraisHorsForfait" value="<?php echo $unFrais['id']; ?>">Reporter</button>
                    </form>
                </td>
                <td>
                    <form action="index.php?uc=validerFrais&action=supprimerFraisHorsForfait" method="post">
                        <input type="hidden" name="id" value="<?php echo $postId; ?>" />
                        <input type="hidden" name="mois" value="<?php echo $postMois; ?>" />
                        <button class="boutton_jolie" type="submit" name="supprimerFraisHorsForfait" value="<?php echo $unFrais['id']; ?>">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <form action="index.php?uc=validerFrais&action=validerFrais" method="post">
        <input type="hidden" name="id" value="<?php echo $postId; ?>" />
        <input type="hidden" name="mois" value="<?php echo $postMois; ?>" />
        <input type="hidden" name="montant" value="<?php echo $montantValide; ?>" />
        <button class="boutton_jolie btn_valider" style="width: 200px;" type="submit">Valider la Fiche</button>
    </form>
</fieldset>
</div>