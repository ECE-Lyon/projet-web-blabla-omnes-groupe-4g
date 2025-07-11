<!-- page qui permet de mettre un prix sur un trajet  -->

<?php
require "../php/config.php";

if (isset($_POST["date"], $_POST["heure"], $_POST["depart1"], $_POST["arriver1"], $_POST["depart2"], $_POST["arriver2"], $_POST["nbpassager"])) {
    if ($_POST["depart1"] == NULL) {
        $adressdep = $_POST["depart2"];
        $adressarr = $_POST["arriver2"];
        $departCampus = 1;
    } else {
        $adressdep = $_POST["depart1"];
        $adressarr = $_POST["arriver1"];
        $departCampus = 0;
    }
    $date = $_POST["date"];
    $heure = $_POST["heure"];
    $nbpassager = $_POST["nbpassager"];


    $adressdepModif = strtr($adressdep, ' ', '+');
    $adressarrModif = strtr($adressarr, ' ', '+');




    // recherchons la latitude et la longitude des addresse de départ et d'arriver


    $url_api_adresse_dep = "https://api-adresse.data.gouv.fr/search/?q=$adressdepModif&limit=1";
    $url_api_adresse_arr = "https://api-adresse.data.gouv.fr/search/?q=$adressarrModif&limit=1";


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




    // Vérification des erreurs CURL
    if (curl_errno($curl_dep) && curl_errno($curl_arr)) {
        echo 'Erreur CURL : ' . curl_error($curl_dep);
        echo 'Erreur CURL : ' . curl_error($curl_arr);
    } else {
        // Décoder la réponse JSON en un tableau associatif PHP
        $donnees_dep = json_decode($response_dep, true);
        $donnees_arr = json_decode($response_arr, true);
        // Vérifier si la réponse contient des données
        if (isset($donnees_dep['features'], $donnees_arr['features']) && !empty($donnees_dep['features']) && !empty($donnees_arr['features'])) {

            $depcity = $donnees_dep["features"][0]["properties"]["city"];
            $arrcity = $donnees_arr["features"][0]["properties"]["city"];
            $geometry = $donnees_dep['features'][0]['geometry'];
            $latitude_dep = $geometry['coordinates'][1];
            $longitude_dep = $geometry['coordinates'][0];
            $geometry = $donnees_arr['features'][0]['geometry'];
            $latitude_arr = $geometry['coordinates'][1];
            $longitude_arr = $geometry['coordinates'][0];

            //requete bdd pour creer les lieux dans la bdd

            $creerCityDep = $bdd->prepare("INSERT INTO Destination (ville, adresse, latitude, longitude) VALUES (:ville, :adresse, :latitude, :longitude)");
            $creerCityDep->bindParam(":ville", $depcity);
            $creerCityDep->bindParam(":adresse", $adressdep);
            $creerCityDep->bindParam(":latitude", $latitude_dep);
            $creerCityDep->bindParam(":longitude", $longitude_dep);

            $creerCityArr = $bdd->prepare("INSERT INTO Destination (ville, adresse, latitude, longitude) VALUES (:ville, :adresse, :latitude, :longitude)");
            $creerCityArr->bindParam(":ville", $arrcity);
            $creerCityArr->bindParam(":adresse", $adressarr);
            $creerCityArr->bindParam(":latitude", $latitude_arr);
            $creerCityArr->bindParam(":longitude", $longitude_arr);

            $creerCityDep->execute();
            $creerCityArr->execute();

            $requestDep = $bdd->query('SELECT idDestination FROM Destination WHERE adresse = "' . $adressdep . '"');
            $requestArr = $bdd->query('SELECT idDestination FROM Destination WHERE adresse = "' . $adressarr . '"');
            $idDep = $requestDep->fetch()["idDestination"];
            $idArr = $requestArr->fetch()["idDestination"];
        } else {
            echo "Aucune adresse trouvée.";
        }
    }

    // Fermer la session CURL
    curl_close($curl_dep);
    curl_close($curl_arr);
} else {
    echo "Les champs 'date', 'heure', 'arriver', 'depart', 'nbpassager' n'ont pas été soumis.";
    header("Location: create-trip.php");
    exit;
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <title>Définir un prix</title>


    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            width: 375px;
            height: 550px;
        }

        .leaflet-touch .leaflet-control-layers,
        .leaflet-touch .leaflet-bar {
            display: none;
        }
    </style>
</head>

<body>
    <header class="">
        <h1>Saisie du prix</h1>
    </header>
    <br><br>
    <main>
        <!-- affichage de la map  -->
        <?php
        $url_api_adresse = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=5b3ce3597851110001cf6248734c41a8117a44f6839360a0e5bbe9f9&start=$longitude_dep,$latitude_dep&end=$longitude_arr,$latitude_arr";
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
                $price = 7 * ($distance / 1000) / 100 * 1.9 / 4;
                $price = round($price, 0, PHP_ROUND_HALF_DOWN);
                $temps = $distance / 1000 / 80;
            }
        }

        curl_close($curl);
        ?>
        <!-- permet de récuperer le prix donner par l'utilisateur  -->
        <form action="create-trip-donnee.php" method="post">
            <nav class="centrer modele-container">
                <span class="text1"></span>
                <h3 class="text2">Prix (en €): <output id="respondprix"><?php echo $price; ?><output></h3>
                <span class="text3"></span>
                <div class="select">
                    <input type="range" name="prix" id="Prix" placeholder="Prix" class="form-input ml-2" value="<?php echo $price; ?>" required="required" min="0" max="<?php echo $price + 10; ?>" step="0.5">
                </div>
            </nav>
            <!-- transmition par post pour le fichier trip donnee  -->
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <input type="hidden" name="heure" value="<?php echo $heure; ?>">
            <input type="hidden" name="arriver" value="<?php echo $idDep; ?>">
            <input type="hidden" name="depart" value="<?php echo $idArr; ?>">
            <input type="hidden" name="nbpassager" value="<?php echo $nbpassager; ?>">
            <input type="hidden" name="depCamp" value="<?php echo $departCampus; ?>">
            <input type="hidden" name="temps" value="<?php echo $temps; ?>">
            <br><br>
            <input class="styled" type="submit" value="Validé" id="valideprix"></input>
        </form>



        <div>
            <div id="map"></div>
            <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

            <?php
            echo "<script>
    var map = L.map('map').setView([$longitude_dep, $latitude_dep], 0);
    mapLink = \"<a href='http://openstreetmap.org'>OpenStreetMap</a>\";
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: 'Leaflet &copy; ' + mapLink + ', contribution',
        maxZoom: 18,
        scrollWheelZoom: false, 
        doubleClickZoom: false, 
    }).addTo(map);

    // Créer l'itinéraire dès que la carte est prête
    L.Routing.control({
        waypoints: [
            L.latLng($latitude_dep, $longitude_dep),
            L.latLng($latitude_arr, $longitude_arr) // Définissez ici vos coordonnées de destination
        ]
        
    }).addTo(map);
</script>";
            ?>
        </div>
        <main>
</body>

</html>