<?php
include "blocks/_header_page_jeu.php";
ob_start();
//$mod_perso_cod = 2;
$erreur = 0;
//
// verif droits
//

$droit_modif        = 'dcompt_enchantements';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    // initialisation de la méthode
    $methode           = get_request_var('methode', 'debut');

    switch ($methode)
    {
        case "debut":
            ?>
            <table>
                <tr>
                    <td>
                        <?php $req = 'select enc_cod,enc_nom,enc_description from enchantements order by enc_nom ';
                        $stmt      = $pdo->query($req);
                        $req       = "select oenc_gobj_cod,oenc_nombre,gobj_nom from enc_objets,objet_generique	
														where oenc_enc_cod = :cod
														and oenc_gobj_cod = gobj_cod";
                        $stmt2     = $pdo->prepare($req);
                        echo '<td class="titre">Enchantements disponibles :</td><br><br>
						<table>
						<td><strong>Nom de l\'enchantement</strong></td><td><strong>Objets nécessaires et quantités</strong></td><td><strong>Description</strong></td>';
                        while ($result = $stmt->fetch())
                        {
                            $cod_enchantement = $result['enc_cod'];
                            echo '<tr><td class="soustitre2"><br><a href="' . $_SERVER['PHP_SELF'] . '?methode=modif&enc=' . $cod_enchantement . '">' . $result['enc_nom'] . '</a></td>';


                            $stmt2 = $pdo->execute(array(":cod" => $cod_enchantement), $stmt2);
                            echo "<td>";
                            while ($result2 = $stmt->fetch())
                            {
                                echo $result2['gobj_nom'] . " \t" . $result2['oenc_nombre'] . "<br>";
                            }
                            echo "</td><td class=\"soustitre2\">" . $result['enc_description'] . "</td></tr>";

                        }
                        ?>
            </table>
            <hr><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=ajout">Ou ajouter un nouvel enchantement</a>
            </td>
            <td><?php
                require 'admin_enchantement_composant.php';
                ?></td></table>
            <?php
            break;
        case "ajout":
            ?>
            <table>
                <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="methode" value="ajout2">
                    <tr>
                        <td class="soustitre2">Nom de l'enchantement</td>
                        <td><input type="text" size="50" name="nom"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Description</td>
                        <td><textarea cols="50" rows="10" name="description"></textarea></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Cout en brouzoufs</td>
                        <td><input type="text" name="enc_cout"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Cout en PA</td>
                        <td><input type="text" name="enc_cout_pa"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus degats (entier)</td>
                        <td><input type="text" name="enc_degat"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus armure (entier)</td>
                        <td><input type="text" name="enc_armure"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus portée (entier)</td>
                        <td><input type="text" name="enc_portee"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus chute (réél)</td>
                        <td><input type="text" name="enc_chute"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus usure (réél, multiplicateur)</td>
                        <td><input type="text" name="enc_usure"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus vampirisme (réél)</td>
                        <td><input type="text" name="enc_vampirisme"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus régénération (entier)</td>
                        <td><input type="text" name="enc_regen"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus aura de feu (réél)</td>
                        <td><input type="text" name="enc_aura_feu"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Protection des critiques (entier > 0, en %)</td>
                        <td><input type="text" name="enc_critique"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus seuil force (entier)</td>
                        <td><input type="text" name="enc_seuil_force"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus seuil dextérité (entier)</td>
                        <td><input type="text" name="enc_seuil_dex"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus vue (entier)</td>
                        <td><input type="text" name="enc_vue"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus chance drop (réél, multiplicateur)</td>
                        <td><input type="text" name="enc_chance_drop"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus poids (réél, multiplicateur)</td>
                        <td><input type="text" name="enc_poids"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="centrer"><input type="submit" class="test" value="Valider"></div>
                        </td>
                    </tr>


                </form>
            </table>
            <?php
            break;
        case "ajout2":
            $fields = array(
                'enc_degat',
                'enc_armure',
                'enc_portee',
                'enc_chute',
                'enc_usure',
                'enc_regen',
                'enc_vampirisme',
                'enc_aura_feu',
                'enc_critique',
                'enc_seuil_force',
                'enc_seuil_dex',
                'enc_vue',
                'enc_chance_drop',
                'enc_poids',
                'enc_cout_pa',
                'enc_cout'
            );
            $req    = 'insert into enchantements
				(enc_nom,enc_description';
            foreach ($fields as $i => $value)
                $req .= ',' . $fields[$i];
            $req    .= ') values (e\'' . pg_escape_string($_POST['nom']) . '\',e\'' . pg_escape_string($_POST['description']) . '\'';
            $fields = array(
                'enc_degat',
                'enc_armure',
                'enc_portee',
                'enc_chute',
                'enc_vampirisme',
                'enc_usure',
                'enc_regen',
                'enc_aura_feu',
                'enc_critique',
                'enc_seuil_force',
                'enc_seuil_dex',
                'enc_vue',
                'enc_chance_drop',
                'enc_poids',
                'enc_cout_pa',
                'enc_cout'
            );
            foreach ($fields as $i => $value)
                $req .= ',' . $_POST[$fields[$i]];
            $req  .= ')';
            $stmt = $pdo->query($req);
            echo "<p>L'enchantement a bien été inséré !<br>
				pensez à inclure les objets nécessaires pour cet enchantement.<br>";
            break;
        case "modif":
            $req = 'select * from enchantements where enc_cod = :enc';
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":enc" => $enc), $stmt);
            $result = $stmt->fetch();
            ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=serie_obj&enc=<?php echo $enc; ?>">Modifier la liste
                d'objets</a>
            <br>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=compat&enc=<?php echo $enc; ?>">Modifier les
                compatibilités</a>
            <br>
            <table>
                <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="methode" value="modif2">
                    <input type="hidden" name="enc" value="<?php echo $enc; ?>">
                    <tr>
                        <td class="soustitre2">Nom de l'enchantement</td>
                        <td><input type="text" size="50" name="nom" value="<?php echo $result['enc_nom']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Description</td>
                        <td><textarea cols="50" rows="10"
                                      name="description"><?php echo $result['enc_description']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Cout en brouzoufs</td>
                        <td><input type="text" name="enc_cout" value="<?php echo $result['enc_cout']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Cout en PA</td>
                        <td><input type="text" name="enc_cout_pa" value="<?php echo $result['enc_cout_pa']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus degats (entier)</td>
                        <td><input type="text" name="enc_degat" value="<?php echo $result['enc_degat']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus armure (entier)</td>
                        <td><input type="text" name="enc_armure" value="<?php echo $result['enc_armure']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus portée (entier)</td>
                        <td><input type="text" name="enc_portee" value="<?php echo $result['enc_portee']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus chute (réél)</td>
                        <td><input type="text" name="enc_chute" value="<?php echo $result['enc_chute']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus usure (réél, multiplicateur)</td>
                        <td><input type="text" name="enc_usure" value="<?php echo $result['enc_usure']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus vampirisme (réél)</td>
                        <td><input type="text" name="enc_vampirisme" value="<?php echo $result['enc_vampirisme']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus régénération (entier)</td>
                        <td><input type="text" name="enc_regen" value="<?php echo $result['enc_regen']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus aura de feu (réél)</td>
                        <td><input type="text" name="enc_aura_feu" value="<?php echo $result['enc_aura_feu']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Protection des critiques (entier > 0, en %)</td>
                        <td><input type="text" name="enc_critique" value="<?php echo $result['enc_critique']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus seuil force (entier)</td>
                        <td><input type="text" name="enc_seuil_force" value="<?php echo $result['enc_seuil_force']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus seuil dextérité (entier)</td>
                        <td><input type="text" name="enc_seuil_dex" value="<?php echo $result['enc_seuil_dex']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus vue (entier)</td>
                        <td><input type="text" name="enc_vue" value="<?php echo $result['enc_vue']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus chance drop (réél, multiplicateur)</td>
                        <td><input type="text" name="enc_chance_drop" value="<?php echo $result['enc_chance_drop']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Bonus poids (réél, multiplicateur)</td>
                        <td><input type="text" name="enc_poids" value="<?php echo $result['enc_poids']; ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="centrer"><input type="submit" class="test" value="Valider"></div>
                        </td>
                    </tr>


                </form>
            </table>
            <?php
            break;
        case "modif2":
            $fields = array(
                'enc_degat',
                'enc_armure',
                'enc_portee',
                'enc_chute',
                'enc_vampirisme',
                'enc_usure',
                'enc_regen',
                'enc_aura_feu',
                'enc_critique',
                'enc_seuil_force',
                'enc_seuil_dex',
                'enc_vue',
                'enc_chance_drop',
                'enc_poids',
                'enc_cout_pa',
                'enc_cout'
            );
            $req    = 'update enchantements
					set enc_nom = e\'' . pg_escape_string($nom) . '\',enc_description = e\'' . pg_escape_string($description) . '\'';
            foreach ($fields as $i => $value)
                $req .= ',' . $fields[$i] . '=' . $_POST[$fields[$i]];
            $req  .= ' where enc_cod = :enc';
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":enc" => $enc), $stmt);
            echo "<p>L'enchantement a bien été modifié !<br>
				pensez à inclure les objets nécessaires pour cet enchantement.<br>";
            break;
        case "serie_obj":
            $action = get_request_var('action','');
            if ($action == 'ajout')
            {
                $req  = " insert into enc_objets (oenc_enc_cod,oenc_gobj_cod,oenc_nombre) 
                    values (:enc,:gobj,:nombre)";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":enc"    => $enc,
                                            ":gobj"   => $gobj,
                                            ":nombre" => $nombre), $stmt);
            }
            if ($action == 'suppr')
            {
                $req  = " delete from enc_objets where oenc_cod = :oenc ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":oenc" => $oenc), $stmt);
            }
            $req  = 'select oenc_cod,gobj_nom,oenc_nombre
				from enc_objets,objet_generique
				where oenc_enc_cod = :enc
				and oenc_gobj_cod = gobj_cod ';
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":enc" => $enc), $stmt);
            while ($result = $stmt->fetch())
            {
                echo '<br>' . $result['gobj_nom'] . ' (' . $result['oenc_nombre'] . ') - <a href="' . $_SERVER['PHP_SELF'] . '?methode=serie_obj&action=suppr&oenc=' . $result['oenc_cod'] . '&enc=' . $enc . '">Supprimer ?</a>';
            }
            ?>
            <hr>Ajouter un objet :
            <form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="serie_obj">
                <input type="hidden" name="action" value="ajout">
                <input type="hidden" name="enc" value="<?php echo $enc; ?>">
                <select name="gobj">
                    <?php
                    $req  = "select gobj_cod,gobj_nom from objet_generique order by gobj_nom ";
                    $stmt = $pdo->query($req);
                    while ($result = $stmt->fetch())
                    {
                        echo '<option value="' . $result['gobj_cod'] . '">' . $result['gobj_nom'] . '</option>';
                    }

                    ?>
                </select><input type="text" name="nombre"> <input type="submit" value="Ajouter"></form>
            <?php
            break;
        case "compat":
            $req = 'select * from enc_type_objet  where tenc_enc_cod = :enc';
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":enc" => $enc), $stmt);
            if (!$result = $stmt->fetch())
            {
                $stmt = $pdo->prepare('insert into enc_type_objet (tenc_enc_cod) values (:enc)');
                $stmt = $pdo->execute(array(":enc" => $enc), $stmt);
            }
            ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="enc" value="<?php echo $enc; ?>">
                <input type="hidden" name="methode" value="compat2">
                Entrez les types d'objets pour lequel cet enchantement sera disponible (1 = disponible, 0 = non
                disponible).<br>
                <table>
                    <tr>
                        <td class="soustitre2">Arme de contact</td>
                        <td><input type="text" name="tenc_arme_contact"
                                   value="<?php echo $result['tenc_arme_contact']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Arme à distance</td>
                        <td><input type="text" name="tenc_arme_distance"
                                   value="<?php echo $result['tenc_arme_distance']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Casque</td>
                        <td><input type="text" name="tenc_casque" value="<?php echo $result['tenc_casque']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Armure</td>
                        <td><input type="text" name="tenc_armure" value="<?php echo $result['tenc_armure']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Artefact</td>
                        <td><input type="text" name="tenc_artefact" value="<?php echo $result['tenc_artefact']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="centrer"><input type="submit" class="test" value="Valider"></div>
                        </td>
                    </tr>
                </table>
            </form>
            <?php
            break;
        case "compat2":
            $fields = array(
                'tenc_arme_contact',
                'tenc_arme_distance',
                'tenc_casque',
                'tenc_armure',
                'tenc_artefact'
            );
            $req    = 'update enc_type_objet
					set tenc_enc_cod = :enc ';
            foreach ($fields as $i => $value)
                $req .= ',' . $fields[$i] . '=' . $_POST[$fields[$i]];
            $req  .= ' where tenc_enc_cod = :enc';
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":enc" => $enc), $stmt);
            echo "Les compatibilités sont bien réglées.";
            break;
    }
}
?>
    <p class="centrer"><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Retour au début</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
