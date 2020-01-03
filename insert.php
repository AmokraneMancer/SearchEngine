<?php

// à exécuter dans phpmyadmin avant chaque mis à jour des tables
// ex: nouveaux fichiers
/*
	DELETE from mot;
	DELETE from document;
	DELETE from indexes;
*/
try
{
	$bdd = new PDO('mysql:host=localhost; dbname=moteur_recherche; charset=utf8', 'amokrane', 'amokrane');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}



require "biblio.inc.php";

// nombre de fichiers html à mettre en bdd
// les noms doivent etre de tyle file1, file2,....fileN.html
// ils sont à mettre dans le dossier html
$nombreFichier = 4;
$id = 1;
var_dump(isset($_POST['file']));
//boucle principale d'insertion
//for($j = 1 ; $j <= $nombreFichier ; $j++){
if(isset($_POST['file'])){
	
	//$file_path = 'html/file' . $j;
	$file_path = 'html/'.$_POST['file'];
	var_dump($file_path);
	$fichier_html = $file_path . ".html";
	$separateur = " \n“«.;,:!?“-’‘'()[]";
	$motvides = file_get_contents("mots_vides.txt");
	$motvides1 = explodebis($separateur, $motvides);
	//Extarction des Metas et title//////////////////////////////

	$titre = get_title($fichier_html);

	$description = getDescription($fichier_html);

	//var_dump($motvides1);

	$keywords = getKeywords($fichier_html);

	$chaine_texte_head = $titre . " " . $keywords . " " . $description;

	$string = strtolower($chaine_texte_head);

	$tab_elements = explodebis($separateur, $string);

	$tab_elements = array_diff($tab_elements, $motvides1);

	$element_count = array_count_values($tab_elements);

	// multiplier le poids * 2
	foreach($element_count as $key => $value){
		$key = explodebis($separateur, $key);
		$element_count[$key] = $value * 2;
	}

	//Extarction du body/////////////////////////////////////////

	$chaine_html_body = get_body($fichier_html);

	//Suppression des scripts

	$textnoScript = strip_scripts($chaine_html_body);

	//Conerssion des caractéres HTML en ASCII

	$chaine_texte_body = html_entity_decode($textnoScript);

	//Suppressipon des tags HTML

	$text_body = strip_tags($chaine_texte_body);

	$text_body = strtolower($text_body);

	$tab_elements_body = explodebis($separateur, $text_body);

	$tab_elements_body = array_diff($tab_elements_body, $motvides1);

	$element_count_body = array_count_values($tab_elements_body);

	// le tableau contenant les mots avec leure pondérations
	// mot => poids
	var_dump($mergedArray);
	$mergedArray = array();
	foreach (array_keys($element_count + $element_count_body) as $key) {
		$mergedArray[$key] = @($element_count[$key] + $element_count_body[$key]);
	}

	//insertion en bdd
	$req1 = 'INSERT INTO document(titre, description, file_path) VALUES(:titre, :description, :file_path)';
	$req2 = "INSERT INTO mot (mot) VALUES(:mot)";
	$req3 = "INSERT INTO indexes (id_mot, id_doc, poids) VALUES (:id_mot, :id_doc, :poids)";
	
	
	// document table
	
	$st1 = $bdd->prepare($req1);
	$st1->execute(array(
		//'id' => $j,
		'titre' => $titre,
		'description' => $description,
		'file_path' => $file_path
		));

	//mot table
	foreach ($mergedArray as $key => $value){
		$st2 = $bdd->prepare($req2);
		
		$st2->execute(array(
			'mot' => $key
			));
		//$id++;
	}

	//indexes table

	$req5 = "SELECT id FROM document WHERE file_path = '$file_path'";
	$st5 = $bdd->prepare($req5);
	$st5->execute();
	$resultForId = $st5->fetch(PDO::FETCH_ASSOC);
	var_dump($resultForId);
	$id_doc = $resultForId['id'];
	foreach($mergedArray as $key => $value){
		$st3 = $bdd->prepare($req3);
		// récupérer l'id du mot courant
		$req4 = "SELECT id FROM mot WHERE mot = '$key'";
		$st4 = $bdd->prepare($req4);
		$st4->execute();
		while($result = $st4->fetch(PDO::FETCH_ASSOC)){
			//insérer l'id récupéré de la table mot dans la table indexes
			if($result['id'] > 0){
				$st3->execute(array(
					'id_mot' => $result['id'],
					'id_doc' => $id_doc,
					'poids' => $value
				));
			}
		}
	}
}

header('Location: insert.html');
exit;
?>