<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Page de gestion</title>
        <meta charset="utf-8" />
        <style>
			body{padding:3%;}
            h1{text-align:center;}
            h2{color:red;}
			table,td,th{border: solid; border-collapse:collapse;text-align:center;}
        </style>
    </head>
    <body>
		<h1>Gestion des comptes</h1>
		
		<?php
			try {
                require("connexion.php"); 
               
				$reqPrep="SELECT * FROM utilisateur";//La requere SQL SELECT
                $req = $conn->prepare($reqPrep);//Préparer la requete
                $req->execute();//Executer la requete

                $resultat = $req->fetchAll(PDO::FETCH_ASSOC);//récupérer le résultat
                
                // Affichage sous forme d'un tableau
                echo "<table>
                            <tr>
                                <th>id</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Date de naissance</th>
                                <th colspan=2>Action</th> 
                            </tr>";
                
                foreach ($resultat as $row) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nom']}</td>
                            <td>{$row['prenom']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['date_naissance']}</td>
                            <td>{$row['adresse']}</td>
                            <td>{$row['admin']}</td>
                            <td><a href='modifier.php?identifiant={$row['id']}'>Modifier</a></td>
                            <td><a href='supprimer.php?identifiant={$row['id']}'>Supprimer</a></td>
                          </tr>";
                }
                
                echo "</table>";
                
                $conn = null; // On ferme la connexion
            }                 
            catch(Exception $e) {
                echo "Erreur : " . $e->getMessage();
            }
		?>

		<!-- Formulaire d'ajout -->
		<h2>Ajouter un adhérent</h2>
		<form name="ajout" action="ajouter.php" method="post">
			<fieldset>
				<legend>Ajouter un adhérent</legend>
				
				<label for="nom">Nom : </label>
				<input type="text" id="nom" name="nom"><br/>
				
				<label for="prenom">Prénom : </label>
				<input type="text" id="prenom" name="prenom"><br/>
				
				<label for="email">Email : </label>
				<input type="email" id="email" name="email"><br/>
				
				<label for="dateN">Date de naissance : </label>
				<input type="date" id="dateN" name="dateN"><br/>
				
				<input Type="submit" name="Ajouter" value="Ajouter">
			</fieldset>
		</form>
		
    </body>
</html>