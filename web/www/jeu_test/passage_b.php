<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");

include_once "verif_connexion.php";
$perso = new perso;
$perso->charge($perso_cod);
$tab_lieu = $perso->get_lieu();
echo "<p><strong>" . $tab_lieu['lieu']->lieu_nom . "</strong> - " . $tab_lieu['lieu']->lieu_description;
echo "<p><em>" . $tab_lieu['description'];
