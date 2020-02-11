<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
// on cherche la guilde dans laquelle est le joueur
$req_guilde =
    "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
$req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
$stmt       = $pdo->query($req_guilde);
if ($stmt->rowCount() == 0)
{
    echo "<p>Erreur ! Vous n'êtes affilié à aucune guilde !";
    $erreur = 1;
}
$result     = $stmt->fetch();
$num_guilde = $result['guilde_cod'];
$perso      = new perso;
$perso->charge($perso_cod);
$autorise = false;
$pguilde  = new guilde_perso();
if ($pguilde->get_by_perso($perso_cod))
{
    $guilde_cod = $pguilde->pguilde_guilde_cod;
    $guilde     = new guilde;
    $guilde->charge($guilde_cod);

}
// on regarde les détails de la révolution
$grev = new guilde_revolution();
if (!$grev->getByGuilde($guilde->guilde_cod))
{
    echo "<p>Aucune révolution en cours pour votre guilde.";
    $erreur = 1;
}
$req_lanceur = "select * from v_revguilde where guilde = $num_guilde ";
$stmt        = $pdo->query($req_lanceur);
?>
    <form name="revolution" method="post" action="vote_revguilde.php">
    <input type="hidden" name="revguilde_cod">
    <input type="hidden" name="visu">
    <table>
        <tr>
            <td class="soustitre2"><p><strong>Lanceur</strong></td>
            <td class="soustitre2"><p><strong>Cible</strong></td>
            <td class="soustitre2"><p><strong>Votes pour le lanceur</strong></td>
            <td class="soustitre2"><p><strong>Votes contre le lanceur</strong></td>
            <td class="soustitre2"><p><strong>Date de fin</strong></td>
            <td></td>
        </tr>
        <?php
        while ($result = $stmt->fetch())
        {
            $pour_oui = round((($result['oui'] / $result['nb_membres']) * 100), 2);
            $pour_non = round((($result['non'] / $result['nb_membres']) * 100), 2);
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.revolution.action='visu_desc_perso.php';document.revolution.visu.value=" . $result['code_lanceur'] . ";document.revolution.submit()\">" . $result['nom_lanceur'] . "</A></td>";
            echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.revolution.action='visu_desc_perso.php';document.revolution.visu.value=" . $result['code_cible'] . ";document.revolution.submit()\">" . $result['nom_cible'] . "</a></td>";
            echo "<td><p>" . $result['oui'] . " (" . $pour_oui . "%)</td>";
            echo "<td><p>" . $result['non'] . " (" . $pour_non . "%)</td>";
            echo "<td><p>" . $result['date_fin'] . "</td>";
            echo "<td>";
            // on regarde si la personne peut voter
            $req2  = "select vrevguilde_cod from guilde_revolution_vote ";
            $req2  = $req2 . "where vrevguilde_revguilde_cod = " . $result['code_rev'] . " ";
            $req2  = $req2 . "and vrevguilde_perso_cod = $perso_cod ";
            $stmt2 = $pdo->query($req2);
            if ($stmt2->rowCount() == 0)
            {
                echo "<p><a href=\"javascript:document.revolution.revguilde_cod.value=" . $result['code_rev'] . ";document.revolution.submit()\">Voter !</a>";
            }
            echo "</td>";
        }
        ?>
    </table>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
