<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:23
 */
$verif_connexion::verif_appel();

$erreur = 0;
$req = "select perso_admin_echoppe from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
else
{
    $result = $stmt->fetch();
}
if ($result['perso_admin_echoppe'] != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
