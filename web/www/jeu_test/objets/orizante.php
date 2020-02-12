<?php 
include "../verif_connexion.php";


$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$db = new base_delain;

$req_matos = "select perobj_obj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 665 order by perobj_obj_cod";
$db->query($req_matos);
if (!($db->next_record()))
{
  // PAS D'OBJET.
    $contenu_page .= "<p>Vous ne portez pas le crâne d'orizante!'</p>";
}
else
{
$erreur = 0;
    $req = 'select obj_nom, obj_gobj_cod,  obj_cod, obj_poids, obj_description, trouve_objet(obj_cod) as trouve from objets where obj_gobj_cod between 665 and 687  and obj_cod not between 7994364 and 7994807 order by obj_cod';
    $db->query($req);
    $contenu_page .= '<table border = 1><tr><th>Nom</th><th>Type</th><th>Numéro</th><th>Poids</th><th>Position</th><th>Description</th></tr>';
    while ($db->next_record())
    {
        $contenu_page .= '<tr><td>' . $db->f('obj_nom') . '</td><td>' . $db->f('obj_gobj_cod') . '</td><td>' . $db->f('obj_cod') . '</td><td>' . $db->f('obj_poids') . '</td><td>' . $db->f('trouve') . '</td><td>' . $db->f('obj_description') . '</td></tr>';
    }
    $contenu_page .= '</table>';
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));