<!-- cette page permet de voir les informations relatif au utilisateur -->

<?php
require "../php/config.php";

$reponse = $bdd->query('SELECT * FROM user');
// On affiche chaque entr´ee une `a une

?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style-main-structure.css">
  <link rel="stylesheet" href="styles/style_admin.css">


  <title>Document</title>
</head>

<body class="page-type">
<!-- menu de l'admin  -->
  <div class=" droite">
    <a href="..\admin\page_campus.php">
      <div class=" titre">Campus</div>
    </a>
    <div class=" titrechoisi">Liste Utilisateur</div>

    <a href="..\admin\page_permis.php">
      <div class=" titre">Gestion de permis</div>
    </a>


  </div>


  <div class=" gauche">


    <div class="titre22 titre">Liste Utilisateur</div>

    <div class="liste ">
      <div class="listeutilisateur ">
        <div class=" titre1">nom</div>
        <div class=" titre1">prenom</div>
        <div class=" titre1">email</div>
        <div class=" titre1"></div>

      </div>
      <!-- selection de tout les utilisateurs, ici c'est laffichage -->
      <?php while ($donnees = $reponse->fetch()) { ?>
        <form method="post" action="effacer_utilisateur.php" class="listeutilisateur ">

          <?php echo ' <input type="text" class="ville1 titre1 input_campus non-selectable" name="nom" readonly="readonly" value="' . $donnees["nom"] . '">
          <input type="text" class="adresse1 titre1 input_campus non-selectable" name="prenom" readonly="readonly" value="' . $donnees["prenom"] . '">
          <input type="text" class="adresse1 titre1 input_campus non-selectable" name="email" readonly="readonly" value="' . $donnees["email"] . '" >';
          ?>
          <!-- bouton pour voir les information   -->
          <input type="submit" name="boutonpermis" value="information" class="selection titre1">
          <!-- bouton pour bannir  -->
          <input type="submit" name="boutonpermis" value="bannir" class="selection titre1">


        </form>

      <?php
      }
      //On termine le traitement de la requ^ete
      $reponse->closeCursor();
      ?>

</body>


</html>