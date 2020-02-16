<?php
include "blocks/_header_page_jeu.php";
ob_start();


$erreur = false;

//
// verif droits
//
$erreur      = 0;
$droit_modif = 'dcompt_enchantements';
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    $log = '';
    // initialisation de la méthode
    $methode = get_request_var('methode', 'debut');
    if ($methode != 'debut')
    {
        $compte = new compte;
        $compte = $verif_connexion->compte;

        $log = date("d/m/y - H:i") . " Compte $compte->compt_nom (n°$compt_cod) modifie le tannage.\n";
    }

    // Récupération des données sur les formules de parchemin
    $req_frm          = 'select frm_cod, frm_nom, frm_comp_cod from formule
		where frm_type = 4 order by frm_comp_cod, frm_nom';
    $stmt             = $pdo->query($req_frm);
    $formules_tannage = array();
    while ($result = $stmt->fetch())
    {
        $tmp_comp_cod = $result['frm_comp_cod'];
        if (!isset($formules_tannage[$tmp_comp_cod]))
        {
            $formules_tannage[$tmp_comp_cod] = array();
        }
        $formules_tannage[$tmp_comp_cod][$result['frm_cod']] = $result['frm_nom'];
    }

    // récupère la sous-liste des formules non utilisés pour un niveau de compétence donné
    function liste_formules_inutilisees($comp_cod, $formules_utilisees)
    {
        global $formules_tannage;
        $formules_inutilisees = array();
        foreach ($formules_tannage[$comp_cod] as $frm_cod => $nom)
        {
            if (!isset($formules_utilisees[$frm_cod]))
                $formules_inutilisees[] = $frm_cod;
        }
        return $formules_inutilisees;
    }

    switch ($methode)
    {
        case "ajouter":
            $log    .= "Ajout d’une possibilité de tannage.\n";
            $erreur = false;
            // Vérification des données : nv_frm_cod, nv_peau_cod, nv_frmco_usure
            if (!isset($nv_frm_cod) || !$nv_frm_cod || $nv_frm_cod == '--' ||
                !isset($nv_peau_cod) || !$nv_peau_cod || !is_numeric($nv_peau_cod) ||
                !isset($nv_frmco_usure) || !$nv_frmco_usure || !is_numeric($nv_frmco_usure))
            {
                $erreur = true;
                $log    .= "Erreur ! Au moins un des paramètres n’est pas valide.\n";
            }

            if (!$erreur)
            {
                // Création de l’élément formule_composant
                $req_ins = "INSERT INTO formule_composant (frmco_frm_cod, frmco_gobj_cod, frmco_num, frmco_usure)
					VALUES (:nv_frm_cod, :nv_peau_cod, 1, :nv_frmco_usure)";
                $stmt    = $pdo->prepare($req_ins);
                $stmt    = $pdo->execute(array(
                                             ":nv_frm_cod"     => $nv_frm_cod,
                                             ":nv_peau_cod"    => $nv_peau_cod,
                                             ":nv_frmco_usure" => $nv_frmco_usure
                                         ), $stmt);

                // récupération des infos pour écriture du log
                $gobj = new objet_generique();
                $gobj->charge($nv_peau_cod);

                $log      .= "$nv_frmco_usure% de " . $gobj->gobj_nom . ' peut maintenant donner un ';
                $req_info = "SELECT frm_nom, comp_libelle from formule
					inner join competences on comp_cod = frm_comp_cod
					where frm_cod = :nv_frm_cod";
                $stmt     = $pdo->prepare($req_info);
                $stmt     = $pdo->execute(array(
                                              ":nv_frm_cod" => $nv_frm_cod
                                          ), $stmt);
                $result   = $stmt->fetch();
                $log      .= $result['frm_nom'] . ' avec la compétence ' . $result['comp_libelle'] . '.';
            }
            break;

        case "durees":
            $log           .= "Modification des durées de tannage.\n";
            $req_info_comp = 'select distinct frm_temps_travail, frm_comp_cod, comp_libelle from formule
				inner join competences on comp_cod = frm_comp_cod
				where frm_type = 4 order by frm_comp_cod';
            $stmt          = $pdo->query($req_info_comp);
            while ($result = $stmt->fetch())
            {
                $duree_comp_cod = $result['frm_comp_cod'];
                $duree_comp_nom = $result['comp_libelle'];
                $duree_minutes  = $result['frm_temps_travail'];
                $var_duree      = "frm_temps_travail_$duree_comp_cod";
                if (isset($$var_duree) && $$var_duree != $duree_minutes)
                {
                    $log     .= "$duree_comp_nom : de $duree_minutes à " . $$var_duree . " minutes.\n";
                    $req_upd = "UPDATE formule SET frm_temps_travail = " . $$var_duree . "
						WHERE frm_type = 4 AND frm_comp_cod = $duree_comp_cod";
                    $stmt    = $pdo->query($req_upd);
                }
            }
            break;

        case "modifier":
            $log    .= "Modification d’une possibilité de tannage.\n";
            $erreur = false;
            // Vérification des données : frm_cod, peau_cod, frmco_usure
            if (!isset($frm_cod) || !$frm_cod ||
                !isset($peau_cod) || !$peau_cod || !is_numeric($peau_cod) ||
                !isset($frmco_usure) || !$frmco_usure || !is_numeric($frmco_usure))
            {
                $erreur = true;
                $log    .= "Erreur ! Au moins un des paramètres n’est pas valide.\n";
            }

            if (!$erreur)
            {
                // récupération des infos pour écriture du log
                $gobj = new objet_generique();
                $gobj->charge($peau_cod);
                $log      .= 'Modif « ' . $gobj->gobj_nom . ' » / ';
                $req_info = "SELECT frm_nom, comp_libelle, frmco_usure from formule
					inner join competences on comp_cod = frm_comp_cod
					inner join formule_composant on frmco_frm_cod = frm_cod
					where frm_cod = $frm_cod and frmco_gobj_cod = :peau";
                $stmt     = $pdo->prepare($req_info);
                $stmt     = $pdo->execute(array(":peau" => $peau_cod), $stmt);
                $result   = $stmt->fetch();
                $log      .= $result['frm_nom'] . ' / ' . $result['comp_libelle'] . ' : usure ' . $result['frmco_usure'] . " => $frmco_usure %.";

                // Modification de l’élément formule_composant
                $req_upd = "UPDATE formule_composant SET frmco_usure = :frm
					WHERE frmco_frm_cod = $frm_cod AND frmco_gobj_cod = :peau";
                $stmt    = $pdo->prepare($req_upd);
                $stmt    = $pdo->execute(array(":peau" => $peau_cod,
                                               ":frm"  => $frm_cod), $stmt);
            }
            break;

        case "supprimer":
            $log    .= "Modification d’une possibilité de tannage.\n";
            $erreur = false;
            // Vérification des données : frm_cod, peau_cod
            if (!isset($frm_cod) || !$frm_cod ||
                !isset($peau_cod) || !$peau_cod || !is_numeric($peau_cod))
            {
                $erreur = true;
                $log    .= "Erreur ! Au moins un des paramètres n’est pas valide.\n";
            }

            if (!$erreur)
            {
                // récupération des infos pour écriture du log
                $req_info = "SELECT gobj_nom FROM objet_generique WHERE gobj_cod = :peau";
                $stmt     = $pdo->prepare($req_info);
                $stmt     = $pdo->execute(array(":peau" => $peau_cod), $stmt);
                $result   = $stmt->fetch();
                $log      .= 'Suppression du tannage de « ' . $result['gobj_nom'] . ' » / ';
                $req_info = "SELECT frm_nom, comp_libelle from formule
					inner join competences on comp_cod = frm_comp_cod
					where frm_cod = :frm";
                $stmt     = $pdo->prepare($req_info);
                $stmt     = $pdo->execute(array(":frm" => $frm_cod), $stmt);
                $result   = $stmt->fetch();
                $log      .= $result['frm_nom'] . ' / ' . $result['comp_libelle'] . '.';

                // Suppression de l’élément formule_composant
                $req_del = "DELETE FROM formule_composant
					WHERE frmco_frm_cod = :frm AND frmco_gobj_cod = :peau";
                $stmt    = $pdo->prepare($req_del);
                $stmt    = $pdo->execute(array(":frm"  => $frm_cod,
                                               ":peau" => $peau_cod), $stmt);
            }
            break;
    }

    if ($log)
    {
        echo "<div class='bordiv'>Mise-à-jour du tannage<br /><pre>$log</pre></div>";
        if (!$erreur) writelog($log, 'params_edit');
    }

    // Affichage des durées de tannage
    echo '<p>Durées de tannage</p>';
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="methode" value="durees" />';
    echo '<table><tr><td class="titre">Compétence</td><td class="titre">Durée</td></tr>';
    $req_info_comp = 'select distinct frm_temps_travail, frm_comp_cod, comp_libelle from formule
		inner join competences on comp_cod = frm_comp_cod
		where frm_type=4 order by frm_comp_cod';
    $stmt          = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $duree_comp_cod = $result['frm_comp_cod'];
        $duree_comp_nom = $result['comp_libelle'];
        $duree_minutes  = $result['frm_temps_travail'];
        echo "<tr><td class='soustitre2'>$duree_comp_nom</td>
			<td><input type='text' name='frm_temps_travail_$duree_comp_cod' value='$duree_minutes' size='6' /> minutes</td></tr>";
    }
    echo '<tr><td colspan="2"><input type="submit" value="Modifier !" class="test" /></td></tr></table></form>';

    // Affichage de la liste des options de tannage
    echo '<p>Liste des peaux, et des parchemins vierges qu’elles peuvent produire</p>';
    echo '<table><tr><td class="titre" rowspan="2">Peau</td><td class="titre" rowspan="2">Niveau</td>
		<td class="titre" colspan="3">Parchemins produits</td></tr>
		<tr>';
    $req_comp = 'select comp_libelle from competences 
		where comp_cod in (91, 92, 93) order by comp_cod';
    $stmt     = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $nom_comp = $result['comp_libelle'];
        echo "<td class='titre'>$nom_comp</td>";
    }
    echo '</tr>';

    $req_info_peau = 'select gobj_cod, gobj_nom, gobj_niv_peau from objet_generique 
		where gobj_tobj_cod = 24 
		order by gobj_nom';
    $stmt          = $pdo->query($req_info_peau);


    $req_info_parcho = "select frm_cod, frm_nom, gobj_niv_parchemin, frm_comp_cod, frmco_usure
				from formule_composant
				inner join formule on frm_cod = frmco_frm_cod
				inner join formule_produit on frmpr_frm_cod = frm_cod
				inner join objet_generique on gobj_cod = frmpr_gobj_cod
				where frmco_gobj_cod = :peau
					and frm_type = 4
					and frm_comp_cod = :icomp
				order by gobj_niv_parchemin";
    $stmt2           = $pdo->prepare($req_info_parcho);


    $comp = array(91, 92, 93);
    while ($result = $stmt->fetch())
    {
        $peau_qualite = $result['gobj_niv_peau'];
        $peau_nom     = $result['gobj_nom'];
        $peau_cod     = $result['gobj_cod'];
        echo "<tr valign='center'><td class='titre'>$peau_nom</td><td>$peau_qualite</td>";
        foreach ($comp as $icomp)
        {
            echo '<td class="soustitre2" valign="top">';
            $formules_utilisees = array();
            $stmt2              = $pdo->execute(array(":peau"  => $peau_cod,
                                                      ":icomp" => $icomp), $stmt2);
            $nombre_parcho      = 0;
            while ($result2 = $stmt2->fetch())
            {
                $frm_cod                      = $result2['frm_cod'];
                $parcho                       = $result2['frm_nom'];
                $usure                        = $result2['frmco_usure'];
                $niveau                       = $result2['gobj_niv_parchemin'];
                $formules_utilisees[$frm_cod] = $frm_cod;
                $nombre_parcho++;

                echo "<p>$parcho<br />
					<form style='display:inline' action='' method='post'>
					Usure  <input type='text' size='4' value='$usure' name='frmco_usure' /> points.<br />
					<input type='hidden' name='methode' value='modifier' />
					<input type='hidden' name='frm_cod' value='$frm_cod' />
					<input type='hidden' name='peau_cod' value='$peau_cod' />
					<input type='submit' class='test' value='Modifier' />
				</form>";
                echo "<form style='display:inline' action='' method='post' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce produit de tannage ?\");'>
						<input type='hidden' name='methode' value='supprimer' />
						<input type='hidden' name='frm_cod' value='$frm_cod' />
						<input type='hidden' name='peau_cod' value='$peau_cod' />
						<input type='submit' class='test' value='Supprimer' />
					</form></p>";

                if ($nombre_parcho < sizeof($formules_tannage[$icomp]))
                    echo "<hr />";
            }
            if ($nombre_parcho < sizeof($formules_tannage[$icomp]))
            {
                echo "<form action='' method='post' onsubmit=''>
					<input type='hidden' name='methode' value='ajouter' />
					<input type='hidden' name='nv_peau_cod' value='$peau_cod' />
					<p><select name='nv_frm_cod'><option value='-1'>Nouveau type de parchemin vierge...</option>";

                $parchos_inutilises = liste_formules_inutilisees($icomp, $formules_utilisees);
                foreach ($parchos_inutilises as $frm_cod)
                    echo "<option value='$frm_cod'>" . $formules_tannage[$icomp][$frm_cod] . '</option>';

                echo "</select>
					<br />Usure  <input type='text' size='4' value='100' name='nv_frmco_usure' title='Valeur entre 0 et 100 : nombre de points d’usure perdus par la peau à chaque tannage.
					Elle commence à 100, et disparaît en arrivant à 0.' /> points.<br />
					<input type='submit' class='test' value='Créer' />
				</form>";
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";