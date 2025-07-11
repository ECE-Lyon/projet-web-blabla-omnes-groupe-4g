<?php
require "../../php/config.php";

session_start();


$email = $_SESSION["current-user-email"];

//verifie si existe
$checkUser = $bdd->query("SELECT COUNT(*) as here FROM Driver d JOIN `User` u on d.email = u.email WHERE u.email = '$email'");
$emailHere = $checkUser->fetch()["here"];
if ($emailHere == 0) {
    echo "vous n'etes pas enregistrer comme conducteur";
} else {
    $requestUser = $bdd->query("SELECT d.idDriver as driver FROM Driver d JOIN `User` u on d.email = u.email WHERE u.email = '$email'");
    $idDriver = $requestUser->fetch()["driver"];
    $requestidDriver = $bdd->query("SELECT * FROM Booking b WHERE b.idDriver = $idDriver");





?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/accept-trip.css">
        <link rel="stylesheet" href="../../css/style-main-structure.css">
        <link rel="stylesheet" href="../../trip-finding/styles/style-trip-description.css">
        <title>Document</title>
    </head>

    <body>
        <header>
            <div class="title-description">
                <a href="my-trip.php">
                    <div class="retour">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="25px" height="25px" viewBox="0 0 1280.000000 640.000000" preserveAspectRatio="xMidYMid meet" fill="#138D75">
                            <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" fill="#138D75" stroke="none">
                                <path d="M3310 5925 c-36 -8 -92 -28 -125 -45 -33 -16 -352 -240 -710 -498 -357 -257 -1010 -726 -1450 -1041 -536 -384 -822 -596 -866 -640 -193 -194 -210 -498 -40 -724 48 -65 2884 -2387 2978 -2439 216 -119 480 -82 655 93 111 111 164 239 162 394 -1 133 -35 235 -113 338 -22 29 -331 289 -814 685 l-778 637 5078 5 5078 5 59 22 c241 91 391 319 372 563 -18 233 -162 415 -393 498 -45 16 -369 17 -5132 22 l-5084 5 794 570 c445 319 818 594 849 625 176 177 206 470 70 678 -74 114 -185 200 -306 237 -72 23 -207 28 -284 10z" />
                            </g>
                        </svg>

                    </div>
                </a>
            </div>
        </header>
        <main>
        <?php
        while ($donnee = $requestidDriver->fetch()) {
            $idPassenger = $donnee["idPassenger"];
            $requestPassenger = $bdd->query("SELECT u.prenom as passager FROM Passenger p  JOIN `User` u on p.email = u.email WHERE p.idPassenger = $idPassenger");
            $nom = $requestPassenger->fetch()["passager"];
            if ($donnee["passed"] == 0) {
                echo '<div class="container">
            <form action="mytrip-traitement.php" method="post">
                <input type="hidden" name="idpass" value="' . $idPassenger . '" class="notvisible">
                <input type="hidden" name="idTrip" value="' . $donnee["idTrip"] . '" class="notvisible">
                <div class="accept-container">
                    <a href="../../profil/php/visuinfo.php?idPassenger=' . $idPassenger . '"
                    <div>
                        <!-- image pdp -->
                        <div>' . $nom . '</div>
                    </div>
                    </a>
                    <div>
        
                    </div>
                    <div class="choose-container">
                        <input type="submit" name="action" value="Accepter">
                        <input type="submit" name="action" value="Refuser">
                    </div>
                </div>
            </form>
            </div>';
            }
        }
    }
        ?>
        </main>

    </body>

    </html>

    <!--quand user accepte envoie ajoute passed = 1 dans la table booking
    et ajout l'idpassager dans la table car_passengers
    si refus mettre passed = 1 et ne pas ajouter l'idPassager -->