<?php
    //récupération par méthodes post, pour récupérer les variables de la page d'avant. 
    if (isset($_POST["date"], $_POST["heure"], $_POST["depart1"], $_POST["arriver1"], $_POST["depart2"], $_POST["arriver2"], $_POST["nbpassager"])) {
        if($_POST["depart1"]==NULL){
            $adressdep = $_POST["depart2"];
            $adressarr = $_POST["arriver2"];
            echo "l'arriver est ". $adressarr ."<br>";
            echo "l'depart est ". $adressdep ."<br>";
        }
        else{
            $adressdep = $_POST["depart1"];
            $adressarr = $_POST["arriver1"];
            echo "l'arriver est". $adressarr ."<br>";
            echo "l'depart est". $adressdep ."<br>";
        }
        $date = $_POST["date"];
        $heure = $_POST["heure"];
        $nbpassager = $_POST["nbpassager"];
    
    
        // recherche de la longitude et de la latitude de l'arrivée et du départ 
        $adressdep = strtr($adressdep, ' ', '+');
        $adressarr = strtr($adressarr, ' ', '+');

        echo "l'arr est". $adressarr ."<br>";
        echo "l'ar est". $adressdep ."<br>";


        $url_api_adresse_dep = "https://api-adresse.data.gouv.fr/search/?q=$adressdep&limit=1";
        $url_api_adresse_arr = "https://api-adresse.data.gouv.fr/search/?q=$adressarr&limit=1";

        // Initialisation de CURL
        $curl_dep = curl_init();
        $curl_arr = curl_init();

        // Configuration de la requête CURL
        curl_setopt($curl_dep, CURLOPT_URL, $url_api_adresse_dep);
        curl_setopt($curl_dep, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl_arr, CURLOPT_URL, $url_api_adresse_arr);
        curl_setopt($curl_arr, CURLOPT_RETURNTRANSFER, true);

        // Exécution de la requête CURL
        $response_dep = curl_exec($curl_dep);
        $response_arr = curl_exec($curl_arr);

        echo $response_arr;



        // Vérification des erreurs CURL
        if (curl_errno($curl_dep) && curl_errno($curl_arr)) {
            echo 'Erreur CURL : ' . curl_error($curl_dep);
            echo 'Erreur CURL : ' . curl_error($curl_arr);
        } else {
            // Décoder la réponse JSON en un tableau associatif PHP
            $donnees_dep = json_decode($response_dep, true);
            $donnees_arr = json_decode($response_arr, true);
            // Vérifier si la réponse contient des données
            $depcity = $donnees_dep["features"][0]["properties"]["city"];
           echo $depcity;
            if (isset($donnees_dep['features'], $donnees_arr['features']) && !empty($donnees_dep['features']) && !empty($donnees_arr['features'])) {

                $depcity = $donnees_dep["features"][0]["properties"]["city"];
                echo $depcity;
                $arrcity = $donnees_arr["features"][0]["properties"]["city"];
                $geometry = $donnees_dep['features'][0]['geometry'];
                $latitude_dep = $geometry['coordinates'][1];
                $longitude_dep = $geometry['coordinates'][0];
                $geometry = $donnees_arr['features'][0]['geometry'];
                $latitude_arr = $geometry['coordinates'][1];
                $longitude_arr = $geometry['coordinates'][0];
                $tab_coord = [[$longitude_dep, $latitude_dep], [$longitude_arr, $latitude_arr]];
            } else {
                echo "Aucune adresse trouvée.";
            }
        }

        // Fermer la session CURL
        curl_close($curl_dep);
        curl_close($curl_arr);

    }
    else {
        echo "Les champs 'date', 'heure', 'arriver', 'depart', 'nbpassager' n'ont pas été soumis.";
    }
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="create-price.js" defer></script>
    <link rel="stylesheet" href="create-trip.css">
    <link rel="stylesheet" href="../css/style-main-structure.css">
    <title>Définir un prix</title>
</head>
<body>
    <header class="">
        <h1>Saisie du prix</h1>
    </header>
    <br><br>
    <main>
    <form action="create-trip-donnee.php" method="post" >
    <nav class="centrer modele-container">
        <span class="text1"></span>
        <h3 class="text2" >Prix (en €): <output id="respondprix"><output></h3>
        <span class="text3"></span>
        <div class="select">
            <input type="range" name="prix" id="Prix" placeholder="Prix" class="form-input ml-2" required="required" min="0" max="100" step="0.5">           
        </div>  
    </nav>
    <input type="hidden" name="date" value="<?php echo $date;?>" >
    <input type="hidden" name="heure" value="<?php echo $heure;?>" >
    <input type="hidden" name="arriver" value="<?php echo $adressarr;?>" >
    <input type="hidden" name="depart" value="<?php echo $adressdep;?>" >
    <input type="hidden" name="nbpassager" value="<?php echo $nbpassager;?>" >
    <br><br>
        <input class="styled" type="submit" value="Validé" id="valideprix"></input>
    </form>

    <?php
    $url_api_adresse = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=5b3ce3597851110001cf6248734c41a8117a44f6839360a0e5bbe9f9&start=$longitude_user,$latitude_user&end=$longitude_trip,$latitude_trip";
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url_api_adresse);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Erreur CURL : ' . curl_error($curl);
            } else {
                // Décoder la réponse JSON en un tableau associatif PHP
                $donnees = json_decode($response, true);
                if (isset($donnees["features"]) && !empty($donnees["features"])) {
                $distance = $donnees["features"][0]["properties"]["segments"][0]["distance"];
                $tab_distance[] = $distance;
                }
            }

            curl_close($curl);
    ?>
    <?php include "../trip-finding/php/trip-ressources/leaflet.php";?>
    <main>
</body>
</html>