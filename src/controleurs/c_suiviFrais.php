<?php

require_once 'vues/v_sommaire.php';

$visiteurMois = $pdo->getVisiteurMoisSuiviFiche();
$mois = getSixDerMois();
switch ($_REQUEST['action']) {
    case 'selectionnerMois':
        require_once 'vues/v_suiviFraisMois.php';
        break;
    case 'getInfos':
        if (isset($_REQUEST['idMois']) && $_REQUEST['idMois'] == 'default') {
            header('Location: index.php?uc=suiviFrais&action=selectionnerMois');
        } else if (isset($_REQUEST['idMois'])) {
            require_once 'vues/v_suiviFraisMois.php';
            $explode = explode('_', $_REQUEST["idMois"]);
            $postId = $explode[0];
            $dateAnn = substr($explode[1], 0, 4);
            $dateMois = substr($explode[1], 4);
            $postMois = $dateAnn . $dateMois;
            $date = dateAnglaisVersFrancais($pdo->getDateValidation($postId, $postMois)['dateModif']);
            $moisComplet = getMoisAvecNumMois(substr($postMois, -2)) . ' ' . substr($postMois, -0, 4);
            $montantValide = $pdo->getMontantValideSuivi($postId, $postMois);
            $fraisForfait = $pdo->getFraisForfaitPourValidation($postId, $postMois);
            $fraisHorsForfait = $pdo->getFraisHorsForfaitPourValidation($postId, $postMois);
            $nbJustificatifs = count($fraisHorsForfait);
            require_once 'vues/v_suiviFraisInfo.php';
        }
        break;
    case 'validationSuivi':
        $pdo->validerSuivi($_REQUEST['id'], $_REQUEST['mois']);
        header('Location: index.php?uc=suiviFrais&action=selectionnerMois');
        break;
}
