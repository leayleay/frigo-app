<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Frigo App</title>

    <!-- Bootstrap & feuille de style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Raleway:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body data-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">

<!-- ============ MODALE INSCRIPTION ============ -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Créer un compte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form id="registerForm" autocomplete="off">
          <div class="mb-3">
            <label for="registerEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="registerEmail" autocomplete="email" required>
          </div>
          <div class="mb-3">
            <label for="registerPassword" class="form-label">Mot de passe (≥ 6 caractères)</label>
            <input type="password" class="form-control" id="registerPassword" autocomplete="new-password" minlength="6" required>
          </div>
          <button type="submit" class="btn btn-success w-100">S’inscrire</button>
          <div id="registerMessage" class="mt-3 small"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ============ NAVBAR ============ -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand frigo-brand d-flex align-items-center" href="#accueil">
            <div class="frigo-bubble me-2"></div>
            <span class="frigo-text">Frigo App</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#accueil">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="#ingredients">Ingrédients</a></li>
                <li class="nav-item"><a class="nav-link" href="#recipesSection">Recettes</a></li>
                <li class="nav-item"><a class="nav-link" href="#apropos">À propos</a></li>
                <?php if (isset($_SESSION['email'])): ?>
                <li class="nav-item">
                    <span class="nav-link">
                        Bienvenue, <?= htmlspecialchars($_SESSION['email']) ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <span class="badge bg-warning text-dark ms-1">ADMIN</span>
                        <?php endif; ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">Déconnexion</a>
                </li>
                <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="#connexion">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


<!-- ============ ACCUEIL ============ -->
<section id="accueil" class="d-flex flex-column justify-content-center align-items-center text-center">
    <h1 class="display-5 fw-bold">Bienvenue sur Frigo App 🥕</h1>
    <p id="slogan" class="lead">Gérez vos ingrédients, réduisez le gaspillage alimentaire et découvrez des recettes inspirantes selon votre frigo.</p>
    <a href="#ingredients" class="btn btn-primary mt-3">Commencer</a>
</section>

<!-- ============ INGRÉDIENTS ============ -->
<section id="ingredients" class="container mt-5 pt-5">
    <h2>Ingrédients</h2>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <input type="text" id="searchInput" class="form-control flex-grow-1" placeholder="Rechercher un ingrédient">

        <select id="filterCategory" class="form-select" style="max-width: 200px;">
            <option value="all">Toutes les catégories</option>
            <option value="Légume">Légume</option>
            <option value="Viande">Viande</option>
            <option value="Produit laitier">Produit laitier</option>
            <option value="Céréale">Céréale</option>
            <option value="Épicerie">Épicerie</option>
            <option value="Fruit">Fruit</option>
            <option value="Herbe">Herbe</option>
        </select>

        <select id="filterExpiring" class="form-select" style="max-width: 180px;">
            <option value="all">Expiration</option>
            <option value="expiring">Expirant bientôt</option>
            <option value="expired">Déjà expiré</option>
        </select>
    </div>

    <div id="tilesContainer" class="row g-3"></div>
    <div id="ingredientPagination" class="my-3"></div>



</div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <button id="addIngredientButton" class="btn btn-primary mb-3">Ajouter un ingrédient</button>
    <?php else: ?>
        <div class="alert alert-warning">Veuillez vous connecter pour ajouter un ingrédient.</div>
    <?php endif; ?>

    <div id="tilesContainer" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3"></div>
</section>

<!-- MODALE AJOUT / MODIF INGRÉDIENT -->
<div class="modal fade" id="ingredientModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formTitle">Ajouter un ingrédient</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form id="ingredientForm" autocomplete="off">
          <div class="mb-3">
            <label for="ingredientName" class="form-label">Nom</label>
            <input type="text" id="ingredientName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="ingredientQuantity" class="form-label">Quantité</label>
            <input type="number" id="ingredientQuantity" class="form-control" min="1" required>
          </div>
          <div class="mb-3">
            <label for="ingredientExpiry" class="form-label">Date de péremption</label>
            <input type="date" id="ingredientExpiry" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="ingredientCategory" class="form-label">Catégorie</label>
            <select id="ingredientCategory" class="form-select" required>
    <option value="" disabled selected>Choisir une catégorie</option>
    <option value="Légume">Légume</option>
    <option value="Viande">Viande</option>
    <option value="Produit laitier">Produit laitier</option>
    <option value="Céréale">Céréale</option>
    <option value="Épicerie">Épicerie</option>
    <option value="Fruit">Fruit</option>
    <option value="Herbe">Herbe</option>
</select>

          </div>
          <input type="hidden" id="ingredientId">
          <button type="submit" class="btn btn-success w-100">Enregistrer</button>
        </form>
        <div id="ingredientMessage" class="mt-3 small"></div>
      </div>
    </div>
  </div>
</div>

<!-- ============ RECETTES ============ -->
<section id="recipesSection" class="container mt-5 pt-5">
    <h2>Mes Recettes</h2>
    <p>Ajoutez vos recettes, elles se mettront en surbrillance quand tous les ingrédients seront disponibles et non périmés.</p>
    <div class="d-flex mb-3">
  <input type="text" id="recipeSearch" class="form-control me-2"
         placeholder="Rechercher une recette">
</div>
    <button id="addRecipeButton" class="btn btn-primary mb-3">Ajouter une recette</button>
    <div id="recipesContainer" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3"></div>
</section>

<!-- MODALE AJOUT RECETTE -->
<div class="modal fade" id="recipeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter une recette</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form id="recipeForm" autocomplete="off">
          <div class="mb-3">
            <label for="recipeName" class="form-label">Nom de la recette</label>
            <input type="text" id="recipeName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="recipeIngredients" class="form-label">Ingrédients (format : ingrédient:quantité, un par ligne)</label>
            <textarea id="recipeIngredients" class="form-control" rows="5" placeholder="Ex:\ntomate:2\npoulet:1" required></textarea>
          </div>
          <input type="hidden" id="recipeId">
          <button type="submit" class="btn btn-success w-100">Enregistrer la recette</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ============ RECETTE ALÉATOIRE ============ -->
<section class="container mt-5 text-center" id="random-recipe-section">
    <h2 class="mb-3">🎲 Recette Aléatoire</h2>
    <button id="randomRecipeBtn" class="btn btn-outline-success mb-3">Lancer</button>

    <div id="randomRecipeResult" class="random-recipe-card d-none fade-in">
        <h2 class="random-recipe-title">Recette aléatoire</h2>
        <p class="card-text" id="randomRecipeName">Nom de la recette ici</p>
        <button class="btn btn-primary mt-2" id="viewRecipeBtn">Voir la recette</button>
    </div>
</section>


<!-- ============ À PROPOS ============ -->
<section id="apropos" class="section-bg">
    <div class="container text-center">
        <h2>À propos</h2>
        <p>Frigo App est un projet IFT3225 permettant la gestion intelligente des ingrédients et la génération de recettes adaptées selon vos disponibilités, visant à réduire le gaspillage alimentaire.</p>
        <p>Réalisé par lea hemidj, youssef]</p>
    </div>
</section>


<!-- ============ CONNEXION ============ -->
<section id="connexion" class="section-bg">
    <div class="container d-flex justify-content-center">
        <div class="login-box p-4">
            <h2 class="text-center mb-3">Connexion</h2>
            <form id="loginForm" autocomplete="off">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" autocomplete="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                <div id="loginMessage" class="mt-3 small"></div>
            </form>
            <div class="text-center mt-2">
                <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Pas encore de compte ? Inscris-toi ici</a>
            </div>
        </div>
    </div>
</section>

<footer>
    Frigo App © 2025 — Réalisé  par lea hemidj et youssef
</footer>

<!-- ============ SCRIPTS ============ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toute la logique client est concentrée dans app.js -->
<script src="app.js" defer></script>
</body>
</html>
