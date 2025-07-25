import { toggleLayer } from "./map";


// Função para inicializar a sidebar
function initializeSidebar() {
    const sidebar = document.getElementById("mainSidebar");
    const toggleButton = document.getElementById("toggleSidebar");

    toggleButton.addEventListener("click", () => {
        sidebar.classList.toggle("sidebar-collapsed");
    });
}

// Inicializa os tooltips do Bootstrap
function initializeTooltip() {
    const elements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    elements.forEach((el) => {
        new bootstrap.Tooltip(el);
    });
}

function initializeSearch() {
    const searchInput = document.querySelector(".input-search");
    const searchButton = document.querySelector("#btn-search");
    const clearButton = document.querySelector(".clear-search");

    // Alternar visibilidade do campo de busca ao clicar no botão 🔍
    searchButton.addEventListener("click", () => {
        searchInput.classList.toggle("hidden");
        if (!searchInput.classList.contains("hidden")) {
            searchInput.focus();
        }
    });

    // Função de busca
    function searchLayers() {
        let searchTerm = searchInput.value.toLowerCase().trim();
        let hasResults = false;

        // 🔄 Fecha todos os acordeões antes de iniciar a busca
        document.querySelectorAll(".accordion-collapse").forEach(collapse => collapse.classList.remove("show"));
        document.querySelectorAll(".accordion-button").forEach(button => {
            button.classList.add("collapsed");
            button.setAttribute("aria-expanded", "false");
        });

        // Oculta todas as categorias e subcategorias inicialmente
        document.querySelectorAll(".sub-list li").forEach(layer => layer.style.display = "none");
        document.querySelectorAll(".accordion-item.sub, .accordion-item.cat").forEach(el => el.style.display = "none");

        // Percorre todas as categorias
        document.querySelectorAll(".accordion-item.cat").forEach((category) => {
            let categoryHasResults = false;

            // Percorre todas as subcategorias dentro da categoria
            category.querySelectorAll(".accordion-item.sub").forEach((subcategory) => {
                let subcategoryHasResults = false;

                // Percorre todas as layers dentro da subcategoria
                subcategory.querySelectorAll(".sub-list li").forEach((layer) => {
                    let layerLabel = layer.querySelector("label");
                    let layerCheckbox = layer.querySelector(".layer-toggle");
                    let layerName = layerLabel.textContent.toLowerCase();
                    let isMatch = layerName.includes(searchTerm);

                    // Exibe apenas as camadas que correspondem à busca
                    if (isMatch) {
                        layer.style.display = "block";
                        subcategoryHasResults = true;
                    }

                    // Se o nome for exatamente igual ao buscado, seleciona a checkbox e adiciona ao mapa
                    if (layerCheckbox && layerCheckbox.getAttribute("data-layer")) {
                        let layerData;
                        
                        try {
                            layerData = JSON.parse(layerCheckbox.getAttribute("data-layer").replace(/&quot;/g, '"'));
                            if (typeof layerData === "string") {
                                layerData = JSON.parse(layerData);
                            }
                   
                            if (layerData.name.toLowerCase() === searchTerm) {
                                layerCheckbox.checked = true;
                                console.log(`🔹 Tentando disparar evento "change" para: ${layerCheckbox.id}`);
                                layerCheckbox.dispatchEvent(new Event("change")); // 🚀 Força o evento
                                console.log(`✅ Evento "change" disparado para: ${layerCheckbox.id}`);
                                console.log(`✅ Selecionando automaticamente: ${layerData.layer_name}`);
                                
                                // Atualiza estatísticas
                                window.updateStatistics(layerData, true);
                                
                                // Adiciona a camada ao mapa
                                toggleLayer(window.map, layerData, true);
                            }
                        } catch (error) {
                            console.error("❌ ERRO ao processar data-layer:", error);
                        }
                    }
                });

                // Se houver resultados na subcategoria, exibe ela, mas NÃO abre automaticamente
                if (subcategoryHasResults) {
                    subcategory.style.display = "block";
                    categoryHasResults = true;
                }
            });

            // Se houver subcategorias com resultados, exibe a categoria
            if (categoryHasResults) {
                category.style.display = "block";
                hasResults = true;
            }
        });

        // 🔄 **Apenas abre os acordeões se houver resultado**
        if (hasResults) {
            document.querySelectorAll(".accordion-item.cat").forEach(category => {
                if (category.style.display === "block") {
                    let categoryButton = category.querySelector(".accordion-button");
                    categoryButton.classList.remove("collapsed");
                    categoryButton.setAttribute("aria-expanded", "true");
                    document.querySelector(`#${categoryButton.getAttribute("data-bs-target").substring(1)}`).classList.add("show");
                }
            });

            document.querySelectorAll(".accordion-item.sub").forEach(subcategory => {
                if (subcategory.style.display === "block") {
                    let subCategoryButton = subcategory.querySelector(".accordion-button");
                    subCategoryButton.classList.remove("collapsed");
                    subCategoryButton.setAttribute("aria-expanded", "true");
                    document.querySelector(`#${subCategoryButton.getAttribute("data-bs-target").substring(1)}`).classList.add("show");
                }
            });
        } else {
            console.warn("Nenhuma camada correspondente encontrada.");
        }
    }

    // **Modificação importante**: Remove a abertura automática ao clicar no botão de pesquisa
    searchButton.addEventListener("click", () => {
        if (searchInput.value.trim() !== "") {
            searchLayers();
        }
    });

    // Evento de pressionar Enter no campo de busca
    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            searchLayers();
        }
    });

    // Exibe ou esconde o botão de limpar dentro do input
    searchInput.addEventListener("input", () => {
        clearButton.style.display = searchInput.value ? "block" : "none";
    });

    // Evento de clique no botão de limpar busca
    clearButton.addEventListener("click", function () {
        searchInput.value = "";
        clearButton.style.display = "none";

        // 🔄 Reseta a exibição para mostrar todas as camadas
        document.querySelectorAll(".sub-list li").forEach(layer => layer.style.display = "block");
        document.querySelectorAll(".accordion-item.sub, .accordion-item.cat").forEach(el => el.style.display = "block");

        // 🔄 **Reseta os acordeões para o estado fechado**
        document.querySelectorAll(".accordion-collapse").forEach(collapse => collapse.classList.remove("show"));
        document.querySelectorAll(".accordion-button").forEach(button => {
            button.classList.add("collapsed");
            button.setAttribute("aria-expanded", "false");
        });
    });
}




    

function toggleFullScreen() {
    // Verifica se o navegador está no modo de tela cheia
    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
        // Para navegadores que não são Safari
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch((err) => {
                console.log(
                    `Erro ao tentar ativar o modo de tela cheia: ${err.message}`
                );
            });
        }
        // Para o Safari no iPhone
        else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        }
    } else {
        // Para sair do modo de tela cheia
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    }
}


// Função para inicializar o botão de expandir (fullscreen)
function initializeExpandButton() {
    const expandButton = document.getElementById("btn-expand");
    const topBar = document.querySelector(".topbar");
    const mapEl = document.getElementById("map");
    expandButton.addEventListener("click", toggleFullScreen);

    // Alterna o ícone ao entrar e sair do modo fullscreen
    document.addEventListener("fullscreenchange", () => {
        const icon = expandButton.querySelector("i");
        if (document.fullscreenElement) {
            mapEl.style.height = "100vh";
            topBar.classList.add("hidden-topbar");
            icon.classList.remove("fa-expand-arrows-alt");
            icon.classList.add("fa-compress-arrows-alt"); // Ícone de "sair de tela cheia"
        } else {
            topBar.classList.remove("hidden-topbar");
            mapEl.style.height = "calc(100vh - 60px)";
            icon.classList.remove("fa-compress-arrows-alt");
            icon.classList.add("fa-expand-arrows-alt"); // Ícone de "expandir"
        }
    });
}

// Função para inicializar os toggles de camadas (checkboxes)
// Inicializa os toggles das camadas
function initializeLayerToggles() {
    document.querySelectorAll(".layer-toggle").forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            // Converte o atributo data-layer de volta para objeto JSON
            const layerData = JSON.parse(this.getAttribute("data-layer"));

            // Chama toggleLayer passando o mapa e os dados da camada
            toggleLayer(window.map, layerData, this.checked);

            // Atualiza a exibição das legendas
            updateLegends(layerData, this.checked);
        });
    });
}

// Função para atualizar as legendas em "Mapas Ativos"
function updateLegends(layerData, isChecked) {
    console.log("🛠 Dados recebidos em RemoveWmsLayer:", JSON.stringify(layerData, null, 2));
    
    if (typeof layerData === "string") {
        try {
            layerData = JSON.parse(layerData);
            console.log("✅ JSON convertido para objeto:", layerData);
        } catch (error) {
            console.error("❌ ERRO ao converter JSON para objeto:", error);
            return;
        }
    }
    const layerName = layerData.layer_name;

    const layerElement = document.getElementById(`active-layer-${layerName}`);
    if (layerElement) {
        if (isChecked) {
            // Se marcado, exibe a camada nos "Mapas Ativos"
            layerElement.style.display = "block";
            console.log(`✅ Camada ${layerName} adicionada à seção de legendas.`);
        } else {
            // Se desmarcado, oculta dos "Mapas Ativos"
            layerElement.style.display = "none";
            console.log(`❌ Camada ${layerName} removida da seção de legendas.`);
        }
    } else {
        console.warn(`⚠️ Elemento de legenda para "${layerName}" não encontrado.`);
    }
}


function enableSwipeToDeleteAccordion(accordionId) {
    const items = document.querySelectorAll(`#${accordionId} .accordion-item`);

    items.forEach((item) => {
        let startX = 0;
        let currentX = 0;
        let threshold = 80;
        let isSwiping = false;
        let isMouseDown = false;
        let isMoving = false;
        let allowSwipe = false;
        let holdTimeout = null;

        // Pega os dados da layer (você pode ajustar isso conforme sua estrutura)
        const layerData = item.dataset.layer ? JSON.parse(item.dataset.layer) : null;

        function startSwipe(x) {
            startX = x;
            isSwiping = true;
            isMoving = false;
        }

        function moveSwipe(x) {
            if (!isSwiping || !allowSwipe) return;
            currentX = x;
            let deltaX = currentX - startX;

            if (Math.abs(deltaX) > 10) isMoving = true;

            if (Math.abs(deltaX) > threshold && allowSwipe) {
                item.classList.add("layer-deleting");
            }

            if (deltaX < 0) {
                item.style.transform = `translateX(${deltaX}px)`;
            }
        }

        function endSwipe() {
            if (isMoving) {
                let deltaX = currentX - startX;

                if (Math.abs(deltaX) > threshold && allowSwipe) {
                    item.style.transition = "transform 0.3s ease";
                    item.style.transform = `translateX(-100%)`;

                    setTimeout(() => {
                        // Desativa camada no mapa
                        if (layerData) {
                            toggleLayer(window.map, layerData, false);
                            updateLegends(layerData, false);
                        }

                        // Desmarca o checkbox dentro do item, se existir
                        const checkbox = item.querySelector("input[type='checkbox']");
                        if (checkbox) checkbox.checked = false;

                        item.remove();
                    }, 300);
                } else {
                    item.style.transition = "transform 0.3s ease";
                    item.style.transform = "translateX(0)";
                    item.classList.remove("layer-deleting");
                }
            }

            isMoving = false;
            isSwiping = false;
            isMouseDown = false;
            allowSwipe = false;
        }

        function startHold(x) {
            holdTimeout = setTimeout(() => {
                allowSwipe = true;
                startSwipe(x); // só inicia o swipe após 5 segundos
            }, 5000); // 5 segundos
        }

        function cancelHold() {
            clearTimeout(holdTimeout);
        }

        // --- Touch events (mobile) ---
        item.addEventListener("touchstart", (e) => {
            startHold(e.touches[0].clientX);
        });

        item.addEventListener("touchmove", (e) => {
            if (allowSwipe) e.preventDefault(); // bloqueia scroll
            moveSwipe(e.touches[0].clientX);
        }, { passive: false });

        item.addEventListener("touchend", () => {
            cancelHold();
            endSwipe();
        });

        // --- Mouse events (desktop) ---
        item.addEventListener("mousedown", (e) => {
            isMouseDown = true;
            startHold(e.clientX);
        });

        item.addEventListener("mousemove", (e) => {
            if (!isMouseDown) return;
            moveSwipe(e.clientX);
        });

        item.addEventListener("mouseup", () => {
            cancelHold();
            endSwipe();
        });

        item.addEventListener("mouseleave", () => {
            cancelHold();
            if (isMouseDown && isMoving) {
                endSwipe();
            }
        });
    });
}
function enableCloseButtonAccordion(accordionId) {
    const items = document.querySelectorAll(`#${accordionId} .accordion-item`);

    items.forEach((item) => {
        // Evita adicionar o botão mais de uma vez
        if (item.querySelector(".close-button")) return;

        // Cria o botão de fechar
        const closeBtn = document.createElement("button");
        closeBtn.className = "close-button";
        closeBtn.textContent = "✕";
        closeBtn.setAttribute("data-bs-toggle", "tooltip");
        closeBtn.setAttribute("title", "Clique aqui para remover a camada");
        closeBtn.setAttribute("aria-label", "Fechar camada");
        closeBtn.style.cssText = `
            position: absolute;
            top: 30px;
            right: 10px;
            background: transparent;
            border: none;
            font-size: 18px;
            cursor: pointer;
            display: none; /* Oculto inicialmente */
        `;
        // Adiciona o botão ao item
        item.style.position = "relative";
        item.appendChild(closeBtn);
        new bootstrap.Tooltip(closeBtn);

        // Evento de clique no botão
        closeBtn.addEventListener("click", () => {
            // Extrai o ID da camada do atributo ID do item
            const layerId = item.id.replace("active-layer-", "");
            if (layerId) {
                const layerData = { layer_name: layerId };
                console.log(`❌${layerData } `)
                // Remove camada do mapa
                toggleLayer(window.map, layerData, false);

                // Atualiza legenda
                updateLegends(layerData, false);
            }

           // Tenta encontrar o checkbox correspondente com ID igual ao layerId
                const checkbox = document.querySelector(`input.layer-toggle[id="${layerId}"]`);

                if (checkbox) {
                    checkbox.checked = false;
                }
        });
        // Detecta quando o acordeão é aberto ou fechado
        const collapse = item.querySelector(".accordion-collapse");

        if (collapse) {
            collapse.addEventListener("show.bs.collapse", () => {
                closeBtn.style.display = "block";
            });

            collapse.addEventListener("hide.bs.collapse", () => {
                closeBtn.style.display = "none";
            });
        }
   
    });
}




// Função para inicializar os botões da Action Bar que alterna entre seções dentro da sidebar
function initializeActionButtons() {
    const btnCamadas = document.getElementById("btn-camadas");
    const btnMapasAtivos = document.getElementById("btn-mapas-ativos");
    const btnImpressao = document.getElementById("btn-imprimir");
    const selectionBox = document.getElementById("selection-box");

    btnImpressao.addEventListener("click", function () {
        // Alterna a visibilidade do componente
        if (
            selectionBox.style.display === "none" ||
            selectionBox.style.display === ""
        ) {
            btnImpressao.classList.add("active");
            selectionBox.style.display = "flex"; // Exibe o componente
        } else {
            btnImpressao.classList.remove("active");
            selectionBox.style.display = "none"; // Oculta o componente
        }
    });

    btnCamadas.addEventListener("click", function () {
        // Exibe a div de Camadas e oculta a div de Mapas Ativos
        document.getElementById("view-camadas").style.display = "block";
        document.getElementById("view-mapas-ativos").style.display = "none";
        btnCamadas.classList.add("active");
        btnMapasAtivos.classList.remove("active");
    });

    btnMapasAtivos.addEventListener("click", function () {
        // Exibe a div de Mapas Ativos e oculta a div de Camadas
        document.getElementById("view-camadas").style.display = "none";
        document.getElementById("view-mapas-ativos").style.display = "block";
        btnMapasAtivos.classList.add("active");
        btnCamadas.classList.remove("active");
    });
}

function statistic() {
    console.log("📊 Função statistic() inicializada...");

    let tempoInicio = Date.now();
    let mapasSelecionados = {};              // { mapa: tempo acumulado em ms }
    let mapasAtivosTimestamp = {};           // { mapa: timestamp de ativação }
    let recommendedMapActivations = {};      // { mapa: contagem de ativações }
    let mapaAnteriorPorRecomendado = {};  // { "Mapa Recomendado": "Mapa Anterior" }
    let ultimoMapaAtivado = null;


    // Gera um ID de sessão único se não existir
    let sessionId = sessionStorage.getItem("sessionId");
    if (!sessionId) {
        sessionId = Math.floor(100000 + Math.random() * 900000).toString();
        sessionStorage.setItem("sessionId", sessionId);
    }
    console.log(`🆔 ID da sessão: ${sessionId}`);

    function atualizarMapas(layerData, isChecked) {
        if (typeof layerData === "string") {
            try {
                layerData = JSON.parse(layerData);
            } catch (error) {
                console.error("❌ ERRO ao converter JSON:", error);
                return;
            }
        }

        const layerName = layerData.layer_name;
        const agora = Date.now();

        // Inicializa se necessário
        if (!mapasSelecionados[layerName]) {
            mapasSelecionados[layerName] = 0;
        }

        if (!isChecked) {
            // Desativou: calcula tempo e limpa timestamp
            if (mapasAtivosTimestamp[layerName]) {
                const tempoAtivo = agora - mapasAtivosTimestamp[layerName];
                mapasSelecionados[layerName] += tempoAtivo;
                console.log(`🕒 Mapa "${layerName}" desmarcado. Tempo acumulado: ${mapasSelecionados[layerName]}ms`);
                delete mapasAtivosTimestamp[layerName];
            }
            return;
        }

        // Ativou: registra timestamp se ainda não tiver
        if (!mapasAtivosTimestamp[layerName]) {
            mapasAtivosTimestamp[layerName] = agora;
            console.log(`🟢 Mapa "${layerName}" ativado em ${agora}`);
        }

        // Se for recomendado
         if (typeof recommendedLayersStats !== 'undefined' && recommendedLayersStats[layerName]) {
        if (ultimoMapaAtivado && ultimoMapaAtivado !== layerName) {
            mapaAnteriorPorRecomendado[layerName] = ultimoMapaAtivado;
            console.log(`🔗 Mapa anterior a "${layerName}" foi "${ultimoMapaAtivado}"`);
        }
        recommendedMapActivations[layerName] = (recommendedMapActivations[layerName] || 0) + 1;
        }

        // Atualiza último ativado
        ultimoMapaAtivado = layerName;
    }

    window.updateStatistics = atualizarMapas;

    // Monitora alterações nos checkboxes
    document.addEventListener("change", function (event) {
        if (event.target.classList.contains("layer-toggle")) {
            let rawData = event.target.getAttribute("data-layer");
            if (!rawData) return;

            try {
                let layerData = JSON.parse(JSON.parse(rawData)); // JSON duplo
                atualizarMapas(layerData, event.target.checked);
                console.log(`🛠 Camada "${layerData.layer_name}" foi ${event.target.checked ? "selecionada" : "desmarcada"}`);
            } catch (e) {
                console.error("❌ Erro ao processar camada:", e);
            }
        }
    });

    function enviarEstatisticas() {
        const agora = Date.now();

        // Finaliza tempo dos mapas ainda ativos
        for (let mapa in mapasAtivosTimestamp) {
            const tempoAtivo = agora - mapasAtivosTimestamp[mapa];
            mapasSelecionados[mapa] = (mapasSelecionados[mapa] || 0) + tempoAtivo;
            mapasAtivosTimestamp[mapa] = agora; // reinicia timestamp para próxima contagem
        }

        const tempoTotal = Math.round((agora - tempoInicio) / 1000); // em segundos

        const estatisticas = {
            session_id: sessionId,
            mapas_selecionados: mapasSelecionados, // { mapa: tempo }
            tempo_total: tempoTotal,
            recommended_map_activations: recommendedMapActivations,
            mapa_anterior_por_recomendado: mapaAnteriorPorRecomendado
        };

        console.log("📤 Enviando estatísticas:", estatisticas);

        fetch(`${window.location.origin}/sobralmapas/public/api/estatisticas`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(estatisticas),
        })
        .then((response) => response.json())
        .then((data) => console.log("✅ Estatísticas enviadas:", data))
        .catch((error) => console.error("❌ Erro ao enviar estatísticas:", error));
    }

    // Envia estatísticas ao sair da página
    window.addEventListener("beforeunload", enviarEstatisticas);

    // (Opcional) envie a cada 30s:
    // setInterval(enviarEstatisticas, 30000);
}

let recommendedLayersStats = {}; // Armazena mapas recomendados
let recommendedMapActivations = {};
export function handleServerResponse(responseData) {
    // Verifica se há um objeto com `map_type` na resposta
    const mapTypeData = responseData.find(item => item.custom && item.custom.map_type);
    const recommendationData = responseData.find(item => item.custom && item.custom.recommended_map);

    if (recommendationData) {
        const recommendedMap = recommendationData.custom.recommended_map.toLowerCase();
        console.log(`📍 Mapa recomendado pelo chatbot: ${recommendedMap}`);

        // Salva a recomendação para comparação futura
        recommendedLayersStats[recommendedMap] = true;
    }
    
    if (mapTypeData) {
        const mapType = mapTypeData.custom.map_type.toLowerCase();
        console.log(`📍 Tentando marcar a camada: ${mapType}`);

        // Expande o menu lateral automaticamente
        const sidebar = document.getElementById("sidebar");
        if (sidebar) {
            sidebar.classList.add("open"); // Certifique-se de que essa classe abre o menu
        }
         // **Verifica se `mapType` é uma categoria ou subcategoria**
         expandCategoryIfNeeded(mapType);
        // Percorre todas as camadas e encontra a que corresponde ao `map_type`
        let foundLayer = false;
        document.querySelectorAll(".layer-toggle").forEach(layerCheckbox => {
            let layerData;

            try {
                layerData = JSON.parse(layerCheckbox.getAttribute("data-layer").replace(/&quot;/g, '"'));
                if (typeof layerData === "string") {
                    layerData = JSON.parse(layerData);
                }

                // Se o `map_type` for igual ao nome da camada, marca e ativa
                if (layerData.name.toLowerCase() === mapType) {
                    foundLayer = true;
                    layerCheckbox.checked = true;
                    console.log(`✅ Marcando automaticamente: ${layerData.layer_name}`);

                    // 🚀 Disparar evento "change" para ativar a camada no mapa
                    layerCheckbox.dispatchEvent(new Event("change"));

                    // Atualiza estatísticas
                    window.updateStatistics(layerData, true);

                    // Adiciona a camada ao mapa
                    toggleLayer(window.map, layerData, true);

                    // **Abre automaticamente a categoria e subcategoria**
                    expandCategoryAndSubcategory(layerCheckbox);
                }
            } catch (error) {
                console.error("❌ ERRO ao processar data-layer:", error);
            }
        });

        if (!foundLayer) {
            console.warn("⚠ Nenhuma camada correspondente encontrada para:", mapType);
        }
    }
}
function expandCategoryAndSubcategory(layerCheckbox) {
    // Encontra a subcategoria e categoria associadas
    let subcategory = layerCheckbox.closest(".accordion-item.sub");
    let category = layerCheckbox.closest(".accordion-item.cat");

    // Expande a subcategoria se estiver fechada
    if (subcategory) {
        subcategory.style.display = "block";
        let subCategoryButton = subcategory.querySelector(".accordion-button");
        if (subCategoryButton) {
            subCategoryButton.classList.remove("collapsed");
            subCategoryButton.setAttribute("aria-expanded", "true");
            let subCategoryContent = document.querySelector(`#${subCategoryButton.getAttribute("data-bs-target").substring(1)}`);
            if (subCategoryContent) {
                subCategoryContent.classList.add("show");
            }
        }
    }

    // Expande a categoria se estiver fechada
    if (category) {
        category.style.display = "block";
        let categoryButton = category.querySelector(".accordion-button");
        if (categoryButton) {
            categoryButton.classList.remove("collapsed");
            categoryButton.setAttribute("aria-expanded", "true");
            let categoryContent = document.querySelector(`#${categoryButton.getAttribute("data-bs-target").substring(1)}`);
            if (categoryContent) {
                categoryContent.classList.add("show");
            }
        }
    }
}
// **Função auxiliar para expandir categorias**
function expandCategory(categoryId) {
    let categoryButton = document.querySelector(`button[data-bs-target="#${categoryId}"]`);
    if (categoryButton) {
        let categoryCollapse = document.getElementById(categoryId);
        if (categoryCollapse && !categoryCollapse.classList.contains("show")) {
            console.log(`📂 Expandindo categoria: ${categoryId}`);
            categoryButton.click(); // Simula clique para abrir
        }
    }
}

// 🔹 NOVA FUNÇÃO: Expande categorias ou subcategorias automaticamente
// 🔹 NOVA VERSÃO ROBUSTA: Expande categoria e subcategoria automaticamente com base no nome da camada
function expandCategoryIfNeeded(layerName) {
    console.log(`🕵️ Procurando camada com o nome: ${layerName}`);

    let targetLayer = Array.from(document.querySelectorAll(".layer-toggle")).find(input => {
        try {
            let layerDataRaw = input.getAttribute("data-layer");
            if (!layerDataRaw) return false;

            // Decode de &quot; para aspas reais
            layerDataRaw = layerDataRaw.replace(/&quot;/g, '"');

            let layerData = JSON.parse(layerDataRaw);
            if (typeof layerData === "string") {
                layerData = JSON.parse(layerData);
            }

            if (!layerData.layer_name) return false;

            return layerData.layer_name.toLowerCase() === layerName.toLowerCase();
        } catch (e) {
            console.warn("⚠️ Erro ao parsear data-layer:", e);
            return false;
        }
    });

    if (targetLayer) {
        console.log(`✅ Camada encontrada no DOM! Expandindo categorias relacionadas...`);

        // Encontra subcategoria
        let subcategory = targetLayer.closest(".accordion-item.sub");
        let category = targetLayer.closest(".accordion-item.cat");

        if (subcategory) {
            let subcategoryButton = subcategory.querySelector(".accordion-button");
            let subcategoryId = subcategoryButton?.getAttribute("data-bs-target")?.replace("#", "");

            if (subcategoryButton && subcategoryId) {
                console.log(`📂 Subcategoria identificada: ${subcategoryId}`);

                let subcategoryCollapse = document.getElementById(subcategoryId);
                if (subcategoryCollapse && !subcategoryCollapse.classList.contains("show")) {
                    console.log(`📂 Expandindo subcategoria: ${subcategoryId}`);
                    subcategoryButton.click();
                }
            } else {
                console.warn("⚠️ Botão ou ID da subcategoria não encontrado.");
            }
        }

        if (category) {
            let categoryButton = category.querySelector(".accordion-button");
            let categoryId = categoryButton?.getAttribute("data-bs-target")?.replace("#", "");

            if (categoryButton && categoryId) {
                console.log(`📂 Categoria identificada: ${categoryId}`);

                let categoryCollapse = document.getElementById(categoryId);
                if (categoryCollapse && !categoryCollapse.classList.contains("show")) {
                    console.log(`📂 Expandindo categoria: ${categoryId}`);
                    categoryButton.click();
                }
            } else {
                console.warn("⚠️ Botão ou ID da categoria não encontrado.");
            }
        }

        return; // Já resolveu, não precisa fazer o fallback
    }

    // 🔄 Fallback: tenta normalizar como se fosse um ID de categoria/subcategoria
    console.warn("❌ Nenhuma camada encontrada com o nome:", layerName);

    const normalizeText = (text) =>
        text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, "-").toLowerCase();

    const categoryId = `cat-${normalizeText(layerName)}`;
    const subcategoryId = `subcat-${normalizeText(layerName)}`;

    console.log(`📂 Tentando fallback com IDs normalizados: Categoria -> ${categoryId} | Subcategoria -> ${subcategoryId}`);

    let subcategoryButton = document.querySelector(`button[data-bs-target="#${subcategoryId}"]`);
    if (subcategoryButton) {
        console.log(`📂 Subcategoria encontrada via fallback: ${subcategoryId}`);

        let parentAccordion = subcategoryButton.closest(".accordion-body")?.closest(".accordion-collapse");
        if (parentAccordion) {
            let parentCategoryButton = document.querySelector(`button[data-bs-target="#${parentAccordion.id}"]`);
            if (parentCategoryButton) {
                console.log(`📂 A subcategoria pertence à categoria: ${parentAccordion.id}`);
                expandCategory(parentAccordion.id);
            }
        }

        let subcategoryCollapse = document.getElementById(subcategoryId);
        if (subcategoryCollapse && !subcategoryCollapse.classList.contains("show")) {
            console.log(`📂 Expandindo subcategoria (fallback): ${subcategoryId}`);
            subcategoryButton.click();
        }
        return;
    }

    // Última tentativa: só tenta abrir como categoria
    expandCategory(categoryId);
}



async function removeAllWmsLayers() {
    // Seleciona todos os checkboxes das camadas
    document.querySelectorAll(".layer-toggle").forEach(checkbox => {
        if (checkbox.checked) {
            // Converte o atributo data-layer de volta para objeto JSON
            const layerData = JSON.parse(checkbox.getAttribute("data-layer"));

            // Remove a camada chamando toggleLayer com checked = false
            toggleLayer(window.map, layerData, false);

            // Desmarca o checkbox
            checkbox.checked = false;
            updateLegends(layerData, false);
        }
    });

    console.log("✅ Todas as camadas WMS foram removidas.");
}

// Evento para o botão "Limpar Mapa"
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btn-clear-map").addEventListener("click", function () {
        removeAllWmsLayers();
    });
});





export function InitializeUI() {
    initializeSidebar();
    initializeTooltip();
    statistic();
    initializeLayerToggles();
    initializeSearch();
    toggleFullScreen();
    initializeExpandButton();
    //enableSwipeToDeleteAccordion("accordionMapasAtivos");
    initializeActionButtons();
    removeAllWmsLayers();
    enableCloseButtonAccordion("accordionMapasAtivos");
}
