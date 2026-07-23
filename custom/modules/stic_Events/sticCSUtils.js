document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Inyección de estilos CSS para invisibilidad manteniendo la cuadrícula/grid
    if (!document.getElementById('sticCustomHidingStyles')) {
        const styleId = 'sticCustomHidingStyles';
        const styles = `
            .${styleId}_hidden_grid_item {
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
            .${styleId}_hidden_grid_item *, 
            .${styleId}_hidden_grid_item input[type="checkbox"],
            .${styleId}_hidden_grid_item .checker,
            .${styleId}_hidden_grid_item .suitepicon {
                visibility: hidden !important;
                opacity: 0 !important;
            }
        `;
        const styleSheet = document.createElement("style");
        styleSheet.id = styleId;
        styleSheet.innerText = styles;
        document.head.appendChild(styleSheet);
    }
    const cssHidingClass = 'sticCustomHidingStyles_hidden_grid_item';

    function updateVisibility() {
        const typeSelect = document.getElementById('type');
        if (!typeSelect) return;

        // --- LECTURA SEGURA DE TIPO ---
        let selectedValue = typeSelect.value || '';
        let selectedText = '';

        try {
            if (typeSelect.options && typeof typeSelect.selectedIndex === 'number' && typeSelect.selectedIndex >= 0) {
                const opt = typeSelect.options[typeSelect.selectedIndex];
                if (opt) {
                    selectedText = (opt.text || opt.innerText || '').trim();
                }
            }
        } catch (e) {
            selectedValue = typeSelect.value || '';
        }

        // --- REFERENCIAS A LOS CAMPOS ---
        const eventField = document.querySelector('[field="stic_events_stic_events_1_name"]');
        const inheritField = document.querySelector('[field="stic_cs_inherit_reg_c"]');
        const inheritCheckbox = document.getElementById('stic_cs_inherit_reg_c');

        // Función para obtener la etiqueta + celda de entrada (o la fila entera)
        function getContainers(element) {
            if (!element) return [];
            
            const rowContainer = element.closest('.detail-view-row-item');
            if (rowContainer) return [rowContainer];

            const containers = [];
            
            const fieldCell = element.closest('.edit-view-field') || element.closest('.detail-view-field') || element.closest('.col-sm-8') || element.parentElement;
            if (fieldCell) {
                containers.push(fieldCell);
                
                const prevElem = fieldCell.previousElementSibling;
                if (prevElem && (prevElem.classList.contains('label') || prevElem.classList.contains('col-1-label') || prevElem.innerText.includes(':'))) {
                    containers.push(prevElem);
                }
            }
            
            return containers.length > 0 ? containers : [element.parentElement];
        }

        const eventContainers = getContainers(eventField);
        const inheritContainers = getContainers(inheritField);

        function setVisibleGrid(containers, isVisible) {
            if (!containers || !containers.length) return;
            containers.forEach(function(container) {
                if (isVisible) {
                    container.classList.remove(cssHidingClass);
                } else {
                    container.classList.add(cssHidingClass);
                }
            });
        }

        // Helper para desmarcar el checkbox de herencia si está activo
        function uncheckInherit() {
            if (inheritCheckbox && inheritCheckbox.checked) {
                inheritCheckbox.checked = false;
                inheritCheckbox.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        // Helper para borrar el evento seleccionado si existe
        function clearEventValue() {
            const eventIdInput = document.getElementById('stic_events_stic_events_1stic_events_ida');
            const eventNameInput = document.getElementById('stic_events_stic_events_1_name');

            let changed = false;

            if (eventIdInput && (eventIdInput.value !== '' || eventIdInput.getAttribute('data-id-value'))) {
                eventIdInput.value = '';
                if (eventIdInput.hasAttribute('data-id-value')) {
                    eventIdInput.setAttribute('data-id-value', '');
                }
                changed = true;
            }

            if (eventNameInput && eventNameInput.value !== '') {
                eventNameInput.value = '';
                changed = true;
            }

            // Si se ha limpiado el valor, disparamos el evento change para notificar a SuiteCRM
            if (changed && eventNameInput) {
                eventNameInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        // --- LÓGICA DE VISIBILIDAD ---
        const isClub = selectedValue === 'club' || selectedText === 'Club Social';

        if (isClub) {
            // 1. Ocultar ambos campos y etiquetas
            setVisibleGrid(eventContainers, false);
            setVisibleGrid(inheritContainers, false);

            // 2. Desmarcar herencia y vaciar el evento seleccionado
            uncheckInherit();
            clearEventValue();
        } else {
            // Mostrar "Esdeveniment principal"
            setVisibleGrid(eventContainers, true);

            // Evaluar si "Esdeveniment principal" tiene valor
            const eventIdInput = document.getElementById('stic_events_stic_events_1stic_events_ida');
            const eventNameInput = document.getElementById('stic_events_stic_events_1_name');
            
            let hasEventValue = false;

            if (eventIdInput) {
                const val = eventIdInput.value !== undefined ? eventIdInput.value : eventIdInput.getAttribute('data-id-value');
                hasEventValue = Boolean(val && val.trim() !== '');
            }

            if (!hasEventValue && eventNameInput) {
                hasEventValue = Boolean(eventNameInput.value && eventNameInput.value.trim() !== '');
            }

            // Mostrar u ocultar "Heretar inscripcions" según si hay evento seleccionado
            setVisibleGrid(inheritContainers, hasEventValue);

            // Si no hay evento seleccionado, desmarcar checkbox
            if (!hasEventValue) {
                uncheckInherit();
            }
        }
    }

    // --- ESCUCHADORES Y OBSERVADORES DE EVENTOS ---

    // A. Escuchar cambios en el desplegable de Tipo
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'type') {
            updateVisibility();
        }
    });

    // B. Escuchar tecleo manual, borrado o autocompletado en el nombre del evento
    const eventNameInput = document.getElementById('stic_events_stic_events_1_name');
    if (eventNameInput) {
        eventNameInput.addEventListener('input', updateVisibility);
        eventNameInput.addEventListener('change', updateVisibility);
        eventNameInput.addEventListener('keyup', updateVisibility);
    }

    // C. Observer para detectar cuando la ventana Popup o script borra/asigna el ID del evento
    const eventIdInput = document.getElementById('stic_events_stic_events_1stic_events_ida');
    if (eventIdInput) {
        const observer = new MutationObserver(function () {
            updateVisibility();
        });

        observer.observe(eventIdInput, {
            attributes: true,
            attributeFilter: ['value', 'data-id-value'],
            childList: true,
            characterData: true
        });
    }

    // D. Capturar cuando se pulsa el botón "Netejar Selecció"
    const clearBtn = document.getElementById('btn_clr_stic_events_stic_events_1_name');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            updateVisibility();
            setTimeout(updateVisibility, 50);
            setTimeout(updateVisibility, 200);
        });
    }

    // --- EJECUCIÓN INICIAL ---
    updateVisibility();
    setTimeout(updateVisibility, 300);
});