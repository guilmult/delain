﻿<?php
include "blocks/_tests_appels_page_externe.php";


$type_lieu = 37;
$nom_lieu  = 'une sortie de donjon';

include "blocks/_test_lieu.php";

$perso = new perso;
$perso->charge($perso_cod);


if ($erreur == 0)
{
    $tab_lieu  = $perso->get_lieu();
    $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu = $tab_lieu['lieu']->lieu_description;
    echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");
    echo("<p>Vous voyez la sortie de ce donjon.");
    echo("<p><a href=\"action.php?methode=sortir_donjon\">Prendre la sortie ! (4PA)</a></p>");
}
