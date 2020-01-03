<html>
<head>
	<title><?php if(isset($_GET['name'])) echo "Moteur de Recherche - " . $_GET['name'];
					 else echo "Moteur de Recherche"; ?></title>
	<link rel="stylesheet" href="style.css" type="text/css">
	<meta charset="utf-8">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js">
	</script>
	<script src="https://www.amcharts.com/lib/4/core.js"></script>
	<script src="https://www.amcharts.com/lib/4/charts.js"></script>
	<script src="https://www.amcharts.com/lib/4/plugins/wordCloud.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
	<script src="js/chartFile.js"></script>
</head>
<body>

<?php
	// récupérer le mot depuis index
	if (isset($_GET['name'])) {
		$name = $_GET['name'] ;
?>
	<!-- formulaire pour éfectuer une nouvelle recherche -->
	<form name="addframe" method="get" action="search.php">
	Nouvelle recherche:
	<input type="text" name="name" value="<?php echo $name; ?>" > 
	<input type="submit" name="rechercher" value="rechercher">
	</form> 
<?php

	try{
		$bd = new PDO('mysql:host=localhost; dbname=moteur_recherche; charset=utf8', 'amokrane', 'amokrane');
	}catch (Exception $e){
			die('Erreur : ' . $e->getMessage());
	}
	// récupérer tout les documents ou se trouve le mot recherché
	// à l'aide d'un jointure entre les 3 tables
	$query = "SELECT * FROM mot m LEFT JOIN 
			indexes i ON m.id = i.id_mot LEFT JOIN document d ON
			i.id_doc = d.id WHERE m.mot = '$name' ORDER BY i.poids";
	//var_dump($name);
	$st = $bd->prepare($query);
	$st->execute();
	$result = $st->fetchAll(PDO::FETCH_ASSOC);
	// compteur pour les ids des <div> chart
	$count =0;
	//var_dump($result);
	echo "<ul>";
	foreach($result as $key => $value){
		$desc = strlen($value['description']) > 250 ? substr($value['description'],0,250)."..." : $value['description'];
		echo "<li><a href=\"".$value['file_path'].".html".
		"\"><strong>".$value['titre']."</strong></a><b> 
		(".$value['poids'].")</b></br>".
		"<p>".$desc." <a href=\"#\" class=\"nuage\"  id=\"nuage".$count
		."\">nuage+</a></p><div class=\"chartdiv\" id=\"".$count."\"></div></li>";
		

		$idDoc = $value['id_doc'];
		// on récupère les mots clés triés par leur poids
		$queryForWords = "SELECT m.mot, i.poids FROM indexes i LEFT JOIN mot m 
		ON i.id_mot = m.id WHERE i.id_doc = '$idDoc' ORDER BY poids DESC";
		$stForWords = $bd->prepare($queryForWords);
		$stForWords->execute();

		// la chaine à transmettre au script js
		$data = "";
		$countWords = 0;
		while($resultForWords = $stForWords->fetch(PDO::FETCH_ASSOC)){ 
			// on écrit le mot "poids" fois pou afficher
			// son poids dans chart
			for($j=0 ; $j < $resultForWords['poids']; $j++)
				$data .= $resultForWords['mot']." "; 
			// on récupère 100 mots clés
			if($countWords > 100)
				break;
			$countWords++;
		}
		// transmettre data au script js
		echo "<script> data.push( " . json_encode($data) . ");</script>";
		$count++;
	}
	echo "</ul>";
	}

	?>

</body>
</html>