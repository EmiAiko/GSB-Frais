
<h3>Fiche de frais du mois <?php echo $numMois."-".$numAnnee?> : 
    </h3>
    <div class="encadre">
    <p>
        Etat : <?php echo $libEtat?> depuis le <?php echo $dateModif?> <br> Montant validé : <?php echo $montantValide?>
              
                     
    </p>
	
  	<table class="listeLegere">
  	   <caption>Eléments forfaitisés </caption>
        <!--<tr>
         <?php
         foreach ( $lesFraisForfait as $unFraisForfait ) 
		 {
			$libelle = $unFraisForfait['libelle'];
		?>	
			<th> <?php echo $libelle?></th>
		 <?php
        }
		?>
		</tr>
        <tr>
        <?php
          foreach (  $lesFraisForfait as $unFraisForfait  ) 
		  {
				$quantite = $unFraisForfait['quantite'];
		?>
                <td class="qteForfait"><?php echo $quantite?> </td>
		 <?php
          }
		?>-->
		
		<tr>
			<th>Type Forfait</th>
			<th>Quantité</th>
			<th>Montant unitaire</th>
			<th>Total</th>
		</tr>
	
			<!-- Ajout des lignes des forfait-->
			<?php
			$totaux = 0;
			foreach (  $lesFraisForfait as $unFraisForfait  ) 
			{
				
				$libelle = $unFraisForfait["libelle"];
				$quantite = $unFraisForfait["quantite"];
				$montant = $unFraisForfait["montant"];
				$total = $quantite*$montant;
				$totaux += $total;
				?>
				<th><?php echo $libelle;?></th>
				<td><?php echo $quantite;?></td>
				<td><?php echo $montant;?>€</td>
				<td><?php echo $total;?>€</td>
				
		</tr>
			<?php
			}
			?>
			
			
    </table>
	
	<h3>Total : <?php echo $totaux  ?>€
	</h3>
	
	
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait -<?php echo $nbJustificatifs ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>  
				<th class='statut'>Statut</th>
             </tr>
        <?php      
          foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
		  {
			$date = $unFraisHorsForfait['date'];
			$libelle = $unFraisHorsForfait['libelle'];
			$montant = $unFraisHorsForfait['montant'];
			$statut = $unFraisHorsForfait['libellestatut'];
		?>
             <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
				<td><?php echo $statut ?></td>
             </tr>
        <?php 
          }
		?>
    </table>
  </div>
  </div>
 













