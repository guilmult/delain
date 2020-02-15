﻿<?php
include "blocks/_header_page_jeu.php";
include "../includes/fonctions.php";
$param = new parametres();
ob_start();

// TODO A Supprimer :
if (isset($_REQUEST['perso']))
{
    $perso = $_REQUEST['perso'];
}


echo '<script type="text/javascript" src="../scripts/cocheCase.js"></script>';
echo '<script type="text/javascript">
	function vendreNombre(gobj_cod, nombre)
	{
		var chkbx = document.getElementById("gobj[" + gobj_cod + "]");
		var inputNombre = document.getElementById("qtegros[" + gobj_cod + "]");
		chkbx.checked = true;
		inputNombre.value = nombre;
	}
	function vendreNombreIncrement(gobj_cod, nombre, nbmax)
	{
		var chkbx = document.getElementById("gobj[" + gobj_cod + "]");
		var inputNombre = document.getElementById("qtegros[" + gobj_cod + "]");
		chkbx.checked = true;
		if (nombre + parseInt(inputNombre.value) < nbmax)
			inputNombre.value = nombre + parseInt(inputNombre.value);
		else
			inputNombre.value = nbmax;
		if (inputNombre.value <= 0)
		{
			inputNombre.value = 0;
			chkbx.checked = false;
		}
	}
	</script>
	';

$methode          = get_request_var('methode', 'debut');
$identifie['O'] = "";
$identifie['N'] = "(non identifié)";

// Définition des types d’objets qui se vendent en gros.
// 5 = runes,
// 11 = objets de quête,
// 17 = minerais,
// 18 = minéraux,
// 19 = pierres précieuses,
// 21 = potions,
// 22 = composants alchimie
// 28 = espèce minérale
// 30 = ingrédients magiques
// 12 = féves (osselets merveilleux)
// 42 = Grisbi
$types_ventes_gros = "(5, 11, 12, 17, 18, 19, 21, 22, 28, 30, 34, 42)";

/************************/
/* recherche des objets */
/************************/
switch ($methode)
{
    case "debut":
        echo "<em><br><strong><p>Les transactions à l’intérieur d’un même compte pour un montant nul seront directement acceptées</em></strong><br><br> ";
        echo "<div class=\"titre\">Choix du destinataire </div>";
        echo "<form name=\"tran\" method=\"post\" action=\"\">";
        echo "Choisissez le joueur à qui vous voulez vendre des objets : ";
        echo "<input type=\"hidden\" name=\"methode\" value=\"e1\">";
        $req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
        $stmt = $pdo->query($req_pos);
        $result = $stmt->fetch();
        $pos_actuelle = $result['ppos_pos_cod'];

        $req_vue = "select lower(perso_cod) as minusc,perso_cod,perso_nom from perso, perso_position where ppos_pos_cod = $pos_actuelle and ppos_perso_cod = perso_cod and perso_cod != $perso_cod  and perso_type_perso in (1,2,3) and perso_actif = 'O' order by perso_type_perso,perso_nom,minusc";

        $liste_vue = $html->select_from_query($req_vue, "perso_cod", "perso_nom");

        if ($liste_vue == '')
        {
            echo 'Aucun joueur en vue';
        } else
        {
            echo '<select name="perso">' . $liste_vue . '</select>';
            echo "<center><input type=\"submit\" class=\"test\" value=\"Passer à la suite\"></center>";
        }
        echo "</form>";
        break;

    case "e1";
        echo "<div class=\"titre\">Sélection des objets à vendre</div>";
        echo "<form name=\"tran\" method=\"post\" action=\"\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"e3\">";
        echo "<input type=\"hidden\" name=\"perso\" value=\"$perso\">";

        $req_objets_unitaires = "select obj_etat, gobj_tobj_cod, obj_cod, obj_nom, obj_nom_generique, tobj_libelle, perobj_identifie
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			left outer join transaction on tran_obj_cod = obj_cod
			where perobj_perso_cod = $perso_cod
				and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
				and perobj_equipe = 'N'
				and obj_deposable != 'N'
				and tran_obj_cod IS NULL
			order by gobj_tobj_cod, obj_nom";

        $req_objets_gros = "select gobj_nom, gobj_cod, count(*) as nombre
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			left outer join transaction on tran_obj_cod = obj_cod
			where perobj_perso_cod = $perso_cod
				and gobj_tobj_cod in $types_ventes_gros
				and obj_nom = gobj_nom
				and perobj_equipe = 'N'
				and obj_deposable != 'N'
				and tran_obj_cod IS NULL
			group by gobj_nom, gobj_cod
			order by gobj_tobj_cod, gobj_nom";

        // Affichage des objets en vente à l’unité
        $stmt = $pdo->query($req_objets_unitaires);
        $nb_objets = $stmt->rowCount();
        if ($nb_objets > 0)
        {
            $etat = '';
            echo "<div style=\"text-align:center;\" id='vente_detail'>Vente au détail : cliquez sur les objets que vous souhaitez vendre, et indiquez leurs prix de vente. Les runes et composants d’alchimie se vendent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
            echo("<center><table>");
            echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\');">cocher/décocher/inverser</a></td></tr>';
            echo '<tr><td class="soustitre2"></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Prix demandé</strong></td></tr>';
            while ($result = $stmt->fetch())
            {
                if ($result['perobj_identifie'] == 'O')
                {
                    $nom_objet = $result['obj_nom'];
                } else
                {
                    $nom_objet = $result['obj_nom_generique'];
                }
                $si_identifie = $result['perobj_identifie'];
                echo "<tr>";
                echo "<td><input type=\"checkbox\" class=\"vide\" name=\"obj[" . $result['obj_cod'] . "]\" value=\"0\" id=\"obj[" . $result['obj_cod'] . "]\"></td>";
                echo "<td class=\"soustitre2\"><label for=\"obj[" . $result['obj_cod'] . "]\">$nom_objet $identifie[$si_identifie]";
                if (($result['gobj_tobj_cod'] == 1) || ($result['gobj_tobj_cod'] == 2) || ($result['gobj_tobj_cod'] == 24))
                {
                    echo "  - " . get_etat($result['obj_etat']);
                }
                echo "</label></td>";

                echo "<td><input type=\"text\" name=\"prix[" . $result['obj_cod'] . "]\" size=\"6\" value=\"0\" /> brouzoufs</td>";
                echo "</tr>";
            }
            echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\');">cocher/décocher/inverser</a></td></tr>';

            echo "</table></center>";
        }

        // Affichage des objets en vente en gros
        $stmt = $pdo->query($req_objets_gros);
        $nb_objets_gros = $stmt->rowCount();
        if ($nb_objets_gros > 0)
        {
            echo "<div style=\"text-align:center;\" id='vente_detail'>Vente en gros : cliquez sur les objets que vous souhaitez vendre, indiquez-en le nombre puis leurs prix de vente. Les autres objets se vendent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
            echo("<center><table>");
            echo '<tr><td class="soustitre2" colspan="4"><strong>Actions</strong></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Quantité à vendre</strong></td><td class="soustitre2"><strong>Prix demandé (à la pièce !)</strong></td></tr>';
            while ($result = $stmt->fetch())
            {
                $nom_objet = $result['gobj_nom'];
                $quantite_dispo = $result['nombre'];
                $gobj_cod = $result['gobj_cod'];
                $id_chk = "gobj[$gobj_cod]";
                $id_qte = "qtegros[$gobj_cod]";
                $id_prx = "prixgros[$gobj_cod]";
                echo "<tr>";
                echo "<td class='soustitre2'><input type=\"checkbox\" class=\"vide\" name=\"$id_chk\" value=\"0\" id=\"$id_chk\"></td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, 1, $quantite_dispo);'>+1</a>&nbsp;</td>
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, -1, $quantite_dispo);'>-1</a>&nbsp;</td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombre($gobj_cod, $quantite_dispo);'>max</a>&nbsp;</td> ";
                echo "<td class=\"soustitre2\"><label for=\"$id_chk\">$nom_objet</label></td>";
                echo "<td><input type=\"text\" name=\"$id_qte\" value=\"0\" size=\"6\" id=\"$id_qte\" 
					onclick='document.getElementById(\"$id_chk\").checked=true;' /> (max. $quantite_dispo)</td>";
                echo "<td><input type=\"text\" name=\"$id_prx\" value=\"0\" size=\"6\" /> brouzoufs</td>";
                echo "</tr>";
            }

            echo "</table></center>";
        }

        if ($nb_objets + $nb_objets_gros > 0)
        {
            echo "<div><center><input class=\"test\" type=\"submit\" value=\"Passer à la suite\" /></center></div></form>";
        } else
        {
            echo 'Vous n’avez aucun objet à vendre';
        }
        break;

    case "e3";
        $compteur_accept_auto = 0;
        $compteur_accept = 0;

        //Analyse des cas d’erreurs
        $tmpperso1 = new perso;
        $tmpperso1->charge($perso_cod);
        $tmpperso2 = new perso;
        $tmpperso2->charge($_REQUEST['perso']);
        $fonctions = new fonctions();

        $tab        = $tmpperso1->get_position();
        $pos_perso1 = $tab['pos']->pos_cod;
        $tab        = $tmpperso2->get_position();
        $pos_perso2 = $tab['pos']->pos_cod;
        $distance   = $fonctions->distance($pos_perso1, $pos_perso2);

        $lieu_protege = '';
        if ($tmpperso1->is_lieu())
        {
            $tab_lieu     = $tmpperso1->get_lieu();
            $lieu_protege = $tab_lieu['lieu']->lieu_refuge;
        }


        $erreur_globale = false;

        if ($distance != 0)
        {
            $erreur_globale = true;
            echo "Vous ne pouvez pas faire de transaction sur des positions différentes !";
        }
        if ($is_lieu and $lieu_protege == 'O')
        {
            $erreur_globale = true;
            echo "Vous ne pouvez pas faire de transaction sur un lieu protégé !";
        }

        // Acceptation automatique des transactions entre persos d’un même compte
        if ($tmpperso1->perso_type_perso == 1)
        {
            $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $compt1 = $result['pcompt_compt_cod'];
        } else
        {
            if ($result['perso_type_perso'] == 3)
            {
                $req = "select pcompt_compt_cod from perso_familier,perso_compte where pfam_familier_cod = $perso_cod and pfam_perso_cod = pcompt_perso_cod";
                $stmt = $pdo->query($req);
                $result = $stmt->fetch();
                $compt1 = $result['pcompt_compt_cod'];
            } else
            {
                $compt1 = '';
            }
        }
        $req = "select perso_type_perso from perso where perso_cod = " . $_REQUEST['perso'];
        $stmt = $pdo->query($req);
        $result = $stmt->fetch();
        if ($result['perso_type_perso'] == 1)
        {
            $req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = " . $_REQUEST['perso'];
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $compt2 = $result['pcompt_compt_cod'];
        } else
        {
            if ($result['perso_type_perso'] == 3)
            {
                $req = "select pcompt_compt_cod from perso_familier,perso_compte where pfam_familier_cod = " . $_REQUEST['perso'] . " and pfam_perso_cod = pcompt_perso_cod";
                $stmt = $pdo->query($req);
                $result = $stmt->fetch();
                $compt2 = $result['pcompt_compt_cod'];
            } else
            {
                $compt2 = '';
            }
        }

        // traitement des ventes au détail
        if (isset($obj) && !$erreur_globale)
        {
            foreach ($obj as $key => $val)
            {
                $req_ident = "select perobj_identifie from perso_objets where perobj_obj_cod = $key ";
                $stmt = $pdo->query($req_ident);
                $result = $stmt->fetch();
                $si_identifie = $result['perobj_identifie'];
                $erreur = 0;
                $prix_obj = $prix[$key];
                if ($prix_obj < 0)
                {
                    echo "Erreur ! Le prix doit être positif !";
                    $erreur = 1;
                }
                if ($prix_obj == '')
                {
                    echo "Erreur ! Le prix doit être fixé !";
                    $erreur = 1;
                }
                $req_exist = "select tran_cod from transaction where tran_obj_cod = $key ";
                $stmt = $pdo->query($req_exist);
                if ($stmt->rowCount() > 0)
                {
                    echo "Erreur ! Une transaction existe déjà sur l’objet $key. Ceci peut arriver en cas de double-clic sur le bouton de validation précédent.";
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    $req_ins = "insert into transaction (tran_obj_cod, tran_vendeur, tran_acheteur, tran_nb_tours, tran_prix, tran_identifie)
						values ($key, $perso_cod, " . $_REQUEST['perso'] . ", " . $param->getparm(7) . ", $prix_obj, '$si_identifie')
						RETURNING tran_cod";
                    $stmt = $pdo->query($req_ins);
                    $result = $stmt->fetch();
                    $num_tran = $result['tran_cod'];

                    if ($compt1 == $compt2 and $prix_obj == 0)
                    {
                        $req_acc_tran = "select accepte_transaction($num_tran) as resultat";
                        $stmt = $pdo->query($req_acc_tran);
                        $result = $stmt->fetch();
                        $resultat_temp = $result['resultat'];
                        $tab_res = explode(";", $resultat_temp);
                        if ($tab_res[0] == -1)
                        {
                            echo("Une erreur est survenue : $tab_res[1]");
                        } else
                        {
                            $compteur_accept_auto++;
                            $compteur_accept++;
                        }
                    } else
                    {
                        $compteur_accept++;
                    }
                }
            }//Fin du foreach
        }

        // traitement des ventes en gros
        if (isset($gobj) && !$erreur_globale)
        {
            
            

            // Récupération globale des infos
            $req_objets_gros = "select gobj_nom, gobj_cod, count(*) as nombre
				from perso_objets
				inner join objets on obj_cod = perobj_obj_cod
				inner join objet_generique on gobj_cod = obj_gobj_cod
				left outer join transaction on tran_obj_cod = obj_cod
				where perobj_perso_cod = $perso_cod
					and gobj_tobj_cod in $types_ventes_gros
					and obj_nom = gobj_nom
					and perobj_equipe = 'N'
					and obj_deposable != 'N'
					and tran_obj_cod IS NULL
				group by gobj_nom, gobj_cod
				order by gobj_tobj_cod";
            $stmt = $pdo->query($req_objets_gros);

            while ($result = $stmt->fetch())
            {
                $gobj_cod = $result['gobj_cod'];
                $gobj_nom = $result['gobj_nom'];
                $nombre_max = $result['nombre'];
                if (isset($gobj[$gobj_cod]))
                {
                    $prix_obj = $prixgros[$gobj_cod];
                    $qte_obj = $qtegros[$gobj_cod];
                    $erreur = 0;

                    // Vérification des données
                    if ($prix_obj < 0)
                    {
                        echo "Erreur sur « $gobj_nom » ! Le prix doit être positif !";
                        $erreur = 1;
                    }
                    if ($prix_obj == '')
                    {
                        echo "Erreur sur « $gobj_nom » ! Le prix doit être fixé !";
                        $erreur = 1;
                    }
                    if ($qte_obj > $nombre_max)
                    {
                        echo "Erreur sur « $gobj_nom » ! Vous ne pouvez pas en vendre plus de $nombre_max !";
                        $erreur = 1;
                    }

                    if ($erreur == 0)
                    {
                        $req_objets = "select obj_cod
							from perso_objets
							inner join objets on obj_cod = perobj_obj_cod
							inner join objet_generique on gobj_cod = obj_gobj_cod
							left outer join transaction on tran_obj_cod = obj_cod
							where perobj_perso_cod = $perso_cod
								and gobj_cod = $gobj_cod
								and obj_nom = gobj_nom
								and perobj_equipe = 'N'
								and obj_deposable != 'N'
								and tran_obj_cod IS NULL
							limit $qte_obj";
                        $stmt2 = $pdo->query($req_objets);

                        while ($result2 = $stmt2->fetch())
                        {
                            $obj_cod = $result2['obj_cod'];
                            $req_ins = "insert into transaction (tran_obj_cod, tran_vendeur, tran_acheteur, tran_nb_tours, tran_prix, tran_identifie)
								values ($obj_cod, $perso_cod, " . $_REQUEST['perso'] . ", " . $param->getparm(7) . ", $prix_obj, 'O')
								RETURNING tran_cod";
                            $stmt3 = $pdo->query($req_ins);
                            $result3 = $stmt3->fetch();
                            $num_tran = $result3['tran_cod'];

                            if ($compt1 == $compt2 && $prix_obj == 0)
                            {
                                $req_acc_tran = "select accepte_transaction($num_tran) as resultat";
                                $stmt3 = $pdo->query($req_acc_tran);
                                $result3 = $stmt3->fetch();
                                $resultat_temp = $result3['resultat'];
                                $tab_res = explode(";", $resultat_temp);
                                if ($tab_res[0] == -1)
                                {
                                    echo("Une erreur est survenue : $tab_res[1]");
                                } else
                                {
                                    $compteur_accept_auto++;
                                    $compteur_accept++;
                                }
                            } else
                            {
                                $compteur_accept++;
                            }
                        }
                    } //Fin de la boucle pour un type d’objet
                }
            } //Fin de la boucle sur les types d’objet
        }
        $compteur_accept_man = $compteur_accept - $compteur_accept_auto;

        $texte_auto = "";
        $texte_man = "";
        $texte_evt = "";

        if ($compteur_accept_man == 1)
        {
            $texte_man = "<p>La transaction est enregistrée. L’acheteur a deux tours pour valider cette transaction, faute de quoi elle sera annulée.<br />
				Elle sera également annulée si vous abandonnez l’objet (volontairement ou non), si vous l’équipez, ou si vous vous déplacez.</p><br />";
        }
        if ($compteur_accept_man > 1)
        {
            $texte_man = "<p>$compteur_accept_man transactions enregistrées. L’acheteur a deux tours pour les valider, faute de quoi elles seront annulées.<br />
				Chacune pourra également être annulée si vous abandonnez l’objet (volontairement ou non), si vous l’équipez, ou si vous vous déplacez.</p><br />";
        }

        if ($compteur_accept_auto == 1)
        {
            $texte_auto = "<strong>La transaction est enregistrée et directement validée.<br /></strong>";
        }
        if ($compteur_accept_auto > 1)
        {
            $texte_auto = "<strong>$compteur_accept_auto transactions enregistrées et directement validées<br /></strong>";
        }

        if ($compteur_accept == 1)
        {
            $texte_evt = "[attaquant] a proposé un objet à la vente à [cible]";
        }
        if ($compteur_accept > 1)
        {
            $texte_evt = "[attaquant] a proposé $compteur_accept objets à la vente à [cible]";
        }

        if ($compteur_accept > 0)
        {
            $req_evt = "select insere_evenement($perso_cod, " . $_REQUEST['perso'] . ", 17, '$texte_evt', 'N', NULL)";
            $stmt = $pdo->query($req_evt);

            echo $texte_man . $texte_auto;
        }
        break;
}
?>
    <br><br><a href="transactions2.php">Retour aux transactions</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";