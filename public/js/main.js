document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter values
    const energieSelect = document.getElementById('energieAjax');
    const marqueSelect = document.getElementById('marqueAjax');
    const prixSlider = document.getElementById('prixAjax');
    const trieSelect = document.getElementById('trie');
    const etatSelector = document.getElementById('etatSelector');
    const rangeValue = document.getElementById('rangeValue');
    
    if (energieSelect) energieSelect.value = '';
    if (marqueSelect) marqueSelect.value = '';
    if (prixSlider) prixSlider.value = '';
    if (trieSelect) trieSelect.value = '';

    /**
     * Filters state
     */
    let filters = {
        energie: null,
        marque: null,
        prixMax: null,
        etat: null,
        conso: null,
        sortType: null
    };

    /**
     * Filter function using Fetch API with loading state
     */
    async function filtrer() {
        const tbody = document.getElementById('voiture_index_body');
        if (!tbody) return;

        try {
            // Show loading state
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 dark:border-primary-400 mb-4"></div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">
                                <i class="fa-solid fa-magnifying-glass mr-2" aria-hidden="true"></i>
                                Recherche en cours...
                            </p>
                        </div>
                    </td>
                </tr>
            `;

            const formData = new FormData();

            if (filters.energie) formData.append('energie', filters.energie);
            if (filters.marque) formData.append('marque', filters.marque);
            if (filters.prixMax) formData.append('prixMax', filters.prixMax);
            if (filters.etat) formData.append('etat', filters.etat);
            if (filters.conso) formData.append('conso', filters.conso);
            if (filters.sortType) formData.append('sortType', filters.sortType);

            const response = await fetch('/voiture/search', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const html = await response.text();
            tbody.innerHTML = html;

        } catch (error) {
            console.error('Error filtering:', error);

            // Show error state
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center text-red-600 dark:text-red-400">
                            <i class="fa-solid fa-exclamation-triangle text-5xl mb-4 opacity-50" aria-hidden="true"></i>
                            <p class="text-lg font-medium mb-2">Erreur lors de la recherche</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Veuillez réessayer dans quelques instants
                            </p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    // Event listener for energie filter
    if (energieSelect) {
        energieSelect.addEventListener('change', function() {
            filters.energie = this.value;
            filtrer();
        });
    }

    // Event listener for marque filter
    if (marqueSelect) {
        marqueSelect.addEventListener('change', function() {
            filters.marque = this.value;
            filtrer();
        });
    }

    // Event listener for price slider
    if (prixSlider) {
        prixSlider.addEventListener('change', function() {
            if (rangeValue) {
                rangeValue.textContent = this.value;
            }
            filters.prixMax = this.value;
            filtrer();
        });

        // Real-time price display update
        prixSlider.addEventListener('input', function() {
            if (rangeValue) {
                rangeValue.textContent = this.value;
            }
        });
    }

    // Event listener for energie unit display in forms
    const energieFormSelect = document.getElementById('energie');
    if (energieFormSelect) {
        energieFormSelect.addEventListener('change', function() {
            let unite = 'L/100 km';
            if (this.value == '4') {
                unite = 'kWh';
            }
            const consoUnite = document.getElementById('conso_unite');
            if (consoUnite) {
                consoUnite.textContent = '(' + unite + ')';
            }
        });
    }

    // Event listener for sort type
    if (trieSelect) {
        trieSelect.addEventListener('change', function() {
            filters.sortType = this.value;
            filtrer();
        });
    }

    // Event listener for etat selector (new only)
    if (etatSelector) {
        etatSelector.addEventListener('change', function() {
            filters.etat = this.checked ? this.value : null;
            filtrer();
        });
    }

    // Event listener for occasion checkbox (show/hide fields)
    const occasionCheckbox = document.getElementById('occasion');
    if (occasionCheckbox) {
        occasionCheckbox.addEventListener('change', function() {
            const occasionFields = document.querySelectorAll('.occasion.hidden');
            occasionFields.forEach(field => {
                field.style.display = 'block';
                field.style.opacity = '0';
                setTimeout(() => {
                    field.style.transition = 'opacity 0.3s';
                    field.style.opacity = '1';
                }, 10);
            });
        });
    }

    // Event listener for neuf checkbox (hide fields)
    const neufCheckbox = document.getElementById('neuf');
    if (neufCheckbox) {
        neufCheckbox.addEventListener('change', function() {
            const occasionFields = document.querySelectorAll('.occasion.hidden');
            occasionFields.forEach(field => {
                field.style.transition = 'opacity 0.3s';
                field.style.opacity = '0';
                setTimeout(() => {
                    field.style.display = 'none';
                }, 300);
            });
        });
    }

    // Event listener for flash messages close button
    document.addEventListener('click', function(e) {
        if (e.target.closest('.flash-message .croix')) {
            const flashMessage = e.target.closest('.flash-message');
            if (flashMessage) {
                flashMessage.style.transition = 'opacity 0.3s';
                flashMessage.style.opacity = '0';
                setTimeout(() => {
                    flashMessage.style.display = 'none';
                }, 300);
            }
        }
    });
});
