<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 19/12/18
 * Time: 18:39
 */
$perso = new perso;
$perso->charge($perso_cod);
if ($perso->compte_objet(86) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
    $erreur = 1;
}
if ($perso->compte_objet(87) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
    $erreur = 1;
}
if ($perso->compte_objet(88) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
    $erreur = 1;
}