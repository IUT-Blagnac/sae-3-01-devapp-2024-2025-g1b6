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
                    <li data-section="liste-categories">Liste des catégories</li>
                    <li data-section="ajouter-categorie">Ajouter une catégorie</li>
                </ul>
            </li>

            <!-- Lots -->
            <li class="nav-item">
                <div class="nav-link has-submenu <?= str_contains($currentPage, 'lot') ? 'active' : '' ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Lots</span>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li data-section="liste-lots">Liste des lots</li>
                    <li data-section="creer-lot">Créer un lot</li>
                </ul>
            </li>

            <!-- Avis -->
            <li class="nav-item">
                <div class="nav-link has-submenu <?= str_contains($currentPage, 'avis') ? 'active' : '' ?>">
                    <i class="fa-solid fa-comments"></i>
                    <span>Avis</span>
                    <i class="fa-solid fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li data-section="tous-les-avis">Tous les avis</li>
                    <li data-section="avis-signales">Avis signalés</li>
                    <li data-section="moderation">Modération</li>
                </ul>
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