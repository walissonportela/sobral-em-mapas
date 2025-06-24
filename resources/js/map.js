const { Resolver } = require("laravel-mix/src/Resolver");
const { VERSION } = require("lodash");

let layersCache = {};

// Inicializa o mapa com OSM e configura o pré-carregamento de tiles
export function initializeMap() {
    var map = new ol.Map({
        target: "map",
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM(),
                preload: 10,
            }),
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([-40.35, -3.69]),
            zoom: 15,
            maxZoom: 17,
            minZoom: 10,
            projection: ol.proj.get("EPSG:3857"),
        }),
        interactions: [
            new ol.interaction.DragPan(),
            new ol.interaction.DoubleClickZoom(),
            new ol.interaction.PinchZoom(),
            new ol.interaction.MouseWheelZoom({
                duration: 100,
            }),
            new ol.interaction.DragZoom(),
            new ol.interaction.KeyboardZoom(),
            new ol.interaction.KeyboardPan(),
        ],
    });

    window.map = map;
    loadSobralBoundary();
}



// Função para carregar e adicionar o polígono de Sobral via GeoJSON
async function loadSobralBoundary() {
    // URL para o GeoJSON de Sobral
    var geojsonUrl =
        "https://polygons.openstreetmap.fr/get_geojson.py?id=302610&params=0";

    try {
        // Carrega o GeoJSON usando ol.source.Vector
        var vectorSource = new ol.source.Vector({
            url: geojsonUrl,
            format: new ol.format.GeoJSON(),
        });

        // Estilo para a linha do polígono
        var lineStyle = new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: "#FF0000", // Cor da linha (vermelho)
                width: 3, // Espessura da linha
            }),
        });

        // Cria a camada vetorial com o GeoJSON
        var vectorLayer = new ol.layer.Vector({
            source: vectorSource,
            style: lineStyle,
        });

        // Adiciona a camada ao mapa
        window.map.addLayer(vectorLayer);
    } catch (error) {
        console.error("Erro ao carregar o polígono de Sobral:", error);
    }
}

// Função para exibir mensagem de erro personalizada
async function showError(message, layerName) {
    const errorMessageElement = document.getElementById("error-message");
    const layerLabel = document.getElementById(layerName);
    const layerIconAlert = layerLabel?.nextElementSibling;

    layerLabel.classList.add("error-layer");
    layerIconAlert.classList.remove("hide-layer-alert");
    layerIconAlert.classList.add("show-layer-alert");

    errorMessageElement.textContent = message;
    errorMessageElement.style.top = "10px";
    errorMessageElement.style.display = "block";

    // Oculta a mensagem após 5 segundos (5000 milissegundos)
    await new Promise((resolve) => setTimeout(resolve, 3000));
    errorMessageElement.style.display = "none";
    errorMessageElement.style.top = "-10px";
}

// Função para adicionar uma camada WMS ao mapa com cache habilitado
async function addWmsLayer(map, layerData) {
    //console.log("🛠 Dados recebidos em addWmsLayer:", JSON.stringify(layerData, null, 2));
    if (typeof layerData === "string") {
        try {
            layerData = JSON.parse(layerData);
            //console.log("✅ JSON convertido para objeto:", layerData);
        } catch (error) {
            console.error("❌ ERRO ao converter JSON para objeto:", error);
            return;
        }
    }
    //console.log(layerData.layer_name)
    try {
       
        

        const layerName = layerData.layer_name;
        const maxScale = layerData.max_scale;
        const order = layerData.order;
        const crs = layerData.crs || "EPSG:3857"; // Padrão
        const wmsLinkId = layerData.wms_link_id;
        const legendUrl = layerData.legend_url;

        //console.log(`⚡️ Processando camada: ${layerName}`);
        //console.log("📌 CRS:", crs, "| MaxScale:", maxScale, "| Order:", order, "| WMS Link ID:", wmsLinkId);
        //console.log("🔗 URL da Legenda:", legendUrl || "Nenhuma legenda disponível");

        if (layersCache[layerName]) {
            map.addLayer(layersCache[layerName]);
            //console.log(`✅ Camada "${layerName}" carregada do cache local.`);
        } else {
            let isError = 0;
            let totalTilesLoading = 0;
            let totalTilesLoaded = 0;

            //console.log(`🛠 Criando camada WMS: ${layerName}`);

            const geoServerLayer = new ol.layer.Tile({
                source: new ol.source.TileWMS({
                    url: "api/proxy-wms",
                    params: {
                        LAYERS: layerName,
                        TILED: true,
                        FORMAT: "image/png",
                        TRANSPARENT: true,
                        VERSION: "1.3.0",
                        SRS: crs,
                    },
                    serverType: "geoserver",
                    crossOrigin: "anonymous",
                    tileLoadFunction: function (imageTile, src) {
                        //console.log(`🎯 Carregando tile: ${src}`);
                        const xhr = new XMLHttpRequest();
                        xhr.open("GET", src, true);
                        xhr.responseType = "blob";

                        xhr.onload = async function () {
                            if (xhr.status === 200) {
                                //console.log(`✅ Tile carregado com sucesso: ${src}`);
                                const reader = new FileReader();
                                reader.readAsDataURL(xhr.response);
                                reader.onload = function () {
                                    imageTile.getImage().src = reader.result;
                                };
                            } else {
                                console.error(`❌ Erro ao carregar tile ${src}: Status ${xhr.status}`);
                                await showError(`Erro ao carregar tile ${src}. Status: ${xhr.status}`, geoServerLayer.get("name"));
                            }
                        };

                        xhr.onerror = async function () {
                            console.error(`🚨 ERRO de rede ao carregar tile: ${src}`);
                            await showError(`Erro de rede ao carregar tile ${src}.`, geoServerLayer.get("name"));
                        };

                        xhr.send();
                    },
                    cacheSize: 2048,
                    preload: 4,
                }),
                name: layerName,
                zIndex: order || 1,
                maxResolution: maxScale || undefined,
            });

            //console.log(`🗺 Adicionando camada "${layerName}" ao mapa.`);
            map.addLayer(geoServerLayer);
            layersCache[layerName] = geoServerLayer;
            //console.log(`✅ Camada "${layerName}" armazenada no cache.`);

            geoServerLayer.getSource().on("tileloadstart", function () {
                totalTilesLoading++;
                //console.log(`🔄 Iniciando carregamento de tile para "${layerName}". Total carregando: ${totalTilesLoading}`);
            });

            geoServerLayer.getSource().on("tileloadend", function () {
                totalTilesLoaded++;
                //console.log(`✅ Tile carregado para "${layerName}". Total carregados: ${totalTilesLoaded}`);
            });

            geoServerLayer.getSource().on("tileloaderror", function () {
                isError++;
                console.error(`🚨 ERRO: Falha ao carregar tiles da camada "${layerName}"`);
                if (isError === 1) {
                    console.warn(`⚠️ Removendo camada "${layerName}" do mapa devido a erro.`);
                    map.removeLayer(geoServerLayer);
                    delete layersCache[geoServerLayer.get("name")];
                    showError(`A camada ${layerName} possui erros e não será carregada.`, geoServerLayer.get("name"));
                }
            });

            if (legendUrl) {
                //console.log(`📜 Legenda disponível para "${layerName}": ${legendUrl}`);
            }
        }
    } catch (error) {
        console.error(`❌ ERRO FATAL ao carregar a camada ${layerData?.layer_name || "Desconhecida"}:`, error);
       
    }
}



// Função para remover uma camada WMS específica do mapa e do cache
// Função para ocultar uma camada WMS do mapa (sem removê-la do cache)
async function removeWmsLayer(map, layerData) {
    //console.log("🛠 Dados recebidos em RemoveWmsLayer:", JSON.stringify(layerData, null, 2));
    if (typeof layerData === "string") {
        try {
            layerData = JSON.parse(layerData);
            //console.log("✅ JSON convertido para objeto:", layerData);
        } catch (error) {
            console.error("❌ ERRO ao converter JSON para objeto:", error);
            return;
        }
    }
    //console.log(layerData.layer_name)
    const layerName = layerData.layer_name;
    //console.log(`🕶 Tentando ocultar camada: ${layerName}`);

    // Obtém todas as camadas carregadas no mapa
    const layers = map.getLayers().getArray();
    //console.log("📌 Camadas carregadas no mapa:", layers.map(layer => layer.get("name") || "Sem Nome"));

    // Encontra a camada correspondente pelo nome
    const layerToRemove = layers.find(layer => layer.get("name") === layerName);

    if (layerToRemove) {
        //console.log(`✅ Ocultando camada "${layerName}" no mapa.`);
        map.removeLayer(layerToRemove); 
    } else {
        console.warn(`⚠️ Camada "${layerName}" não encontrada no mapa.`);
    }
}


// Função para manipular camadas do mapa de fora do arquivo
export function toggleLayer(map, layerName, shouldAdd) {
    // Verifica se a camada já está presente no mapa
    // const existingLayer = map
    //    .getLayers()
    //     .getArray()
    //    .find((layer) => layer.get("name") === layerName);
    if (shouldAdd) {
        //if (existingLayer) {
        // Se a camada já está no mapa, não faz nada
        //   //console.log(`A camada ${layerName} já está no mapa.`);
        //   return;
        // }
        // Se a camada não existe, chama a função para adicioná-la
        addWmsLayer(map, layerName);
    } else {
        // Remove a camada se ela estiver presente
        removeWmsLayer(map, layerName);
    }
}

export function getSelectionAreaPixels() {
    const selectionArea = document.getElementById('selection-area');
    const mapElement = document.getElementById('map');

    if (!selectionArea || !mapElement) return null;

    const selectionRect = selectionArea.getBoundingClientRect();
    const mapRect = mapElement.getBoundingClientRect();

    return {
        left: selectionRect.left - mapRect.left,
        top: selectionRect.top - mapRect.top,
        width: selectionRect.width,
        height: selectionRect.height
    };
}

export function exportVisibleMapArea(map) {
    const selection = getSelectionAreaPixels();
    if (!selection) {
        console.error('Área de seleção não encontrada.');
        return;
    }

    const { left, top, width, height } = selection;

    const format = document.getElementById('format').value;
    const scaleValue = parseFloat(document.getElementById('scale').value);

    const view = map.getView();
    const originalResolution = view.getResolution();

    // Define nova resolução com base na escala fornecida
    const dpi = 96;
    const newResolution = scaleValue / (dpi * 39.37);
    //view.setResolution(newResolution);

    // Espera o mapa renderizar tudo
    map.renderSync(); // Garante renderização imediata
    
    console.log('inciando print');
    //map.once('rendercomplete', () => {
    setTimeout(()=> {
        console.log('renderizado');
        const mapCanvas = document.createElement('canvas');
        mapCanvas.width = width;
        mapCanvas.height = height;
        const mapContext = mapCanvas.getContext('2d');

        // Itera sobre todos os canvas das camadas do OL
        document.querySelectorAll('.ol-layer canvas').forEach((canvas) => {
            if (canvas.width > 0 && canvas.height > 0) {
                const opacity = canvas.parentNode.style.opacity;
                mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);

                const transform = canvas.style.transform;
                if (transform && transform.startsWith('matrix')) {
                    const matrix = transform
                        .match(/^matrix\(([^)]+)\)$/)[1]
                        .split(',')
                        .map(Number);
                    mapContext.setTransform(...matrix);
                }

                // Recorta apenas a parte da seleção
                mapContext.drawImage(
                    canvas,
                    left, top, width, height,
                    0, 0, width, height
                );
            }
        });

        // Gera o conteúdo final
        const mime = format === 'jpg' ? 'image/jpeg' : 'image/png';
        const imgData = mapCanvas.toDataURL(mime);

        if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: width > height ? 'landscape' : 'portrait',
                unit: 'px',
                format: [width, height]
            });
            pdf.addImage(imgData, 'PNG', 0, 0, width, height);
            pdf.save('mapa_selecionado.pdf');
        } else {
            const link = document.createElement('a');
            link.download = `mapa_selecionado.${format}`;
            link.href = imgData;
            link.click();
        }

        // Restaura a resolução original
        //view.setResolution(originalResolution);
    }, 500);

    //map.renderSync(); // Garante renderização imediata
}




