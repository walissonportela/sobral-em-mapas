import { toggleLayer, getSelectionAreaPixels, exportVisibleMapArea } from "./map";
import { handleServerResponse } from "./ui.js";

// Inicializa componentes interativos do mapa
export function InitializeComponents() {
    initializeSelectionBox();         // Caixa de seleção para exportação de imagem
    initializeResolutionWatcher();    // Observa mudanças de resolução
    initializeFloatingButton();       // Botão flutuante de medição
    initializeChat();                 // Chat com assistente (caso aplicável)
    initializeMeasure();              // Função de medição de distância/área
    updateScaleInputFromMap();       // Atualiza campo de escala baseado na resolução
}

// ======================= CAIXA DE SELEÇÃO =========================

function initializeSelectionBox() {
    const selectionBox = document.getElementById("selection-box");
    const dragHandle = document.getElementById("drag-handle");
    const selectionButton = document.getElementById("selection-button");
    const selectionTools = document.querySelector(".selection-tools");
    const resolution = document.getElementById("resolution");
    const selectionArea = document.getElementById("selection-area");
    const downloadButton = document.getElementById('download-button');
    const scaleInput = document.getElementById('scale');

    let isDragging = false;
    let startX, startY, offsetX, offsetY;

    // Alterna visibilidade da barra de seleção
    if (selectionButton && selectionTools) {
        selectionButton.addEventListener("click", function () {
            if (selectionTools.style.display === "none" || selectionTools.style.display === "") {
                selectionButton.classList.add("active");
                selectionTools.style.display = "block";
            } else {
                selectionTools.style.display = "none";
                selectionButton.classList.remove("active");
            }
        });
    }

    // Atualiza dimensões da área de seleção (largura x altura)
    function updateDimensions() {
        const width = selectionArea.offsetWidth;
        const height = selectionArea.offsetHeight;
        resolution.innerHTML = `${width} x ${height}`;
    }

    // Começa o arrasto da caixa ao clicar
    dragHandle.addEventListener("mousedown", function (e) {
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        offsetX = selectionBox.offsetLeft;
        offsetY = selectionBox.offsetTop;
        e.preventDefault();
    });

    // Move a caixa de seleção durante o arrasto
    document.addEventListener("mousemove", function (e) {
        if (isDragging) {
            const moveX = e.clientX - startX;
            const moveY = e.clientY - startY;
            selectionBox.style.left = offsetX + moveX + "px";
            selectionBox.style.top = offsetY + moveY + "px";
        }
    });

    // Finaliza o arrasto ao soltar o mouse
    document.addEventListener("mouseup", function () {
        isDragging = false;
        updateDimensions();
    });

    // Atualiza dimensões ao carregar a página
    updateDimensions();

    // Atualiza dimensões ao redimensionar a caixa
    const resizeObserver = new ResizeObserver(() => updateDimensions());
    resizeObserver.observe(selectionBox);

    // Exporta a área visível do mapa
    downloadButton.addEventListener('click', function () {
        exportVisibleMapArea(window.map);
    });

    // Atualiza o zoom com base no input da escala (com debounce)
    if (scaleInput) {
        const debouncedScaleUpdate = debounce(updateMapFromScaleInput, 400);
        scaleInput.addEventListener('input', debouncedScaleUpdate);
    }
}

// ======================= BOTÃO FLUTUANTE =========================

function initializeFloatingButton() {
    const floatingButton = document.getElementById("floating-button");
    const measureButton = document.getElementById("btn-measure");

    floatingButton.style.display = "none";

    // Alterna visibilidade do botão flutuante
    const toggleFloatingButtonVisibility = () => {
        floatingButton.style.display = floatingButton.style.display === "none" ? "block" : "none";
    };

    // Clique e toque ativam/desativam o botão flutuante
    measureButton.addEventListener("click", toggleFloatingButtonVisibility);
    measureButton.addEventListener("touchend", (e) => {
        e.preventDefault();
        toggleFloatingButtonVisibility();
    });

    // Permite arrastar o botão flutuante na tela
    function dragElement(el) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

        const startDrag = (e) => {
            if (e.target.closest(".dropdown-menu") || e.target.closest("select")) return;
            e.preventDefault();
            pos3 = e.type === "mousedown" ? e.clientX : e.touches[0].clientX;
            pos4 = e.type === "mousedown" ? e.clientY : e.touches[0].clientY;

            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
            document.ontouchend = closeDragElement;
            document.ontouchmove = elementDrag;
        };

        const elementDrag = (e) => {
            e.preventDefault();
            pos1 = pos3 - (e.type === "mousemove" ? e.clientX : e.touches[0].clientX);
            pos2 = pos4 - (e.type === "mousemove" ? e.clientY : e.touches[0].clientY);
            pos3 = e.type === "mousemove" ? e.clientX : e.touches[0].clientX;
            pos4 = e.type === "mousemove" ? e.clientY : e.touches[0].clientY;

            el.style.top = el.offsetTop - pos2 + "px";
            el.style.left = el.offsetLeft - pos1 + "px";
        };

        const closeDragElement = () => {
            document.onmouseup = null;
            document.onmousemove = null;
            document.ontouchend = null;
            document.ontouchmove = null;
        };

        el.onmousedown = startDrag;
        el.ontouchstart = startDrag;
    }

    if (floatingButton) {
        dragElement(floatingButton);
    }
}

// ======================= ESCALA E RESOLUÇÃO =========================

const DPI = 96; // Dots per inch (usado na conversão)

function resolutionToScale(resolution) {
    return Math.round(resolution * DPI * 39.37);
}

function scaleToResolution(scale) {
    return scale / (DPI * 39.37);
}

function updateScaleInputFromMap() {
    const view = window.map.getView();
    const scaleInput = document.getElementById('scale');
    if (!view || !scaleInput) return;

    const resolution = view.getResolution();
    const escala = resolutionToScale(resolution);
    scaleInput.value = escala;
}

function updateMapFromScaleInput() {
    const scaleInput = document.getElementById('scale');
    const scaleMin = 4514;
    if (!scaleInput) return;

    let scaleValue = parseFloat(scaleInput.value);
    if (isNaN(scaleValue)) return;
    if (scaleValue < scaleMin) {
        scaleInput.value = scaleMin;
        scaleValue = scaleMin;
    }

    const resolution = scaleToResolution(scaleValue);
    const view = window.map.getView();
    view.setResolution(resolution);
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function initializeResolutionWatcher() {
    const view = window.map.getView();
    const resolutionElement = document.getElementById('resolution');
    const scaleInput = document.getElementById('scale');

    if (!view) return;

    const updateUI = () => {
        const res = view.getResolution();
        if (resolutionElement) {
            resolutionElement.innerText = `${Math.round(res * 1000)} x ${Math.round(res * 1000)}`;
        }
        if (scaleInput) {
            scaleInput.value = resolutionToScale(res);
        }
    };

    updateUI();
    view.on('change:resolution', updateUI);
}

// ======================= DROPDOWN PARA TOUCH =========================

document.querySelector(".dropdown-toggle").addEventListener("touchend", function (event) {
    event.stopPropagation();
    let dropdown = new bootstrap.Dropdown(this);
    dropdown.toggle();
});
