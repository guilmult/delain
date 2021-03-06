﻿<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>

    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
<?php $erreur = 0;
//
// verif droits

$droit_modif = 'dcompt_potions';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    // initialisation de la méthode
    $methode      = get_request_var('methode', 'debut');
    switch ($methode)
    {
        case "debut":
            ?>
            <table>
                <?php
                $req  = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where (gobj_tobj_cod = 21 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37 or gobj_tobj_cod = 39)
											and not exists (select 1 from formule_produit where frmpr_gobj_cod = gobj_cod ) 
											order by gobj_nom';
                $stmt = $pdo->query($req);
                echo '<br><hr><td class="titre">Liste des potions sans formule</td><br><br><table>
						<td><strong>Nom de la potion</strong></td><td><strong>Description</strong></td>';
                while ($result = $stmt->fetch())
                {
                    echo '<tr><td class="soustitre2"><br><a href="' . $_SERVER['PHP_SELF'] . '?methode=ajout&pot=' . $result['gobj_cod'] . '">' . $result['gobj_nom'] . '</a></td>
						<td class="soustitre2">' . $result['gobj_nom'] . '</td></tr>';

                }
                ?>
            </table>
            <hr><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=ajout">Ou ajouter une nouvelle formule de
            potion</a>
            <?php
            $req  =
                'select 	frmpr_frm_cod,frmpr_gobj_cod,frmpr_num,frm_cod,frm_type,frm_nom,frm_comp_cod from formule_produit,formule where frm_type = 2 and frm_cod = frmpr_frm_cod order by frm_nom ';
            $stmt = $pdo->query($req);
            echo '<br><table><td class="titre">Potions disponibles :</td><tr><br><br>
						<td><strong>Nom de la potion</strong></td><td><strong>Objets nécessaires et quantités</strong></td><td><strong>Description</strong></td><td><strong>Compétence nécessaire</strong></td>';
            while ($result = $stmt->fetch())
            {
                $cod_potion = $result['frm_cod'];
                $comp       = $result['frm_comp_cod'];
                echo '<tr><td class="soustitre2"><br><a href="' . $_SERVER['PHP_SELF'] . '?methode=modif&pot=' . $cod_potion . '">' . $result['frm_nom'] . '</a></td>';
                if ($stmt->rowCount() != 0)
                {
                    $req_composant = "select 	frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom from formule_composant,objet_generique	
														where frmco_frm_cod = $cod_potion 
														and frmco_gobj_cod = gobj_cod";
                    $stmt2         = $pdo->query($req_composant);
                    echo "<td>";
                    while ($result2 = $stmt2->fetch())
                    {
                        echo $result2['gobj_nom'] . " \t" . $result2['frmco_num'] . "<br>";
                    }

                    echo "</td><td class=\"soustitre2\">" . $result['frm_nom'] . "</td>";
                    $req_comp = "select comp_libelle from competences	
														where comp_cod = " . $comp;
                    $stmt2    = $pdo->query($req_comp);
                    $result2  = $stmt2->fetch();
                    echo "	<td class=\"soustitre2\">" . $result2['comp_libelle'] . "</td></tr>";
                }
            }
            ?>
            </table>

            <?php
            break;
        case "ajout":
            if ($pot != null)
            {
                $req    = 'select fpot_niveau from potions.fonction_potion where fpot_gobj_cod = ' . $pot;
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                $comp   = $result['fpot_niveau'];
            }
            ?>
            <table>
            <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="methode" value="ajout2">
            <tr>
                <td class="soustitre2">Nom / Description de la formule de potion (conserver le nom de la potion
                    dedans)
                </td>
                <td><textarea cols="50" rows="10" name="nom">Rentrer une description</textarea></td>
            </tr>
            <tr>
                <td class="soustitre2">Temps Travail <em>(Non utilisé pour l'instant)</em></td>
                <td><input type="text" name="temps" value="0"></td>
            </tr>
            <tr>
                <td class="soustitre2">Cout en brouzoufs</td>
                <td><input type="text" name="pot_cout" value="0"></td>
            </tr>
            <tr>
                <td class="soustitre2">Résultat <em>(Non utilisé pour l'instant)</em></td>
                <td><input type="text" name="resultat" value="0"></td>
            </tr>
            <tr>
                <td class="soustitre2">Compétence</em></td>
                <td>
                    <select name="competence">
                        <?php
                        $s1 = '';
                        $s2 = '';
                        $s3 = '';
                        if ($comp == '1')
                        {
                            $s1 = 'selected';
                        } else if ($comp == '2')
                        {
                            $s2 = 'selected';
                        } else if ($comp == '3')
                        {
                            $s3 = 'selected';
                        }
                        ?>
                        <option value="97" <?php echo $s1 ?> >Alchimie Niveau 1</option>
                        ';
                        <option value="100" <?php echo $s2 ?> >Alchimie Niveau 2</option>
                        ';
                        <option value="101" <?php echo $s3 ?> >Alchimie Niveau 3</option>
                        ';
                    </select>
                    <em> <br>Par défaut, cela correspond au niveau de la potion.
                        <br>Mais on peut imaginer plusieurs formules pour une même potion, avec des compétences
                        différentes / <br><strong> Pas sûr que cela marche pour l'instant !</strong></em>

                </td>
            </tr>
            <tr>
            <td class="soustitre2">Potion concernée</em></td>
            <td>
            <select name="potion">
            <?php
            $req = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where (gobj_tobj_cod = 21 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37 or gobj_tobj_cod = 39)';
            require "blocks/_admin_enchantement_potions.php";

            break;
        case "ajout2":
            $req_form_cod = "select nextval('seq_frm_cod') as numero";
            $stmt = $pdo->query($req_form_cod);
            $result = $stmt->fetch();
            $num_form = $result['numero'];
            $req = 'insert into formule
								(frm_cod,frm_type,frm_nom,frm_temps_travail,frm_cout,frm_resultat,frm_comp_cod)
								values(' . $num_form . ',2,e\'' . pg_escape_string($_POST['nom']) . '\',' . $_POST['temps'] . ',' . $_POST['pot_cout'] . ',' . $_POST['resultat'] . ',' . $_POST['competence'] . ')';
            $stmt = $pdo->query($req);
            $req = 'insert into formule_produit
								(frmpr_frm_cod,frmpr_gobj_cod,frmpr_num)
								values(' . $num_form . ',' . $_POST['potion'] . ',' . $_POST['nombre'] . ')';
            $stmt = $pdo->query($req);
            echo "<p>La formule de base de la potion a bien été insérée !<br>
				Pensez à inclure les composants nécessaires pour cette potion.<br>";
            ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=serie_obj&pot=<?php echo $num_form; ?>">Modifier la
            liste des
            composants de la potion</a><br>
            <strong>Rappel des règles de création des formules :</strong>
            <br>Une potion de niveau 1 contient au moins 4 composants
            <br>Une potion de niveau 2 contient au moins 5 composants, dont deux identiques (ex : ABBCD ou ABBBB)
            <br>Une potion de niveau 3 contient au moins 7 composants, dont deux composants au moins en double ( (ex : ABBCDDE ou ABBBDDD)
            <br>
            <br>Chaque potion contient un élément "base", à prendre parmi les suivants, en plus des autres composants : Pissenlit de vin (Composant base), Léonine sucrée (Composant base), Herbe de Lune (Composant base)
            <br>
            <hr>
            <?php
            $action = get_request_var('action', '');
            if ($action == 'ajout')
            {
                $req  =
                    " insert into formule_composant (frmco_frm_cod,frmco_gobj_cod,frmco_num) values ($pot,$gobj,$nombre)";
                $stmt = $pdo->query($req);
            }
            if ($action == 'suppr')
            {
                $req  = " delete from formule_composant where frmco_frm_cod = $pot and frmco_gobj_cod = $comp_pot";
                $stmt = $pdo->query($req);
            }
            $req  = 'select frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom
				from formule_composant,objet_generique
				where frmco_frm_cod = ' . $num_form . '
				and frmco_gobj_cod = gobj_cod ';
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                echo '<br>' . $result['gobj_nom'] . ' (' . $result['frmco_num'] . ') - <a href="' . $_SERVER['PHP_SELF'] . '?methode=serie_obj&action=suppr&comp_pot=' . $result['frmco_gobj_cod'] . '&pot=' . $pot . '">Supprimer ?</a>';
            }
            ?>
            <br>Ajouter un objet :
            <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="serie_obj">
                <input type="hidden" name="action" value="ajout">
                <input type="hidden" name="pot" value="<?php echo $pot; ?>">
                <table>
                    <tr>
                        <td>Composant</td>
                        <td>Nombre de composants</td>
                    </tr>
                    <tr>
                        <td><select name="gobj">
                                <?php
                                $req  =
                                    "select gobj_cod,gobj_nom from objet_generique where (gobj_tobj_cod = 22 or gobj_tobj_cod = 28 or gobj_tobj_cod = 30 or gobj_tobj_cod = 34 or gobj_tobj_cod = 39) order by gobj_nom ";
                                $stmt = $pdo->query($req);
                                while ($result = $stmt->fetch())
                                    echo '<option value="' . $result['gobj_cod'] . '">' . $result['gobj_nom'] . '</option>';
                                ?>
                            </select></td>
                        <td><input type="text" name="nombre" value="1"></td>
                </table>
            <input type="submit" value="Ajouter"></form>
            <?php
            break;
        case "modif":
            require "blocks/_admin_enchantement_pot_comp_2.php";
            if ($s == '97')
            {
                $s1 = 'selected';
            } else if ($s == '100')
            {
                $s2 = 'selected';
            } else if ($s == '101')
            {
                $s3 = 'selected';
            }
            ?>
            <option value="97" <?php echo $s1 ?> >Alchimie Niveau 1</option>
            ';
            <option value="100" <?php echo $s2 ?> >Alchimie Niveau 2</option>
            ';
            <option value="101" <?php echo $s3 ?> >Alchimie Niveau 3</option>
            ';
            </select>
            </td>
            </tr>
            <tr>
                <td class="soustitre2">Potion concernée</em></td>
                <td>
                    <select name="potion">
                        <?php
                        $req_pot = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where (gobj_tobj_cod = 21 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37 or gobj_tobj_cod = 39) 
											order by gobj_nom';
                        $stmt    = $pdo->query($req_pot);
                        while ($result = $stmt->fetch())
                        {
                            $sel    = '';
                            $potion = $result['gobj_cod'];
                            if ($potion == $cod_pot)
                            {
                                $sel = "selected";
                            }
                            echo '<option value="' . $result['gobj_cod'] . '" ' . $sel . '> ' . $result['gobj_nom'] . '</option>';
                        }
                        echo '</select><br>'; ?>
                </td>
            </tr>
            <tr>
                <td class="soustitre2">Nombre de potions produites</em></td>
                <td><input type="text" name="nombre" value="<?php echo $result['frmpr_num']; ?>"></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" class="test" value="Valider"></td>
            </tr>


            </form>
            </table>
            <?php
            break;
        case "modif2":
            $req = 'update formule
								set frm_nom = e\'' . pg_escape_string($_POST['nom']) . '\',
								frm_temps_travail = ' . $_POST['temps'] . ',
								frm_cout =' . $_POST['pot_cout'] . ',
								frm_resultat = ' . $_POST['resultat'] . ',
								frm_comp_cod = ' . $_POST['competence'] . '
								where frm_cod = ' . $pot;
            $stmt = $pdo->query($req);
            $req  = 'update formule_produit
									set frmpr_gobj_cod = ' . $_POST['potion'] . ',
									frmpr_num = ' . $_POST['nombre'] . '
									where frmpr_frm_cod = ' . $pot;
            $stmt = $pdo->query($req);
            if ($_POST['competence'] == '97')
            {
                $comp = 1;
            } else if ($_POST['competence'] == '100')
            {
                $comp = 2;
            } else if ($_POST['competence'] == '101')
            {
                $comp = 3;
            }
            $req  =
                'update potions.fonction_potion set fpot_niveau = ' . $comp . ' where fpot_gobj_cod = ' . $_POST['potion'];
            $stmt = $pdo->query($req);
            echo "<p>La formule de base de la potion a bien été modifiée !<br>
							Vous pouvez aussi en modifier les composants.<br>";
            ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=serie_obj&pot=<?php echo $pot; ?>">Modifier la liste
            des
            composants de la potion</a><br>
            <?php
            break;
        case "serie_obj":
            ?>
            <strong>Rappel des règles de création des formules :</strong>
            <br>Une potion de niveau 1 contient au moins 4 composants
            <br>Une potion de niveau 2 contient au moins 5 composants, dont deux identiques (ex : ABBCD ou ABBBB)
            <br>Une potion de niveau 3 contient au moins 7 composants, dont deux composants au moins en double ( (ex : ABBCDDE ou ABBBDDD)
            <br>
            <br>Chaque potion contient un élément "base", à prendre parmi les suivants, en plus des autres composants : Pissenlit de vin (Composant base), Léonine sucrée (Composant base), Herbe de Lune (Composant base)
            <br>
            <hr>
            <?php
            $action = get_request_var('action', '');
            if ($action == 'ajout')
            {
                $req  =
                    " insert into formule_composant (frmco_frm_cod,frmco_gobj_cod,frmco_num) values ($pot,$gobj,$nombre)";
                $stmt = $pdo->query($req);
            }
            if ($action == 'suppr')
            {
                $req  = " delete from formule_composant where frmco_frm_cod = $pot and frmco_gobj_cod = $comp_pot";
                $stmt = $pdo->query($req);
            }
            $req  = 'select frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom
				from formule_composant,objet_generique
				where frmco_frm_cod = ' . $pot . '
				and frmco_gobj_cod = gobj_cod ';
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                echo '<br>' . $result['gobj_nom'] . ' (' . $result['frmco_num'] . ') - <a href="' . $_SERVER['PHP_SELF'] . '?methode=serie_obj&action=suppr&comp_pot=' . $result['frmco_gobj_cod'] . '&pot=' . $pot . '">Supprimer ?</a>';
            }
            ?>
            <br>Ajouter un objet :
            <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="serie_obj">
                <input type="hidden" name="action" value="ajout">
                <input type="hidden" name="pot" value="<?php echo $pot; ?>">
                <table>
                    <tr>
                        <td>Composant</td>
                        <td>Nombre de composants</td>
                    </tr>
                    <tr>
                        <td><select name="gobj">
                                <?php
                                $req  =
                                    "select gobj_cod,gobj_nom from objet_generique where (gobj_tobj_cod = 22 or gobj_tobj_cod = 28 or gobj_tobj_cod = 30 or gobj_tobj_cod = 34 or gobj_tobj_cod = 39) order by gobj_nom ";
                                $stmt = $pdo->query($req);
                                while ($result = $stmt->fetch())
                                    echo '<option value="' . $result['gobj_cod'] . '">' . $result['gobj_nom'] . '</option>';
                                ?>
                            </select></td>
                        <td><input type="text" name="nombre" value="1"></td>
                </table>
                <input type="submit" value="Ajouter"></form>
            <?php
            break;
    }
}
?>
    <p style="text-align:center;"><a href="<?php $_SERVER['PHP_SELF'] ?>?methode=debut ">Retour au début</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
