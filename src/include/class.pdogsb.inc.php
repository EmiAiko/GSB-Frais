<?php

/**
 * Classe d'accès aux données. 

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_mvc';
    private static $user = 'root';
    private static $mdp = 'EmiAikoBTSSLAM';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login 
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
     */
    public function getInfosVisiteur($login, $mdp) {
        $req = "select utilisateur.id as id, utilisateur.nom as nom, utilisateur.prenom as prenom, utilisateur.statut as statut from utilisateur 
		where utilisateur.login='$login' and utilisateur.mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur' 
		and lignefraishorsforfait.mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs 
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
		order by lignefraisforfait.idfraisforfait";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif 
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif 
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux 
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur 
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait (idVisiteur, mois, libelle, date, montant) 
		values('$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        echo $req;
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais 
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' 
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id 
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now() 
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Recupere les visiteursa vec le statut de visiteur (1)
     * 
     * @return type
     */
    function getVisiteurs() {
        $req = "select utilisateur.id as id, utilisateur.nom as nom, utilisateur.prenom as prenom from utilisateur where utilisateur.statut = 1";
        $rs = PdoGsb::$monPdo->query($req);
        return $rs->fetchAll();
    }

    /**
     * Recupere la date de validation 
     * 
     * @param type $id
     * @param type $date
     * @return type
     */
    function getDateValidation($id, $date) {
        $req = "select dateModif from fichefrais where idVisiteur='" . $id . "' and mois='" . $date . "'";
        $donnees = PdoGsb::$monPdo->query($req);
        return $donnees->fetch();
    }

    /**
     * Recupere les frais forfait pour la validation
     * 
     * @param type $id
     * @param type $mois
     * @return type
     */
    function getFraisForfaitPourValidation($id, $mois) {
        $req = "select idFraisForfait, quantite from lignefraisforfait where idVisiteur='" . $id . "' and mois='" . $mois . "'";
        $rs = PdoGsb::$monPdo->query($req);
        return $rs->fetchAll();
    }

    /**
     * Recupere les frais hors forfait pour la validation
     * 
     * @param type $id
     * @param type $mois
     * @return type
     */
    function getFraisHorsForfaitPourValidation($id, $mois) {
        $req = "select date, libelle, montant, id from lignefraishorsforfait where idVisiteur='" . $id . "' and mois='" . $mois . "'";
        $rs = PdoGsb::$monPdo->query($req);
        return $rs->fetchAll();
    }

    /**
     * Concatene 'REFUSE:' devant le libelle du frais hors forfait supprimer
     * 
     * @param type $id
     */
    function supprimerFraisHorsForfaitPourValidation($id) {
        $req = "select libelle from lignefraishorsforfait where id='" . $id . "'";
        $rs = PdoGsb::$monPdo->query($req);
        $estRefuse = preg_match('#^REFUSE:#', $rs->fetch()['libelle']) ? TRUE : FALSE;
        if (!$estRefuse) {
            $reqDeux = "update lignefraishorsforfait set libelle = concat('REFUSE: ',libelle) where id ='" . $id . "'";
            PdoGsb::$monPdo->query($reqDeux);
        }
    }

    /**
     * Ajoute le frais hors forfait au prochain mois
     * 
     * @param type $idFraisHorsForfais
     * @param type $moisFraisHorsForfais
     * @param type $idVisiteur
     */
    function modifierFraisHorsForfaitPourValidation($idFraisHorsForfais, $moisFraisHorsForfais, $idVisiteur) {
        $dateMois = substr($moisFraisHorsForfais, -2);
        $dateAnnee = substr($moisFraisHorsForfais, -0, 4);
        $dateDef = new DateTime($dateAnnee . '-' . $dateMois . '-01');
        $dateDef->modify('+1  month');
        $reqUne = "insert into fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif,idEtat) values ('" . $idVisiteur . "'," . $dateDef->format("Ym") . ",0,NULL,'" . $dateDef->format("Y-m") . "-01','CR')";
        PdoGsb::$monPdo->exec($reqUne);
        $reqDeux = "insert into lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite) values ('" . $idVisiteur . "'," . $dateDef->format("Ym") . ",'ETP',0)";
        PdoGsb::$monPdo->exec($reqDeux);
        $reqTrois = "insert into lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite) values ('" . $idVisiteur . "'," . $dateDef->format("Ym") . ",'KM',0)";
        PdoGsb::$monPdo->exec($reqTrois);
        $reqQuatre = "insert into lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite) values ('" . $idVisiteur . "'," . $dateDef->format("Ym") . ",'NUI',0)";
        PdoGsb::$monPdo->exec($reqQuatre);
        $reqCinq = "insert into lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite) values ('" . $idVisiteur . "'," . $dateDef->format("Ym") . ",'REP',0)";
        PdoGsb::$monPdo->exec($reqCinq);
        $reqSix = "update lignefraishorsforfait set mois = " . $dateDef->format("Ym") . " where id = '" . $idFraisHorsForfais . "'";
        PdoGsb::$monPdo->exec($reqSix);
    }

    /**
     * 
     * Modifie les frais forfait
     * 
     * @param type $id
     * @param type $date
     * @param type $etape
     * @param type $kilom
     * @param type $nuits
     * @param type $repas
     */
    function modifierFraisForfaitPourValidation($id, $date, $etape, $kilom, $nuits, $repas) {
        $reqUne = "update lignefraisforfait set quantite = '" . $etape . "' where mois = '" . $date . "' and idFraisForfait='ETP' and idVisiteur ='" . $id . "'";
        PdoGsb::$monPdo->exec($reqUne);
        $reqDeux = "update lignefraisforfait set quantite = '" . $kilom . "' where mois = '" . $date . "' and idFraisForfait='KM' and idVisiteur ='" . $id . "'";
        PdoGsb::$monPdo->exec($reqDeux);
        $reqTrois = "update lignefraisforfait set quantite = '" . $nuits . "' where mois = '" . $date . "' and idFraisForfait='NUI' and idVisiteur ='" . $id . "'";
        PdoGsb::$monPdo->exec($reqTrois);
        $reqQuatre = "update lignefraisforfait set quantite = '" . $repas . "' where mois = '" . $date . "' and idFraisForfait='REP' and idVisiteur ='" . $id . "'";
        PdoGsb::$monPdo->exec($reqQuatre);
    }

    /**
     * recupere le montant pour la validation
     * 
     * @param type $id
     * @param type $date
     * @return type
     */
    function montantPourValidation($id, $date) {
        $reqUne = "select sum(lignefraisforfait.quantite * fraisforfait.montant) as total from lignefraisforfait"
                . " join fraisforfait on fraisforfait.id = lignefraisforfait.idFraisForfait"
                . " where lignefraisforfait.idVisiteur='" . $id . "' and lignefraisforfait.mois=" . $date
                . " group BY lignefraisforfait.idFraisForfait";
        $rsUne = PdoGsb::$monPdo->query($reqUne);
        $rpUne = $rsUne->fetchAll();
        $reqDeux = "select sum(montant) as total from lignefraishorsforfait"
                . " where libelle not regexp '^(REFUSE: )' and idVisiteur='" . $id . "' and mois=" . $date;
        $rsDeux = PdoGsb::$monPdo->query($reqDeux);
        $rpDeux = $rsDeux->fetch();
        $total = $rpDeux['total'];

        foreach ($rpUne as $uneSomme) {
            $total += $uneSomme['total'];
        }
        return $total;
    }

    /**
     * Valide la fiche de frais
     * 
     * @param type $id
     * @param type $date
     * @param type $montant
     */
    function valildationFiche($id, $date, $montant) {
        $reqDeux = "select count(*) as nbJusti from lignefraishorsforfait"
                . " where libelle not regexp '^(REFUSE:)' and idVisiteur='" . $id . "' and mois=" . $date;
        $rsDeux = PdoGsb::$monPdo->query($reqDeux);
        $rpDeux = $rsDeux->fetch()['nbJusti'];
        $dateModif = new DateTime();
        $reqUne = "update fichefrais set idEtat = 'VA',"
                . " montantValide ='" . $montant . "',"
                . " nbJustificatifs='" . $rpDeux . "',"
                . " dateModif ='" . $dateModif->format('Y-m-d') . "'"
                . " where mois='" . $date . "' and idVisiteur ='" . $id . "'";
        PdoGsb::$monPdo->exec($reqUne);
    }

    /**
     * 
     * retourne les visiteurs ayant des fiches frais validées
     * 
     * @return type
     */
    function getVisiteurMoisSuiviFiche() {
        $reqUne = "select nom, prenom, id, mois, montantValide, dateModif from utilisateur join fichefrais on id=idVisiteur where idEtat='VA'";
        $rsUne = PdoGsb::$monPdo->query($reqUne);
        $rpUne = $rsUne->fetchAll();
        return $rpUne;
    }

    /**
     * 
     * valide definitivement la fiche;
     * 
     * @param type $id
     * @param type $date
     */
    function validerSuivi($id, $date) {
        $dateModif = date('Y-m-d');
        $reqUne = "update fichefrais set idEtat = 'RB', dateModif='" . $dateModif . "' where mois='" . $date . "' and idVisiteur ='" . $id . "'";
        PdoGsb::$monPdo->exec($reqUne);
    }

    /**
     * recupere montant valide
     * 
     * @param type $id
     * @param type $date
     * @return type
     */
    function getMontantValideSuivi($id, $date) {
        $reqUne = "select montantValide from fichefrais where idVisiteur='" . $id . "' and mois=" . $date;
        $rsUne = PdoGsb::$monPdo->query($reqUne);
        $rpUne = $rsUne->fetch();
        return $rpUne[0];
    }

}

?>
