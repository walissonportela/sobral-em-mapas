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
        let threshold = 80; // Limiar para remover o item
        let isSwiping = false;
        let isMouseDown = false; // Flag para verificar se o mouse está pressionado
        let isMoving = false; // Flag para verificar se está havendo movimento
        let allowSwipe = false; // Flag para permitir o arraste
        let holdTimeout = null; // Timeout para contar os 5 segundos

        // Função para iniciar o arrasto
        function startSwipe(x) {
            startX = x;
            isSwiping = true;
            isMoving = false; // Resetar flag de movimento
        }

        // Função para processar o movimento
        function moveSwipe(x) {
            if (!isSwiping || !allowSwipe) return; // Só permitir o movimento se o arraste for autorizado
            currentX = x;
            let deltaX = currentX - startX;

            if (Math.abs(deltaX) > 10) {
                // Se houver movimento significativo, ativar a flag de movimento
                isMoving = true;
            }
            if (Math.abs(deltaX) > threshold && allowSwipe) {
                item.classList.add("layer-deleting");
            }

            if (deltaX < 0) {
                // Apenas seguir o arraste para a esquerda
                item.style.transform = `translateX(${deltaX}px)`;
            }
        }

        // Função para finalizar o arrasto
        function endSwipe() {
            if (isMoving) {
                let deltaX = currentX - startX;

                if (Math.abs(deltaX) > threshold && allowSwipe) {
                    // Se o arraste for maior que o limiar, remova o item
                    item.style.transition = "transform 0.3s ease";
                    item.style.transform = `translateX(-100%)`;

                    setTimeout(() => {
                        item.remove(); // Remove o item após a animação
                    }, 300);
                } else {
                    // Volta ao estado original se o arraste for muito pequeno ou se não foi autorizado
                    item.style.transition = "transform 0.3s ease";
                    item.style.transform = "translateX(0)";
                    item.classList.remove("layer-deleting");
                }
            }

            isMoving = false;
            isSwiping = false;
            isMouseDown = false;
            allowSwipe = false; // Reseta o estado para a próxima vez
        }

        // Inicia o temporizador de 5 segundos ao pressionar o botão
        function startHold() {
            holdTimeout = setTimeout(() => {
                allowSwipe = true; // Permitir o arraste após 5 segundos
                //item.classList.add("layer-deleting");
            }, 500); // Aguardar 5 segundos
        }

        // Cancela o temporizador se o mouse ou o dedo for solto antes dos 5 segundos
        function cancelHold() {
            clearTimeout(holdTimeout); // Cancela o timeout se o mouse for solto antes de 5 segundos
        }

        // Eventos para mobile
        item.addEventListener("touchstart", (e) => {
            startHold(); // Inicia o temporizador de 5 segundos
            startSwipe(e.touches[0].clientX);
        });

        item.addEventListener("touchmove", (e) => {
            moveSwipe(e.touches[0].clientX);
        });

        item.addEventListener("touchend", (e) => {
            cancelHold(); // Cancela o temporizador se o arrasto for interrompido
            if (isMoving) {
                endSwipe();
            }
        });

        // Eventos para desktop (mouse)
        item.addEventListener("mousedown", (e) => {
            isMouseDown = true;
            startHold(); // Inicia o temporizador de 5 segundos
            startSwipe(e.clientX);
        });

        item.addEventListener("mousemove", (e) => {
            if (!isMouseDown) return; // Apenas mover se o mouse estiver pressionado
            moveSwipe(e.clientX);
        });

        item.addEventListener("mouseup", (e) => {
            cancelHold(); // Cancela o temporizador se o arrasto for interrompido
            if (isMoving) {
                endSwipe();
            } else {
                isMouseDown = false;
                isSwiping = false;
                allowSwipe = false;
            }
        });

        item.addEventListener("mouseleave", () => {
            cancelHold(); // Cancela o temporizador se o mouse sair do item
            if (isMouseDown && isMoving) {
                endSwipe();
            }
        });
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
    let mapasSelecionados = {};

    // Gera um identificador único para a sessão (se não existir na localStorage)
    let sessionId = sessionStorage.getItem("sessionId");
    if (!sessionId) {
        sessionId = Math.floor(100000 + Math.random() * 900000).toString(); // Gera um número de 6 dígitos
        sessionStorage.setItem("sessionId", sessionId);
    }
    console.log(`🆔 ID da sessão: ${sessionId}`);

    function atualizarMapas(layerData, isChecked) {
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
    
        // Garante que o valor não seja indefinido
        if (!mapasSelecionados[layerName]) {
            mapasSelecionados[layerName] = 0;
        }
    
        if (!isChecked) {
            console.log(`🛠 Camada "${layerName}" desmarcada. Contador mantido: ${mapasSelecionados[layerName]}`);
        }
    
        // 🚀 Atualiza SOMENTE as camadas que estão ativas!
        document.querySelectorAll(".layer-toggle:checked").forEach((checkbox) => {
            try {
                let activeLayerData = checkbox.getAttribute("data-layer");
    
                if (!activeLayerData) {
                    console.warn("⚠️ data-layer ausente no checkbox:", checkbox);
                    return;
                }
    
                console.log("🔍 Conteúdo bruto do data-layer:", activeLayerData);
    
                // Converte para objeto JSON
                if (typeof activeLayerData === "string") {
                    try {
                        activeLayerData = JSON.parse(activeLayerData);
                        activeLayerData = JSON.parse(activeLayerData);
                    } catch (parseError) {
                        console.error("❌ ERRO ao converter data-layer para JSON:", parseError, "Conteúdo:", activeLayerData);
                        return;
                    }
                }
    
                console.log("📦 Objeto convertido:", activeLayerData);
    
                if (!activeLayerData || typeof activeLayerData !== "object" || !activeLayerData.layer_name) {
                    console.warn("⚠️ Estrutura inválida no objeto após parse:", activeLayerData);
                    return;
                }
    
                let activeLayerName = activeLayerData.layer_name;
                console.log(`📌 Nome da camada ativa detectado: ${activeLayerName}`);
    
                // ✅ Agora só aumenta a contagem de camadas ATIVAS, sem duplicar
                mapasSelecionados[activeLayerName] = (mapasSelecionados[activeLayerName] || 0) + 1;
    
            } catch (error) {
                console.error("❌ ERRO ao processar camada ativa:", error);
            }
        });
    
        console.log("📊 Mapas Selecionados Atualizados:", JSON.stringify(mapasSelecionados, null, 2));
    }
    
    
    
    

    window.updateStatistics = atualizarMapas;

    // Captura mudanças nos checkboxes das camadas
    document.addEventListener("change", function (event) {
        if (event.target.classList.contains("layer-toggle")) {
            let layerData = JSON.parse(event.target.getAttribute("data-layer"));
            atualizarMapas(layerData, event.target.checked);
            console.log(`🛠 Camada "${layerData.layer_name}" foi ${event.target.checked ? "selecionada" : "desmarcada"}`);
        }
    });

    // Função para enviar estatísticas periodicamente
    function enviarEstatisticas() {
        let tempoAtual = Date.now();
        let tempoTotal = Math.round((tempoAtual - tempoInicio) / 1000); // Tempo em segundos

        let estatisticas = {
            session_id: sessionId, // Enviamos o identificador único da sessão
            mapas_selecionados: mapasSelecionados, // Apenas os nomes das camadas e contagem
            tempo_total: tempoTotal,
        };

        console.log("📤 Enviando estatísticas a cada 30s:", estatisticas);

        fetch(`${window.location.origin}/sobralmapas/public/api/estatisticas`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(estatisticas),
        })
        .then((response) => response.json())
        .then((data) => console.log("📊 Estatísticas enviadas com sucesso:", data))
        .catch((error) => console.error("❌ Erro ao enviar estatísticas:", error));
    }



    // Envia estatísticas finais ao sair da página
    window.addEventListener("beforeunload", enviarEstatisticas);
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
    enableSwipeToDeleteAccordion("accordionMapasAtivos");
    initializeActionButtons();
    removeAllWmsLayers();
    
}
