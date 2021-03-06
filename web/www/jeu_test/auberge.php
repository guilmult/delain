<?php
//
//Contenu de la div de droite
//
$contenu_page = '';
$param        = new parametres();
// on regarde si le joueur est bien sur une banque

$type_lieu = 4;
$nom_lieu  = 'une auberge';
$perso     = $verif_connexion->perso;

define('APPEL', 1);
include "blocks/_test_lieu.php";

$methode = get_request_var('methode', 'debut');
if ($erreur == 0)
{
    // Fidèle de Tonto (id=9) ?
    $tonto = 0;

    $dp = new dieu_perso();
    if ($dp->getByPersoCod($perso_cod))
    {
        $tonto = 1;
    }


    switch ($methode)
    {
        case "debut":
            $nb_pa        = $perso->perso_pa;
            $prix         = $nb_pa * 2;
            $tab_temple   = $perso->get_lieu();
            $contenu_page .= '<p><img src="../images/auberge.png"><br />
                <strong></strong>' . $tab_temple['lieu']->lieu_nom . " - " . $tab_temple['lieu']->description . '
				<p>Bienvenue dans mon humble auberge, aventurier.
				<p>Voici les services que nous avons à proposer :';
            if ($nb_pa != 0)
                $contenu_page .= '<a href="' . $_SERVER['PHP_SELF'] . '?methode=repos">';
            $contenu_page .= 'Se reposer ';
            if ($nb_pa != 0)
            {
                $contenu_page .= '</a>(' . $nb_pa . 'PA) - ' . $prix . ' brouzoufs.<br />';
            } else
            {
                $contenu_page .= '<em>(pas assez de PA)</em>';
            }

            $contenu_page .= '<br>
				<a href="' . $_SERVER['PHP_SELF'] . '?methode=boire">Boire un verre ? (10 brouzoufs - 4 PA)</a><br>
				<a href="' . $_SERVER['PHP_SELF'] . '?methode=offre">Offrir un verre ? (10 brouzoufs - 4 PA)</a><br>
				<a href="' . $_SERVER['PHP_SELF'] . '?methode=ecoute_rumeur">Payer le barman pour écouter une rumeur ? (20 brouzoufs)</a><br>
				<a href="' . $_SERVER['PHP_SELF'] . '?methode=rumeur">Lancer une rumeur ?</a><br>';

            // Pour les fidèles de Tonto, les auberges sont comme des autels de prière
            if ($tonto)
            {
                $priere_pa    = $param->getparm(48);
                $contenu_page .= '<a href="' . $_SERVER['PHP_SELF'] . '?methode=prier">Prier votre dieu Tonto ? (' . $priere_pa . ' PA)</a><br>';
            }

            // Bière & opium
            $nb_futs  = $perso->compte_objet(196);
            $nb_opium = $perso->compte_objet(186);
            if ($nb_futs != 0)
            {
                if ($lieu_cod == 13)
                {
                    $contenu_page .= '<hr><p>Je vois que vous avez des futs de bière. Toutefois, vous pouvez les garder, je brasse tout moi même. Cela donne une bière incomparable par rapport à celle qui vient des extérieurs.';
                } else
                {
                    $contenu_page .= '<hr><p>Vous avez ' . $nb_futs . ' futs de bière dans votre inventaire. <br>
						<p><a href="action.php?methode=vente_auberge&objet=1">Les revendre ? </a><br></hr>';
                }
            }
            if ($nb_opium != 0)
            {
                if ($lieu_cod == 13)
                {
                    $contenu_page .= '<p>Au cas où vous viendrez ici pour revendre des matières illicites, sachez que mon auberge est un endroit respectable, et que vous pouvez aller voir ailleurs !';
                } else
                {
                    $contenu_page .= '<hr><p>Vous avez ' . $nb_opium . ' paquets bruns dans votre inventaire. <br>
						<p><a href="action.php?methode=vente_auberge&objet=2">Les revendre ? </a><br></hr>';
                }
            }
            include "quete.php";
            $contenu_page .= ob_get_contents();
            ob_end_clean();
            ob_start();
            break;

        case 'prier':
            if ($tonto)
            {
                echo '<p>Vous vous apprêtez à prier <strong>Tonto<br>
  				<a href="action.php?methode=prie&dieu=9">Continuer ?</a>';
            } else
                echo "<p>Vous ne pouvez prier Tonto, car votre coeur appartient à un autre dieu.<br>";

            break;


        case "repos":

            $nb_pa = $perso->perso_pa;
            $prix  = $nb_pa * 2;
            $sexe  = $perso->perso_sex;

            if ($perso->perso_po < $prix)
            {
                $contenu_page .= '<p>Vous savez, ' . $nom_sexe[$sexe] . ', nous n\'apprécions pas vraiment le genre de personnes qui n\'ont pas de quoi payer ce qu\'elles demandent.<br />
					Revenez quand vous poches seront plus pleines, ou bien allez dormir dehors, au milieu des monstres.';
                $erreur       = 1;
            } else
            {
                $gain_pv = $nb_pa * 1.5;
                $gain_pv = round($gain_pv);
                $diff_pv = $perso->perso_pv_max - $perso->perso_pv;
                if ($gain_pv > $diff_pv)
                {
                    $gain_pv = $diff_pv;
                }
                $perso->perso_pv = $perso->perso_pv + $gain_pv;
                $perso->perso_pa = 0;
                $perso->perso_po = $perso->perso_po - $prix;
                $perso->stocke();
                $contenu_page .= '<p>Vous vous êtes bien reposé. Vous avez regagné <strong>' . $gain_pv . '</strong> PV';

            }
            $contenu_page .= '<p><a href="' . $_SERVER['PHP_SELF'] . '">Retour</a>';
            break;
        case "boire":
            $erreur   = 0;
            $req      = 'select lpos_lieu_cod from lieu_position,perso_position
				where ppos_perso_cod = :perso_cod
				and ppos_pos_cod = lpos_pos_cod';
            $stmt     = $pdo->prepare($req);
            $stmt     = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
            $result   = $stmt->fetch();
            $lieu_cod = $result['lpos_lieu_cod'];


            $nb_po = $perso->perso_po;
            $prix  = 10;
            $sexe  = $perso->perso_sex;

            if ($perso->perso_po < $prix)
            {
                $contenu_page .= '<p>Vous savez, ' . $nom_sexe[$sexe] . ', nous n\'apprécions pas vraiment le genre de personnes qui n\'ont pas de quoi payer ce qu\'elles demandent.<br />
				Revenez quand vous poches seront plus pleines, ou bien allez boire ailleurs.';
                $erreur       = 1;
            }
            if ($perso->perso_pa < 4)
            {
                $contenu_page .= '<p>pas assez de PA....<br />';
                $erreur       = 1;
            }
            if ($erreur == 0)
            {
                $perso->perso_po = $perso->perso_po - 10;
                $perso->perso_pa = $perso->perso_pa - 4;
                $perso->stocke();

                $req  = 'select paub_perso_cod from perso_auberge where paub_perso_cod =  :perso_cod . 
					and paub_lieu_cod = :lieu_cod';
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":perso_cod" => $perso_cod, ":lieu_cod" => $lieu_cod), $stmt);
                $pab  = new perso_auberge();
                if ($stmt->rowCount() == 0)
                {
                    $pab->paub_nombre    = 1;
                    $pab->paub_perso_cod = $perso_cod;
                    $pab->paub_lieu_cod  = $lieu_cod;
                    $pab->stocke(true);
                } else
                {
                    $pab->paub_nombre++;
                    $pab->paub_perso_cod = $perso_cod;
                    $pab->paub_lieu_cod  = $lieu_cod;
                    $pab->stocke();
                }

                $contenu_page          .= '<p>Vous vous asseyez à une table, et sirotez une bière bien fraiche.<br>
					<p><em>Rumeur :</em> ' . $fonctions->get_rumeur();
                $texte_evt             = "[attaquant] siroté une petite bière tout seul";
                $levt                  = new ligne_evt();
                $levt->levt_tevt_cod   = 82;
                $levt->levt_perso_cod1 = $perso_cod;
                $levt->levt_texte      = $texte_evt;
                $levt->levt_attaquant  = $perso_cod;
                $levt->levt_cible      = $perso_cod;
                $levt->levt_lu         = 'O';
                $levt->levt_visible    = 'O';
                $levt->stocke(true);
                unset($levt);
                //
                // on recherche les auberges déjà visitées
                //
                $req          = 'select lieu_nom,pos_x,pos_y,etage_libelle
					from perso_auberge,lieu,lieu_position,etage,positions
					where paub_perso_cod = :perso_cod
					and paub_lieu_cod = lieu_cod
					and lpos_lieu_cod = lieu_cod
					and lpos_pos_cod = pos_cod
					and pos_etage = etage_numero';
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
                $contenu_page .= '<p>Voici les tavernes dans lesquelles vous avez déjà étanché votre soif : ';
                while ($result = $stmt->fetch())
                {
                    $contenu_page .= '<br /><strong>' . $result['lieu_nom'] . '</strong> (' . $result['pos_x'] . ', ' . $result['pos_y'] . ', ' . $result['etage_libelle'] . ')';
                }
            }
            break;
        case "offre":
            $ppos = new perso_position();
            $ppos->getByPerso($perso_cod);
            $pos  = $ppos->ppos_pos_cod;
            $req  = "select perso_cod,perso_nom,lower(perso_nom) as minusc
				from perso,perso_position
				where ppos_pos_cod = :pos
				and ppos_perso_cod = perso_cod
				and perso_actif = 'O'
				and perso_cod != :perso_cod
				order by minusc";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod" => $perso_cod,
                                        ":pos"       => $pos), $stmt);
            if ($stmt->rowCount() == 0)
            {
                $contenu_page .= 'Il n\'y a personne ici à qui vous puissiez offrir un verre.';
            } else
            {
                $contenu_page .= 'Voici les personnes présentes à qui vous pouvez offrir un verre :
				<table>';
                while ($result = $stmt->fetch())
                {
                    $contenu_page .= '<tr><td class="soustitre2"><strong><a href="visu_desc_perso.php?visu=' . $result['perso_cod'] . '">' . $result['perso_nom'] . '</td>
						<td><a href="action.php?methode=offre_boire&cible=' . $result['perso_cod'] . '">Offrir un verre ?</a></td></tr>';
                }
                $contenu_page .= '</table>';
            }
            break;
        case "ecoute_rumeur":
            $erreur = 0;

            $prix = 20;
            $sexe = $perso->perso_sex;

            if ($perso->perso_po < $prix)
            {
                $contenu_page .= '<p>Vous savez, ' . $nom_sexe[$sexe] . ', nous n\'apprécions pas vraiment le genre de personnes qui n\'ont pas de quoi payer ce qu\'elles demandent.<br />
				Revenez quand vous poches seront plus pleines, ou bien allez boire ailleurs.';
                $erreur       = 1;
            }
            if ($erreur == 0)
            {
                $perso->perso_po = $perso->perso_po - 20;
                $perso->stocke();

                $contenu_page .= "<p><em>Rumeur :</em> " . $fonctions->get_rumeur();
            }
            break;
        case "rumeur":
            $contenu_page .= '<p>Vous pouvez ici lancer une rumeur. Cette rumeur existera pendant ' . $param->getparm(44) . ' jours.<br>
			Pendant ce temps, les personnes qui viennent prendre un verre à l\'auberge auront une chance de l\'entendre. Toutefois, ils n\'auront aucun moyen de savoir qui l\'a lancée.<br>
			Afin d\'augmenter les chances que l\'on connaisse votre rumeur, vous pouvez soudoyer le barman. Plus vous lui donnez d\'argent, plus votre rumeur à des chances d\'être connue.<br>';

            $contenu_page .= "<p>Vous disposez de <strong>" . $perso->perso_po . "</strong> brouzoufs.";
            $contenu_page .= '
			<table>
			<form name="rumeur" method="post" action="' . $_SERVER['PHP_SELF'] . '">
			<input type="hidden" name="methode" value="rumeur2">
			<tr>
				<td class="soustitre2"><p>Texte :</td>
				<td><textarea cols="40" rows="10" name="rumeur_txt"></textarea></td>
			</tr>
			<tr>
				<td class="soustitre2"><p>Prix à mettre</td>
				<td><input type="text" name="prix" value="0"> brouzoufs</td>
			</tr>
			<tr>
				<td colspan="2"><p style="text-align:center;"><input type="submit" value="Envoyer !" class="test"></td>
			</tr>
			</form>
			</table>';
            break;
        case "rumeur2":
            $erreur     = 0;
            $rumeur_txt = get_request_var('rumeur_txt');
            if ($perso->perso_po < $prix)
            {
                $erreur       = 1;
                $contenu_page .= "<p>Il semble que vous n'ayez pas assez de brouzoufs pour payer le barman....";
            }
            if (strlen(trim($rumeur_txt)) < 5)
            {
                $erreur       = 1;
                $contenu_page .= "<p>Ah non, c'est un peu court jeune homme, on aurait pu dire bien des choses en somme...<br>Bref, je m'égare, mais votre texte est trop bref justement, parlez plus, n'hésitez pas !";
            }
            if ($erreur == 0)
            {
                $poids                 = $prix + 1;
                $rumeur                = new rumeurs();
                $rumeur->rum_perso_cod = $perso_cod;
                $rumeur->rum_texte     = $rumeur_txt;
                $rumeur->rum_poids     = $poids;
                $rumeur->stocke(true);

                $contenu_page .= "<p>Votre rumeur a bien été enregistrée ";

                $perso->perso_po = $perso->perso_po - $prix;
                $perso->stocke();
            }

            $contenu_page .= "<p>Vous disposez de <strong>" . $perso->perso_po . "</strong> brouzoufs.";
            break;
    }
}
