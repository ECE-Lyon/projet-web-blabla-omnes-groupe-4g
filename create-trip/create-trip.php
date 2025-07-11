<!-- page qui permet de récupéere les données de l'utilisateur afin de creer un trajet  -->

<?php require "../php/config.php";
session_start();
$email = $_SESSION["current-user-email"];

$checkDriver = $bdd->query("SELECT COUNT(*) as here FROM Driver d JOIN `User` u on d.email = u.email WHERE u.email = '$email'");
$emailHere = $checkDriver->fetch()["here"];
if ($emailHere == 0) {
    header("Location: non-driver-redirection.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="create-trip.js" defer></script>
    <link rel="stylesheet" href="create-trip.css">
    <link rel="stylesheet" href="../css/style-main-structure.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script src="../trip-finding/scripts/autocompletion.js" defer></script>
    <title>Creer un trajet</title>
</head>

<body>
    <header class="">
        <h1>Créer un trajet</h1>
    </header>
    <main>
        <form method="post" id="myForm" action="creat-trip-price.php">
            <nav class="modele-container">
                <p class="text1">Départ</p>
                <span class="text2"></span>
                <span class="text3 centrerhorizontalement">
                    <label class="switch">
                        <input type="checkbox" id="Campus" />
                        <span></span>
                    </label>
                    campus OMNES
                </span>
                <div class="select">
                    <!-- creation d'un select et d'un input avec un des deux ayant un display = none  -->
                    <input type="text" name="depart1" id="depart1" placeholder="Départ" class="form-input autocomplete ">
                    <div class="suggestions"></div>
                    <select name="depart2" id="depart2" class="form-input ml-2">
                        <?php
                        $reponse = $bdd->query('SELECT * FROM campus');
                        // on récupère les différents campus 
                        while ($donnees = $reponse->fetch()) {
                        ?>
                            <option value="<?php echo $donnees['address']; ?>" name="<?php echo $donnees['city']; ?>">Campus <?php echo $donnees['city']; ?></option>
                        <?php
                        }
                        //On termine le traitement de la requ^ete
                        $reponse->closeCursor();
                        ?>
                    </select>
                </div>
            </nav>
            <br><br>
            <nav class="modele-container">
                <p class="text1">Arrivée</p>
                <span class="text2"></span>
                <span class="text3"></span>
                <div class="select">
                    <!-- creation d'un select et d'un input avec un des deux ayant un display = none  -->
                    <input type="text" name="arriver2" id="arriver2" placeholder="Arriver" class="form-input autocomplete ">
                    <div class="suggestions"></div>
                    <select name="arriver1" id="arriver1" class="form-input ml-2">
                        <?php
                        $reponse = $bdd->query('SELECT * FROM campus');
                        // on récupère les différents campus 
                        while ($donnees = $reponse->fetch()) {
                        ?>
                            <option value="<?php echo $donnees['address']; ?>" name="<?php echo $donnees['city']; ?>">Campus <?php echo $donnees['city']; ?></option>
                        <?php
                        }
                        //On termine le traitement de la requ^ete
                        $reponse->closeCursor();
                        ?>
                    </select>
                </div>
                <div class="suggestions"></div>
            </nav>
            <br><br>
            <nav class="modele-container">
                <p class="text1">Date</p>
                <span class="text2"></span>
                <span class="text3"></span>
                <div class="select">
                    <input type="text" name="date" id="datepicker" placeholder="Date" class="form-input ml-2" required="required">
                </div>
            </nav>
            <br><br>
            <nav class="modele-container">
                <p class="text1">Heure de départ</p>
                <span class="text2"></span>
                <span class="text3"></span>
                <div class="select">
                    <input type="time" name="heure" placeholder="Heure de départ" class="form-input ml-2" required="required">
                </div>
            </nav>
            <br><br>
            <nav class="modele-container">
                <p class="text1">Nombre de passager</p>
                <span class="text2"></span>
                <span class="text3"></span>
                <div class="select">
                    <input type="number" name="nbpassager" placeholder="Nombre de passager" class="form-input ml-2" required="required" min="1" max="10">
                </div>
            </nav>
            <br><br>
            <input class="styled" type="submit" value="Validé" id="valide"></input>
        </form>
    </main>
    <?php require "../php/footer.php"; ?>
</body>

</html>