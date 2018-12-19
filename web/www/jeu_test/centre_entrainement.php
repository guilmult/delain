<?php 
include_once "verif_connexion.php";

$type_lieu = 6;
$nom_lieu = 'un centre d\'entraînement';

include "blocks/_test_lieu.php";

// on regarde si le joueur est bien sur un centre d'entrainement

if ($erreur == 0)
{
	echo("<p>Vous entrez dans un centre d'entrainement. Vous pourrez ici améliorer vos compétences nécessaires au combat, moyennant finances, bien sur...
	<br>Au delà d'une certaine expertise dans votre compétence, nous ne pourrons plus vous aider. Il faudra vous entrainer en conditions réelles<br /><br />");
	$req_typc = "select typc_cod,typc_libelle from type_competences where typc_cod in (2,6,7,8,19) ";

	$db->query($req_typc);
	?>
	<form name="amelioration_comp" method="post" action="amel_centre_entrainement.php">
	<input type="hidden" name="comp_cod">
	<input type="hidden" name="prix">
	<table>
	<tr>
	<td class="soustitre2"><p>Compétence</td>
	<td class="soustitre2"><p>Valeur actuelle</td>
	<td class="soustitre2"><p>Prix de l'amélioration</td>
	<td class="soustitre2"><p>Amélioration</td>
	<td class="soustitre2"></td>
	</tr>
	<?php 
	while($db->next_record())
	{
		printf("<tr><td colspan=\"5\" class=\"titre\"><p class=\"titre\">%s</td></tr>",$db->f("typc_libelle"));
		$typc_cod = $db->f("typc_cod");
		$req_comp = "select comp_cod,typc_libelle,comp_libelle,pcomp_modificateur from perso_competences,competences,type_competences
											where pcomp_perso_cod = $perso_cod
											and pcomp_pcomp_cod = comp_cod
											and comp_typc_cod = typc_cod
											and typc_cod = $typc_cod
											order by comp_libelle ";
		$db_comp = new base_delain;
		$db_comp->query($req_comp);
		while($db_comp->next_record())
		{
			echo("<tr>");
			$score = $db_comp->f("pcomp_modificateur");
			printf("<td class=\"soustitre2\"><p>%s</td>",$db_comp->f("comp_libelle"));
			printf("<td><p style=\"text-align:right;\">%s", $score);
			echo(" %</td>");
			$prix = 4 * $score;
			if ($score <= 25)
			{
				$amel = '1D4';
				$pa = 1;
			}
			if (($score > 25) && ($score <= 50 ))
			{
				$amel = '1D3';
				$pa = 1;
			}
			if (($score > 50) && ($score <= 75 ))
			{
				$amel = '1D2';
				$pa = 2;
			}
			if (($score > 75) && ($score < 85 ))
			{
				$amel = '1';
				$pa = 3;
			}
			if ($score >= 85)
			{
				$amel = "Pas d'amélioration possible";
			}
			echo("<td><p style=\"text-align:right;\">$prix brouzoufs</td>");



			echo("<td><p>$amel</td>");
			echo("<td>");
			if ($score < 85)
			{
				printf("<p><a href=\"javascript:document.amelioration_comp.comp_cod.value=%s;document.amelioration_comp.prix.value=$prix;document.amelioration_comp.submit();\">S'entrainer ! ($pa PA)</a>",$db_comp->f("comp_cod"));
			}
			echo("</td>");
			echo("</tr>");
		}
	}
	echo("</table>");
	echo("</form>");
}
include "quete.php";

?>
