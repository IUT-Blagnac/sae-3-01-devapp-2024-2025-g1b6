<style>
/* Sidebar */
.sidebar {
    width: 250px;
    background-color: var(--white);
    border-radius: 15px;
    padding: 20px;
    box-shadow: var(--shadow);
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 10px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--text-dark);
    gap: 10px;
}

.nav-link:hover, .nav-link.active {
    background-color: var(--clair-purple);
    color: var(--dark-purple);
}

.nav-link i {
    width: 20px;
    text-align: center;
}

.nav-link.has-submenu {
    justify-content: space-between;
}

.arrow {
    transition: transform 0.3s ease;
}

.submenu {
    list-style: none;
    padding-left: 45px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
}

.submenu.open {
    max-height: 500px;
}

.arrow.rotated {
    transform: rotate(180deg);
}

.submenu li {
    padding: 10px 0;
    cursor: pointer;
    color: var(--text-dark);
}

.submenu li:hover {
    color: var(--dark-purple);
}

.submenu li.active {
    color: var(--dark-purple);
    font-weight: 500;
}
</style>

<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <!-- Accueil -->
            <li class="nav-item">
                <div class="nav-link <?= $currentPage === 'accueil' ? 'active' : '' ?>" data-section="accueil">
                    <i class="fa-solid fa-house"></i>
                    <span>Accueil</span>
                </div>
            </li>

            <!-- Produits -->
            <li class="nav-item">
                <div class="nav-link has-submenu <?= str_contains($currentPage, 'produit') ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i>
                    <span>Produits</span>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu" <?= str_contains($currentPage, 'produit') ? 'style="max-height: none;"' : '' ?>>
                    <li data-section="liste-produits" class="<?= $currentPage === 'liste-produits' ? 'active' : '' ?>">Liste des produits</li>
                    <li data-section="ajouter-produit" class="<?= $currentPage === 'ajouter-produit' ? 'active' : '' ?>">Ajouter un produit</li>
                </ul>
            </li>

            <!-- Catégories -->
            <li class="nav-item">
                <div class="nav-link has-submenu <?= str_contains($currentPage, 'categorie') ? 'active' : '' ?>">
                    <i class="fa-solid fa-tags"></i>
                    <span>Catégories</span>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li data-section="liste-categories" class="<?= $currentPage === 'liste-categories' ? 'active' : '' ?>">Liste des catégories</li>
                    <li data-section="ajouter-categorie" class="<?= $currentPage === 'ajouter-categorie' ? 'active' : '' ?>">Ajouter une catégorie</li>
                </ul>
            </li>

            <!-- Packs -->
            <li class="nav-item">
                <div class="nav-link has-submenu <?= str_contains($currentPage, 'pack') ? 'active' : '' ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Packs</span>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li data-section="liste-packs" class="<?= $currentPage === 'liste-packs' ? 'active' : '' ?>">Liste des packs</li> 
                    <li data-section="creer-pack" class="<?= $currentPage === 'creer-pack' ? 'active' : '' ?>">Créer un pack</li>
                </ul>
            </li>

            <!-- Avis -->
            <li class="nav-item">
                <div class="nav-link has-submenu <?= str_contains($currentPage, 'avis') ? 'active' : '' ?>">
                    <i class="fa-solid fa-comments"></i>
                    <span>Marques</span>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li data-section="liste-marques">Liste Marques</li>
                    <li data-section="ajouter-marque">Ajouter une marque</li>
                </ul>
            </li>

            <!-- Gestion Admin -->
            <li class="nav-item">
                <div class="nav-link <?= $currentPage === 'gestion-admin' ? 'active' : '' ?>" data-section="gestion-admin">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Gestion Admin</span>
                </div>
            </li>
        </ul>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.nav-link.has-submenu');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Fermer tous les autres sous-menus
            menuItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.nextElementSibling.classList.remove('open');
                    otherItem.querySelector('.arrow')?.classList.remove('rotated');
                }
            });
            
            // Basculer l'état du sous-menu actuel
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('open');
            
            // Rotation de la flèche
            const arrow = this.querySelector('.arrow');
            arrow.classList.toggle('rotated');
        });
    });

    // Gestion de la navigation
    document.querySelectorAll('[data-section]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            const section = this.dataset.section;
            switch(section) {
                case 'accueil':
                    window.location.href = 'dashboardAdmin.php';
                    break;
                case 'liste-produits':
                    window.location.href = 'listeProduits.php';
                    break;
                case 'ajouter-produit':
                    window.location.href = 'ajouterProduit.php';
                    break;
                case 'liste-categories':
                    window.location.href = 'listeCategories.php';
                    break;
                case 'ajouter-categorie':
                    window.location.href = 'ajouterCategorie.php';
                    break;
                case 'liste-packs':
                    window.location.href = 'listePacks.php';
                    break;
                case 'creer-pack':
                    window.location.href = 'ajouterPack.php';
                    break;
                case 'liste-marques':
                    window.location.href = 'listeMarques.php';
                    break;
                case 'ajouter-marque':
                    window.location.href = 'ajouterMarque.php';
                    break;
                case 'gestion-admin':
                    window.location.href = 'gestionAdmin.php';
                    break;
            }
        });
    });

    // Ouvrir automatiquement le menu actif
    const activeSubmenu = document.querySelector('.nav-link.has-submenu.active');
    if (activeSubmenu) {
        const submenu = activeSubmenu.nextElementSibling;
        const arrow = activeSubmenu.querySelector('.arrow');
        submenu.classList.add('open');
        arrow?.classList.add('rotated');
    }
});
</script> 