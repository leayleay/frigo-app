    const PAGE_SIZE = 15;
    const normalize = txt => txt.toLowerCase()
                                 .normalize('NFD')
                                 .replace(/\p{Diacritic}/gu, '');
    const today0    = () => new Date().setHours(0,0,0,0);
    
    const GET  = u     => fetch(u).then(r => r.json());
    const POST = (u,d) => fetch(u, {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify(d)
    }).then(r => r.json());
    
    const isLoggedIn = document.body.dataset.loggedIn === 'true';
    
    /* ------------------------------------------------------------------
       1.  INGRÉDIENTS 
       ------------------------------------------------------------------ */
    const tilesContainer = document.getElementById('tilesContainer');
    let   ingPagNav      = document.getElementById('ingredientPagination');
    if (!ingPagNav) {
        ingPagNav        = document.createElement('nav');
        ingPagNav.id     = 'ingredientPagination';
        ingPagNav.className = 'my-3';
        tilesContainer.parentElement.appendChild(ingPagNav);
    }
    
    const addIngBtn = document.getElementById('addIngredientButton');
    const ingModal  = new bootstrap.Modal(document.getElementById('ingredientModal'));
    const ingForm   = document.getElementById('ingredientForm');
    const ingMsg    = document.getElementById('ingredientMessage');
    const formTitle = document.getElementById('formTitle');
    
    const ingName = document.getElementById('ingredientName');
    const ingQty  = document.getElementById('ingredientQuantity');
    const ingDate = document.getElementById('ingredientExpiry');
    const ingCat  = document.getElementById('ingredientCategory');
    const ingId   = document.getElementById('ingredientId');
    
    const searchInp = document.getElementById('searchInput');
    const filterSel = document.getElementById('filterExpiring');
    const filterCategory = document.getElementById('filterCategory');

    
    let ingredients = [];
    let ingPage     = 1;
    
    const bg = c => ({
        legume: '#cce3de',          
        fruit: '#e2d3db',           
        viande: '#ffd6ba',       
        'produit laitier': '#d5dbec', 
        cereale: '#eddbae',         
        epicerie: '#e3e2e2',       
        herbe: '#d8e2dc'            
    }[normalize(c)] || '#f5f5f5');  
    
    const expTxt = d => new Date(d).setHours(0,0,0,0) < today0() ? ' (Expiré)' : '';
    function getCategoryBadgeClass(category) {
        const cat = normalize(category);
        if (cat === 'legume') return 'badge-legume';
        if (cat === 'viande') return 'badge-viande';
        if (cat === 'produit laitier') return 'badge-laitier';
        if (cat === 'cereale') return 'badge-cereale';
        if (cat === 'epicerie') return 'badge-epicerie';
        if (cat === 'fruit') return 'badge-fruit';
        if (cat === 'herbe') return 'badge-herbe';
        return '';
    }
    
    async function loadIngredients() {
        ingredients = await GET('get_ingredients.php');
        renderTiles();
        if (recipes.length) renderRecipes(); // recolore si recettes déjà chargées
    }
    
    function paginate(nav, current, total, cb) {
        nav.innerHTML = '';
        if (total <= 1) return;
    
        const ul = document.createElement('ul');
        ul.className = 'pagination justify-content-center';
    
        const add = (lbl, disabled, page) => {
            const li = document.createElement('li');
            li.className = 'page-item' + (disabled ? ' disabled' : '');
            const a  = document.createElement('a');
            a.className = 'page-link';
            a.href      = '#';
            a.textContent = lbl;
            a.addEventListener('click', e => {
                e.preventDefault();
                if (disabled) return;
                cb(page); // change page
                window.scrollTo({           // scroll-top
                    top: nav.parentElement.offsetTop - 70,
                    behavior: 'smooth'
                });
            });
            li.appendChild(a);
            ul.appendChild(li);
        };
    
        add('«', current === 1,        current - 1);
        for (let p = 1; p <= total; p++) add(p, p === current, p);
        add('»', current === total,    current + 1);
    
        nav.appendChild(ul);
    }
    
    function renderTiles() {
        tilesContainer.innerHTML = '';
        const txt  = normalize(searchInp.value);
        const mode = filterSel.value;               // all | expiring
        const now  = new Date();
    
        const filtered = ingredients.filter(i => {
            const days = Math.ceil((new Date(i.expiry_date) - now) / 8.64e7);
        
            const matchTxt = normalize(i.name).includes(txt) ||
                             normalize(i.category).includes(txt);
        
                             let matchExp = true;
                             if (mode === 'expiring')   matchExp = days > 0  && days <= 3;  // <=3 jours
                             if (mode === 'expired')    matchExp = days <= 0;               // déjà dépassé
                             
        
            const matchCat = (filterCategory.value === 'all') ||
                             (i.category === filterCategory.value);
        
            return matchTxt && matchCat && matchExp;
        });
        
    
        const total = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (ingPage > total) ingPage = total;
    
        const slice = filtered.slice(
            (ingPage - 1) * PAGE_SIZE,
            ingPage       * PAGE_SIZE
        );
    
        slice.forEach(i => {
            const days = Math.ceil((new Date(i.expiry_date) - now) / 8.64e7);
            const col  = document.createElement('div');
            col.className = 'col-md-4 fade-in';
            col.innerHTML = `
            <div class="card shadow-sm position-relative" style="background:${bg(i.category)}">
            <span class="badge-category ${getCategoryBadgeClass(i.category)}">${i.category}</span>
              <div class="card-body">
             <h5 class="card-title">${i.name}</h5>
             <p class="card-text">Quantité : ${i.quantity}</p>
             <p class="card-text ${days<=3 ? 'text-danger' : ''}">
             Péremption : ${i.expiry_date}${expTxt(i.expiry_date)}
             </p>
             <div class="mt-2">
             <button class="btn btn-sm btn-primary me-2" data-e>Edit</button>
            <button class="btn btn-sm btn-danger" data-d>Supp</button>
             </div>
            </div>
            </div>`;

            col.querySelector('[data-e]')
               .addEventListener('click', () => editIng(i));
            col.querySelector('[data-d]')
               .addEventListener('click', () => delIng(i.id));
            tilesContainer.appendChild(col);
            setTimeout(() => col.classList.add('show'), 10);
        });
    
        paginate(ingPagNav, ingPage, total, p => { ingPage = p; renderTiles(); });
    }
    
    function editIng(i) {
        formTitle.textContent = 'Modifier un ingrédient';
        ingName.value = i.name;
        ingQty.value  = i.quantity;
        ingDate.value = i.expiry_date;
        ingCat.value  = i.category;
        ingId.value   = i.id;
        ingModal.show();
    }
    
    async function delIng(id) {
        if (!confirm('Supprimer ?')) return;
        const r = await POST('delete_ingredient.php', { id });
        r.success ? loadIngredients()
                  : alert(r.message || 'Erreur suppression.');
    }
    
    addIngBtn?.addEventListener('click', () => {
        formTitle.textContent = 'Ajouter un ingrédient';
        ingForm.reset();
        ingId.value = '';
        ingModal.show();
    });
    searchInp?.addEventListener('input',  renderTiles);
    filterSel?.addEventListener('change', renderTiles);
    filterCategory?.addEventListener('change', renderTiles);

    
    ingForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const payload = {
            name        : ingName.value.trim(),
            quantity    : ingQty.value.trim(),
            expiry_date : ingDate.value,
            category    : ingCat.value,
            id          : ingId.value || undefined
        };
        if (!payload.name || !payload.quantity ||
            !payload.expiry_date || !payload.category) {
            ingMsg.textContent = 'Veuillez remplir tous les champs.';
            ingMsg.className   = 'alert alert-danger mt-3';
            return;
        }
        ingMsg.textContent = 'Enregistrement…';
        ingMsg.className   = 'alert alert-info mt-3';
        const url = payload.id ? 'update_ingredient.php' : 'add_ingredient.php';
        const r   = await POST(url, payload);
        if (r.success) {
            ingMsg.textContent = '✅ Enregistré !';
            ingMsg.className   = 'alert alert-success mt-3';
            setTimeout(() => ingMsg.textContent = '', 1500);
            ingModal.hide();
            loadIngredients();
        } else {
            ingMsg.textContent = r.message || 'Erreur.';
            ingMsg.className   = 'alert alert-danger mt-3';
        }
    });
    
    /* ------------------------------------------------------------------
       2.  RECETTES 
       ------------------------------------------------------------------ */
    const recipesSection = document.getElementById('recipesSection');
    const recIdHidden = document.getElementById('recipeId');
    let recPagNav = document.getElementById('recipesPagination');
    if (!recPagNav) {
        recPagNav = document.createElement('nav');
        recPagNav.id = 'recipesPagination';
        recPagNav.className = 'my-3';
        recipesSection.appendChild(recPagNav);
    }
    
    const recipeSearch = document.getElementById('recipeSearch');
    
    const addRecBtn   = document.getElementById('addRecipeButton');
    const recModal    = new bootstrap.Modal(document.getElementById('recipeModal'));
    const recForm     = document.getElementById('recipeForm');
    const recipesContainer = document.getElementById('recipesContainer');
    const randBtn     = document.getElementById('randomRecipeBtn');
    const randRes     = document.getElementById('randomRecipeResult');
    
    let recipes = [];
    let recPage  = 1;
    
    function recDoable(r) {
        return r.ingredients.every(q => {
            const f = ingredients.find(i =>
                normalize(i.name) === normalize(q.ingredient)
            );
            return f &&
                   Number(f.quantity) >= Number(q.quantity) &&
                   new Date(f.expiry_date).setHours(0,0,0,0) >= today0();
        });
    }
    
    async function loadRecipes() {
        recipes = await GET('get_recipe.php');
        renderRecipes();
    }
    
    function renderRecipesPagination(total) {
        paginate(recPagNav, recPage, total, p => { recPage = p; renderRecipes(); });
    }
    
    function renderRecipes() {
        recipesContainer.innerHTML = '';
    
        // filtre texte + pagination
        const txt   = normalize(recipeSearch?.value || '');
        const filt  = recipes.filter(r => normalize(r.name).includes(txt));
    
        const total = Math.max(1, Math.ceil(filt.length / PAGE_SIZE));
        if (recPage > total) recPage = total;
    
        const slice = filt.slice(
            (recPage - 1) * PAGE_SIZE,
            recPage       * PAGE_SIZE
        );
    
        slice.forEach(rec => {
            const doable = recDoable(rec);

    
            const col  = document.createElement('div');
            col.className = 'col-md-4';
    
            const card = document.createElement('div');
            card.className = 'card shadow-sm';
            card.style.backgroundColor = doable ? '#d0f0c0' : '#f8d7da';
    
            const body = document.createElement('div');
            body.className = 'card-body';
            body.innerHTML = `<h5 class="card-title">${rec.name}</h5>`;
    
            const ul = document.createElement('ul');
            rec.ingredients.forEach(i => {
                const li = document.createElement('li');
                li.textContent = `${i.ingredient}: ${i.quantity}`;
                ul.appendChild(li);
            });
            body.appendChild(ul);
            // ----- bouton Modifier -----
            const edit = document.createElement('button');
            edit.className = 'btn btn-sm btn-primary mt-2 me-2';
            edit.textContent = 'Modifier';
            edit.addEventListener('click', () => {
    // pré-remplit la modale
                document.getElementById('recipeName').value = rec.name;
                document.getElementById('recipeIngredients').value =
                    rec.ingredients.map(i => `${i.ingredient}:${i.quantity}`).join('\n');
                recIdHidden.value = rec.id;          // <-- conserve l'id
                recModal.show();
            });
            body.appendChild(edit);

            const del = document.createElement('button');
            del.className = 'btn btn-sm btn-danger mt-2';
            del.textContent = 'Supprimer';
            del.addEventListener('click', async () => {
                if (!confirm('Supprimer ?')) return;
                const r = await POST('delete_recipe.php', { id: rec.id });
                r.success ? loadRecipes()
                          : alert(r.message || 'Erreur suppression.');
            });
            body.appendChild(del);
    
            card.appendChild(body);
            col.appendChild(card);
            recipesContainer.appendChild(col);
        });
    
        renderRecipesPagination(total);
    }
    
    recipeSearch?.addEventListener('input', () => { recPage = 1; renderRecipes(); });
    
    addRecBtn?.addEventListener('click', () => { recForm.reset(); recModal.show(); });
    
    recForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const name = document.getElementById('recipeName').value.trim();
        const raw  = document.getElementById('recipeIngredients').value.trim();
        if (!name || !raw) { alert('Champs requis.'); return; }
    
        const parsed = raw.split('\n').map(line => {
            const [ing, qty] = line.split(':').map(s => s.trim());
            if (!ing || isNaN(qty)) throw new Error('Format Ingrédient:quantité');
            return { ingredient: ing, quantity: Number(qty) };
        });
    
        /* ---------- AJOUT / MISE À JOUR ---------- */
        const idVal   = recIdHidden.value;           // id vide = ajout, sinon update
        const endpoint = idVal ? 'update_recipe.php' // choisit le fichier PHP
                               : 'add_recipe.php';

        const payload = { name, ingredients: parsed };
        if (idVal) payload.id = Number(idVal);       // ajoute id quand c’est un update

        const r = await POST(endpoint, payload);
        if (r.success) {
            recModal.hide();
            recIdHidden.value = '';                  // réinitialise le champ caché
         loadRecipes();                           // rafraîchit l’affichage
        } else {
            alert(r.message || 'Erreur recette.');
        }

    });
    
    randBtn?.addEventListener('click', () => {
        const doable = recipes.filter(recDoable);
        const randRes = document.getElementById('randomRecipeResult');
    
        if (!doable.length) {
            randRes.innerHTML = `
                <h2 class="random-recipe-title">😕 Aucune recette réalisable</h2>
                <p class="card-text">Vous n'avez pas les ingrédients nécessaires actuellement.</p>
            `;
            randRes.className = 'random-recipe-card fade-in';
            randRes.classList.remove('d-none');
            setTimeout(() => randRes.classList.add('show'), 10);
            return;
        }
    
        const r = doable[Math.floor(Math.random() * doable.length)];
        randRes.innerHTML = `
            <h2 class="random-recipe-title"> ${r.name}</h2>
            <ul class="list-unstyled card-text">
                ${r.ingredients.map(i => `<li>• ${i.ingredient}: ${i.quantity}</li>`).join('')}
            </ul>
        `;
        randRes.className = 'random-recipe-card fade-in';
        randRes.classList.remove('d-none');
        setTimeout(() => randRes.classList.add('show'), 10);
    });
    
    /* ------------------------------------------------------------------
       3.  AUTHENTIFICATION (identique à avant)
       ------------------------------------------------------------------ */
    const loginForm    = document.getElementById('loginForm');
    const loginMsg     = document.getElementById('loginMessage');
    const registerForm = document.getElementById('registerForm');
    const registerMsg  = document.getElementById('registerMessage');
    const registerModal= new bootstrap.Modal(document.getElementById('registerModal'));
    
    loginForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        const pass  = document.getElementById('password').value;
        loginMsg.textContent = 'Connexion…';
        loginMsg.className   = 'alert alert-info mt-3';
        const r = await fetch('login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ email, password: pass })
        }).then(x => x.json());
        loginMsg.textContent = r.message;
        loginMsg.className = 'alert mt-3 ' + (r.success ? 'alert-success' : 'alert-danger');
        if (r.success) {
            setTimeout(() => {
                localStorage.setItem('scrollToIngredients', 'true');
                location.reload();
            }, 800);
        }
        
    });
    
    registerForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const email = document.getElementById('registerEmail').value.trim();
        const pass  = document.getElementById('registerPassword').value;
    
      
        if (pass.length < 6) {
            registerMsg.textContent = 'Mot de passe : 6 caractères minimum';
            registerMsg.className   = 'alert alert-danger mt-3';
            return;
        }
    
    
        registerMsg.textContent = 'Création du compte…';
        registerMsg.className   = 'alert alert-info mt-3';
        const r = await fetch('register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ email, password: pass })
        }).then(x => x.json());
        registerMsg.textContent = r.message;
        registerMsg.className = 'alert mt-3 ' + (r.success ? 'alert-success' : 'alert-danger');
        if (r.success) setTimeout(() => { registerModal.hide(); location.reload(); }, 1200);
    });
    
    /* ------------------------------------------------------------------
       4.  Initialisation
       ------------------------------------------------------------------ */
    loadIngredients();          
    if (isLoggedIn) loadRecipes();
    else recipesSection && (recipesSection.style.display = 'none');
    // Si on a mémorisé qu'il faut scroller après reload
    if (localStorage.getItem('scrollToIngredients') === 'true') {
      localStorage.removeItem('scrollToIngredients'); // Nettoie le flag
       document.getElementById('ingredients').scrollIntoView({ behavior: 'smooth' }); // Scroll
}

    /* ------------------------------------------------------------------
   5.  Polling : rafraîchissement toutes les 5 secondes
   ------------------------------------------------------------------ */
setInterval(() => {
    loadIngredients();           // maj frigo
    if (isLoggedIn) loadRecipes(); // maj recettes pour l'utilisateur
}, 5000); 
