<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <script language="javascript" src="javascripts/changestyles.js"></script>
<?php
$erreur = 0;
if (!isset($_REQUEST['methode']))
{
    $methode = "debut";
} else
{
    $methode = $_REQUEST['methode'];
}
$perso = new perso;
$perso->charge($perso_cod);
if ($perso->perso_admin_echoppe != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            $type_lieu = '21';
            require "_admin_echoppe_stats_debut.php";
            break;
        case "stats":
            require "_admin_echoppe_stats_stats.php";
            break;
        case "stats2":
            require "_admin_echoppe_stats_stats2.php";
            break;
    }


}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
