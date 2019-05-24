<?php

require_once 'vues/v_sommaire.php';

$visiteurs = setVisiteurs($pdo->getVisiteurs());
$mois = getSixDerMois();
switch ($_REQUEST['action']) {
    case 'selectionnerMois':
        require_once 'vues/v_validerFraisMois.php';
        break;
    case 'getInfos':
        require_once 'vues/v_validerFraisMois.php';
        $postId = $_REQUEST['id'];
        $postMois = $_REQUEST['mois'];
        if ($postId == 'default_visiteur' && $postMois == 'default_mois') {
            echo 'Aucune Fiche';
        } else {
            $date = dateAnglaisVersFrancais($pdo->getDateValidation($postId, $postMois)['dateModif']);
            $montantValide = $pdo->montantPourValidation($postId, $postMois);
            $moisComplet = getMoisAvecNumMois(substr($postMois, -2)) . ' ' . substr($postMois, -0, 4);
            $fraisForfait = $pdo->getFraisForfaitPourValidation($postId, $postMois);
            $fraisHorsForfait = $pdo->getFraisHorsForfaitPourValidation($postId, $postMois);
            $nbJustificatifs = count($fraisHorsForfait);
            require_once 'vues/v_validerFraisInfo.php';
        }
        break;

    case 'modifierFraisForfait':
        $pdo->modifierFraisForfaitPourValidation($_REQUEST['id'], $_REQUEST['mois'], $_REQUEST['ETP'], $_REQUEST['KM'], $_REQUEST['NUI'], $_REQUEST['REP']);
        header('Location: index.php?uc=validerFrais&action=getInfos&id=' . $_REQUEST['id'] . '&mois=' . $_REQUEST['mois'] . '&modif=1');
        break;

    case 'validerFrais':
        $pdo->valildationFiche($_REQUEST['id'], $_REQUEST['mois'], $_REQUEST['montant']);
        header('Location: index.php?uc=validerFrais&action=selectionnerMois&modif=1');
        break;

    case 'supprimerFraisHorsForfait':
        $pdo->supprimerFraisHorsForfaitPourValidation($_REQUEST['supprimerFraisHorsForfait']);
        header('Location: index.php?uc=validerFrais&action=getInfos&id=' . $_REQUEST['id'] . '&mois=' . $_REQUEST['mois'] . '&modif=1');
        break;

    case 'modifierFraisHorsForfait':
        $pdo->modifierFraisHorsForfaitPourValidation($_REQUEST['modifierFraisHorsForfait'], $_REQUEST['mois'], $_REQUEST['id']);
        header('Location: index.php?uc=validerFrais&action=getInfos&id=' . $_REQUEST['id'] . '&mois=' . $_REQUEST['mois'] . '&modif=1');
        break;
}
