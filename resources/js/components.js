import { toggleLayer, getSelectionAreaPixels, exportVisibleMapArea } from "./map";
import { handleServerResponse } from "./ui.js";

// Função para inicializar a caixa de seleção (mover e redimensionar)
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

    // Verifica se os elementos estão presentes
    if (selectionButton && selectionTools) {
        selectionButton.addEventListener("click", function () {
            // Alterna a visibilidade da div "selection-tools"
            if (
                selectionTools.style.display === "none" ||
                selectionTools.style.display === ""
            ) {
                selectionButton.classList.add("active");
                selectionTools.style.display = "block"; // Exibe o selection-tools
            } else {
                selectionTools.style.display = "none"; // Oculta o selection-tools
                selectionButton.classList.remove("active");
            }
        });
    }

    // Função para atualizar as dimensões no cabeçalho
    function updateDimensions() {
        const width = selectionArea.offsetWidth;
        const height = selectionArea.offsetHeight;
        resolution.innerHTML = `${width} x ${height}`;
    }

    // Iniciar o arraste ao clicar no cabeçalho
    dragHandle.addEventListener("mousedown", function (e) {
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;

        // Pega a posição inicial da caixa de seleção
        offsetX = selectionBox.offsetLeft;
        offsetY = selectionBox.offsetTop;

        e.preventDefault(); // Previne seleção de texto
    });

    // Mover a caixa durante o arraste
    document.addEventListener("mousemove", function (e) {
        if (isDragging) {
            const moveX = e.clientX - startX;
            const moveY = e.clientY - startY;

            // Atualizar a posição da caixa de seleção
            selectionBox.style.left = offsetX + moveX + "px";
            selectionBox.style.top = offsetY + moveY + "px";
        }
    });

    // Finalizar o arraste ao soltar o mouse
    document.addEventListener("mouseup", function () {
        isDragging = false;
        updateDimensions(); // Atualiza as dimensões após o movimento
    });

    // Atualizar as dimensões quando a página carregar
    updateDimensions();

    // Função que escuta o redimensionamento da caixa
    const resizeObserver = new ResizeObserver(() => {
        updateDimensions(); // Atualiza as dimensões após o redimensionamento
    });

    downloadButton.addEventListener('click', function () {
        exportVisibleMapArea(window.map);
    });

    if (scaleInput) {
        const debouncedScaleUpdate = debounce(updateMapFromScaleInput, 400);
        scaleInput.addEventListener('input', debouncedScaleUpdate);
    }


    // Observar mudanças na caixa de seleção
    resizeObserver.observe(selectionBox);
}

// botao flutuante de Medir
function initializeFloatingButton() {
    const floatingButton = document.getElementById("floating-button");
    const measureButton = document.getElementById("btn-measure");
    floatingButton.style.display = "none";

    // Função para alternar a visibilidade do botão flutuante
    const toggleFloatingButtonVisibility = () => {
        floatingButton.style.display = floatingButton.style.display === "none" ? "block" : "none";
    };

    // Adicionando eventos de clique para o botão "Medir"
    measureButton.addEventListener("click", toggleFloatingButtonVisibility);
    
    // Adicionando evento de toque para dispositivos móveis
    measureButton.addEventListener("touchend", (e) => {
        e.preventDefault(); // Previne o comportamento padrão
        toggleFloatingButtonVisibility();
    });

    function dragElement(el) {
        let pos1 = 0,
            pos2 = 0,
            pos3 = 0,
            pos4 = 0;

        const startDrag = (e) => {
            // Previne arrastar quando é um clique em dropdown ou select
            if (e.target.closest(".dropdown-menu") || e.target.closest("select")) {
                return;
            }

            e.preventDefault(); // Previne o comportamento padrão
            pos3 = e.type === "mousedown" ? e.clientX : e.touches[0].clientX;
            pos4 = e.type === "mousedown" ? e.clientY : e.touches[0].clientY;

            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;

            // Para touch
            document.ontouchend = closeDragElement;
            document.ontouchmove = elementDrag;
        };

        const elementDrag = (e) => {
            e.preventDefault(); // Previne o comportamento padrão
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

        // Adicionando os event listeners para mouse e toque
        el.onmousedown = startDrag;
        el.ontouchstart = startDrag; // Para dispositivos móveis
    }

    // Ativando a funcionalidade de arraste no botão flutuante
    if (floatingButton) {
        dragElement(floatingButton);
    }

}
document.querySelector(".dropdown-toggle").addEventListener("touchend", function (event) {
    event.stopPropagation();
    let dropdown = new bootstrap.Dropdown(this);
    dropdown.toggle();
});


// Card com opcoes de medição
function initializeMeasure() {
    let draw;
    let sketch;
    let helpTooltipElement;
    let measureTooltipElement;
    let measureTooltip;
    let helpTooltip;
    let selectedLineColor = "#ffcc33"; // Cor padrão da linha e bolinha
    let selectedLineWidth = 2; // Largura padrão

    // Elementos HTML
    const measureLineButton = document.getElementById("measure-line");
    const measureAreaButton = document.getElementById("measure-area");
    const lineColorPicker = document.getElementById("line-color-picker");
    const lineWidthPicker = document.getElementById("line-width-picker");
    const lineWidthValue = document.getElementById("line-width-value");
    const clearDrawingsButton = document.getElementById("clear-drawings");
    const stopDrawingButton = document.getElementById("stop-drawing");

    const source = new ol.source.Vector({
        wrapX: false,
    });

    // Camada de vetor para as geometrias
    const vectorLayer = new ol.layer.Vector({
        source: source,
        style: function (feature) {
            return new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: selectedLineColor,
                    width: 2,
                }),
                fill: new ol.style.Fill({
                    color: hexToRGBA(selectedLineColor, 0.4), // Cor preenchida semi-transparente
                }),
                image: new ol.style.Circle({
                    radius: 5,
                    fill: new ol.style.Fill({
                        color: selectedLineColor, // Cor da bolinha
                    }),
                    stroke: new ol.style.Stroke({
                        color: "#000000",
                        width: 1,
                    }),
                }),
            });
        },
    });

    window.map.addLayer(vectorLayer);

    // Converte hexadecimal para RGBA
    function hexToRGBA(hex, alpha) {
        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Atualiza a cor da linha e do polígono com base no seletor de cor
    function updateLineColor(color) {
        selectedLineColor = color;
        vectorLayer.setStyle(function (feature) {
            return new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: selectedLineColor,
                    width: 2,
                }),
                fill: new ol.style.Fill({
                    color: hexToRGBA(selectedLineColor, 0.4), // Preenchimento semi-transparente
                }),
                image: new ol.style.Circle({
                    radius: 5,
                    fill: new ol.style.Fill({
                        color: selectedLineColor, // Bolinha com a cor atual
                    }),
                    stroke: new ol.style.Stroke({
                        color: "#000000",
                        width: 1,
                    }),
                }),
            });
        });
    }

    // Define o tipo de desenho (linha ou polígono)
    function setDrawType(type) {
        if (draw) {
            window.map.removeInteraction(draw);
        }

        draw = new ol.interaction.Draw({
            source: source,
            type: type,
            style: function (feature) {
                return new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: selectedLineColor,
                        width: 2,
                        lineDash: [10, 10],
                    }),
                    fill: new ol.style.Fill({
                        color: hexToRGBA(selectedLineColor, 0.4), // Preenchimento semi-transparente durante o desenho
                    }),
                    image: new ol.style.Circle({
                        radius: 5,
                        fill: new ol.style.Fill({
                            color: selectedLineColor, // Cor da bolinha
                        }),
                        stroke: new ol.style.Stroke({
                            color: "#000000",
                            width: 1,
                        }),
                    }),
                });
            },
        });

        window.map.addInteraction(draw);
        createMeasureTooltip();
        createHelpTooltip();

        draw.on("drawstart", function (evt) {
            sketch = evt.feature;
            let tooltipCoord = evt.coordinate;

            sketch.getGeometry().on("change", function (evt) {
                const geom = evt.target;
                let output;
                if (geom instanceof ol.geom.Polygon) {
                    output = `<span>${formatArea(geom)}</span>`;
                    tooltipCoord = geom.getInteriorPoint().getCoordinates();
                } else if (geom instanceof ol.geom.LineString) {
                    output = `<span>${formatLength(geom)}</span>`;
                    tooltipCoord = geom.getLastCoordinate();
                }
                measureTooltipElement.innerHTML = output;
                measureTooltip.setPosition(tooltipCoord);
            });
        });

        draw.on("drawend", function () {
            sketch.setStyle(
                new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: selectedLineColor,
                        width: 5,
                        lineDash: null,
                    }),
                    fill: new ol.style.Fill({
                        color: hexToRGBA(selectedLineColor, 0.4), // Preenchimento após o desenho
                    }),
                    image: new ol.style.Circle({
                        radius: 5,
                        fill: new ol.style.Fill({
                            color: selectedLineColor,
                        }),
                        stroke: new ol.style.Stroke({
                            color: "#000000",
                            width: 1,
                        }),
                    }),
                })
            );
            measureTooltipElement.className = "ol-tooltip ol-tooltip-static";
            measureTooltip.setOffset([0, -7]);
            sketch = null;
            measureTooltipElement = null;
            createMeasureTooltip();
        });
    }

    // Criação dos tooltips de ajuda e medição
    function createHelpTooltip() {
        if (helpTooltipElement) {
            helpTooltipElement.remove();
        }
        helpTooltipElement = document.createElement("div");
        helpTooltipElement.className = "ol-tooltip hidden";
        helpTooltip = new ol.Overlay({
            element: helpTooltipElement,
            offset: [15, 0],
            positioning: "center-left",
        });
        window.map.addOverlay(helpTooltip);
    }

    function createMeasureTooltip() {
        if (measureTooltipElement) {
            measureTooltipElement.remove();
        }
        measureTooltipElement = document.createElement("div");
        measureTooltipElement.className = "ol-tooltip ol-tooltip-measure";
        measureTooltip = new ol.Overlay({
            element: measureTooltipElement,
            offset: [0, -15],
            positioning: "bottom-center",
            stopEvent: false,
            insertFirst: false,
        });
        window.map.addOverlay(measureTooltip);
    }

    // Formata área e comprimento
    function formatArea(polygon) {
        const area = ol.sphere.getArea(polygon);
        return area > 10000
            ? `${Math.round((area / 1000000) * 100) / 100} km²`
            : `${Math.round(area * 100) / 100} m²`;
    }

    function formatLength(line) {
        const length = ol.sphere.getLength(line);
        return length > 100
            ? `${Math.round((length / 1000) * 100) / 100} km`
            : `${Math.round(length * 100) / 100} m`;
    }

    // Limpa todas as geometrias desenhadas e tooltips
    function clearDrawings() {
        source.clear();
        window.map
            .getOverlays()
            .getArray()
            .slice()
            .forEach(function (overlay) {
                if (overlay.getElement().classList.contains("ol-tooltip")) {
                    window.map.removeOverlay(overlay);
                }
            });
        if (measureTooltipElement) {
            measureTooltipElement.innerHTML = "";
        }
        if (helpTooltipElement) {
            helpTooltipElement.classList.add("hidden");
        }
        createMeasureTooltip();
        createHelpTooltip();
    }

    // Eventos para definir o tipo de medição
    measureLineButton.addEventListener("click", function (event) {
        event.preventDefault();
        setDrawType("LineString");
    });

    measureAreaButton.addEventListener("click", function (event) {
        event.preventDefault();
        setDrawType("Polygon");
    });

    // Atualiza a cor conforme a escolha do usuário
    lineColorPicker.addEventListener("input", function () {
        updateLineColor(this.value);
    });

    clearDrawingsButton.addEventListener("click", function () {
        clearDrawings();
    });

    // Adiciona um evento para o botão que para o desenho
    stopDrawingButton.addEventListener("click", function (event) {
        event.preventDefault();
        if (draw) {
            window.map.removeInteraction(draw);
            draw = null; // Limpa a variável draw para que não haja referências pendentes
        }
    });
}

// Mover pro map.js depois
// Atualiza o input #scale com a escala baseada na resolução atual
function updateScaleInputFromMap() {
    const view = window.map.getView();
    const scaleInput = document.getElementById('scale');
    if (!view || !scaleInput) return;

    const resolution = view.getResolution();
    const escala = resolutionToScale(resolution);
    scaleInput.value = escala;
}

const DPI = 96;
// Converte resolução do mapa para escala (ex: 1:10000)
function resolutionToScale(resolution) {
    return Math.round(resolution * DPI * 39.37);
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

    // Atualiza ao carregar e sempre que a resolução mudar
    updateUI();
    view.on('change:resolution', updateUI);
}

// retarda a ação input
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Converte escala para resolução
function scaleToResolution(scale) {
    return scale / (DPI * 39.37);
}

// Altera o zoom do mapa baseado no valor digitado no input de escala
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


export function InitializeComponents() {
    initializeSelectionBox();
    initializeResolutionWatcher();
    initializeFloatingButton();
    initializeMeasure();
    updateScaleInputFromMap();
}
