<?php
session_start();
?>
<!DOCTYPE>
<html lang="de">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Hallo?</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>

    <?php


	$formular_anzeigen = true;

	if (isset($_GET['submit'])) {

		if (!isset($_GET['sortierung'])){
			$sortspalte = 'artikelnummer';
		}else{
			$sortspalte = $_GET['sortierung']; //z.B. die Spalte Artikelbezeichnung
		}
		$suchwort = $_GET['suchwort']; //z.B. Pullover
	    $erlaubte_spalten = array('artikelnummer', 'artikelbezeichnung', 'preis');
		
		if(!in_array($sortspalte, $erlaubte_spalten)) {
		   die('Ungültiger Parameter für $suchwort');
		}

		try {
			$dsn = "mysql:host=localhost;dbname=webshop;charset=utf8";
			$pdo = new PDO($dsn,'root','');
		} catch ( PDOException $e ) {
			  die ( "Es ist ein Fehler beim Verbindungsaufbau zur Datenbank aufgetreten!" );
		}
	
		try {
			//SQL-Stmt vorbereiten
			$statement = $pdo->prepare("SELECT * FROM w_artikel WHERE artikelbezeichnung LIKE :suchwort ORDER BY :sortspalte");
			//Variablen einzeln binden
			$statement->bindParam(':sortspalte',$sortspalte);
			//dafür sogen, dass vor oder nach dem Suchwort weitere Zeichen kommen können
			$imTextSuchen = "%$suchwort%";
			$statement->bindParam(':suchwort', $imTextSuchen);
			$statement->execute();
			//oder binden und ausführen in einem Schritt
			//$statement->execute(array(':suchwort' => "%$suchwort%", ':suchspalte' => $suchspalte));   
		} catch ( PDOException $e ) {
			die ( "Es ist ein Fehler bei der Verarbeitung der Anfrage aufgetreten!" );
		}
		
		if($statement->rowCount() > 0){
		//Tabelle mit dem ResultSet aufbauen
		?>
			<table border=1>
			<th>Artikelnummer</th>
			<th>Artikelbezeichnung</th>
			<th>Preis</th>
		<?php
			//immer eine Zeile aus dem ResultSet holen und ausgeben
			while($row = $statement->fetch()) {
				echo "<tr><td>".$row['artikelnummer']."</td>";
				echo "<td>".$row['artikelbezeichnung']."</td>";
				echo "<td>".$row['preis']."</td></tr>";
			}
			echo "</table>";
		 /*
		//oder das ganze ResultSet holen und mit foreach durchlaufen
		$result = $statement->fetchAll();
		
		echo "<ul>";
		foreach ($result as $row) {
			echo "<li>".
				  $row['artikelnummer'] .
				  $row['artikelbezeichnung'].
				  $row['preis'] . " Euro" .
				  "</li>";
		} 
		echo "</ul>";
		echo "<hr>";
		*/	

		}else{
			echo "Keine passenden Artikel in der Datenbank!";
		}
		$formular_anzeigen = false;
	}

	if ($formular_anzeigen == true) {


	?>

    <h2>Artikelsuche</h2>
    <p>Geben Sie den gewünschten Artikel ein:</p>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">

        <label for="suchwort">Artikel:</label>
        <input id="suchwort" type="text" name="suchwort" required /></br></br>
		<label for="sortierung">Sortierung aufsteigend nach:</label>
		<select name="sortierung">
			<option value="artikelnummer">Artikelnummer</option>
			<option value="artikelbezeichnung">Artikelbezeichnung</option>
			<option value="preis">Preis</option>
		</select>
        <input type="submit" value="Senden" name="submit" />

    </form>

    <?php
	} //end if 
	?>

</body>

</html>