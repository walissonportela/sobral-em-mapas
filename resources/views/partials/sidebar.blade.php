<style>
    /* Estilos gerais para sidebar */
    .sidebar {
        position: absolute;
        top: 10px;
        left: 0px;
        bottom: 0;
        padding: 0;  
        width: 350px;
        display: flex;
        transition: all 0.3s ease-in-out;
        z-index: 1000;
    }

    .sidebar-content {
        width: 100%;
        padding: 5px;
        height: 100%;
        background: var(--primary-dark-color-glass);
    }
    
    .sidebar-camadas {
        overflow-y: scroll;
        background-color: var(--background-color);
        height: 100%;
        padding-left: 5px;
        padding-right: 5px;
        border-radius: 0 8px 8px 0;
    }

    .sidebar-camadas-admin {
        overflow-y: hidden;
        background-color: var(--background-color);
        height: 100%;
        padding-left: 5px;
        padding-right: 5px;
        border-radius: 0 8px 8px 0;
    }

    .sidebar-mapas-ativos {
        padding-left: 5px;
        padding-right: 5px;
        height: 100%;
        background-color: var(--background-color);
        border-radius: 0 8px 0 0;
    }

    /* Estiliza a barra de rolagem */
    .sidebar-camadas::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-camadas::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 8px;
    }

    .sidebar-camadas::-webkit-scrollbar-thumb {
        background-color: var(--text-gray-color);
        border-radius: 10px;
        border: none;
    }

    .sidebar-camadas::-webkit-scrollbar-thumb:hover {
        background-color: var(--text-gray-color);
    }

    .sidebar.sidebar-collapsed .sidebar-content {
        width: 0;
        padding: 0;
        display: none;
    }
    
    .sidebar-header {
        border-bottom: 1px solid var(--primary-dark-color);
        display: flex;
        align-items: start;
        gap: 8px;
        padding: 8px 10px;
        font-size: 20px;
        font-weight: 600;
        color: #005bb5;
        user-select: none;
        background-color: var(--background-color);
        border-radius: 0 8px 0 0;
        box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
        margin-bottom: 10px;
    }

    .sidebar-header i {
        font-size: 24px;
        color: #005bb5;
        flex-shrink: 0;
        filter: drop-shadow(0 1px 1px rgb(0 0 0 / 0.15));
    }

    .sidebar.sidebar-collapsed {
        width: 0px;
    }

    /* Estilos gerais para a actionbar */
    .action-bar {
        background-color: var(--primary-color-glass);
        display: flex;
        position: absolute;
        top: 0;
        right: -50px;
        flex-direction: column;
        align-items: start;
        border-radius: 0px 0px 10px 0;
        transition: all 0.3s ease-in-out;
    }

    .action-bar .btn {
        color: var(--text-white-color);
        font-size: 20px;
        width: 50px;
        height: 50px;
    }
    
    .active {
        background: var(--primary-dark-color);
    }
    
    .action-bar .btn:hover {
        background-color: var(--primary-dark-color);
        border-radius: none;
        cursor: pointer;
    }
    
    .search-container {
        position: relative;
    }

    .input-search {
        display: block;
        position: absolute;
        left: 50px;
        background: var(--background-color);
        width: 200px;
        border-radius: 0px 5px 5px 0px;
        height: 100%;
        top: 0;
        transition: width 0.3s ease-in-out;
        overflow: hidden;
        padding-left: 10px;
        padding-right: 30px; /* Ajusta espaço para o botão ❌ */
    }

    .input-search:focus {
        background-color: var(--background-color);
        color: var(--secondary-color);
    }

    .input-search.hidden {
        width: 0;
        padding: 0;
    }

    /* Botão "X" dentro do input */
    .clear-search {
        position: absolute;
        right: 10px;
        top: 30%;
        transform: translateX(2000%);
        cursor: pointer;
        font-size: 14px;
        color: var(--secondary-color);
        display: none; /* Oculto até que o usuário digite algo */
    }

    .input-search:not(:placeholder-shown) + .clear-search {
        display: block; /* Exibe o botão "X" quando há texto no input */
    }

    .clear-search:hover {
        color: var(--primary-color);
    }

    /* Botão "X" dentro do input */
    .clear-search {
        position: absolute;
        right: 10px;
        top: 30%;
        transform: translateX(2000%);
        cursor: pointer;
        font-size: 14px;
        color: var(--secondary-color);
        display: none; /* Oculto até que o usuário digite algo */
    }

    .input-search:not(:placeholder-shown) + .clear-search {
        display: block; /* Exibe o botão "X" quando há texto no input */
    }

    .clear-search:hover {
        color: var(--primary-color);
    }
    

/* ========================================================================================================== */
/* ESTILOS GERAIS */
/* ========================================================================================================== */

/* Contêiner geral do Accordion */
.accordion {
    display: flex;
    flex-direction: column;
    gap: 25px; /* Espaço entre os blocos */
    font-family: 'Segoe UI', 'Roboto', 'Helvetica', sans-serif;
    padding: 20px;
    background-color: #0086de; /* Fundo azul escuro */
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}

/* Botões do Accordion (padrão) */
.accordion-button {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: #005bb5;
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.accordion-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}

/* Ícone de seta */
.accordion-button::after {
    content: "";
    width: 22px;
    height: 22px;
    background-size: contain;
    background-repeat: no-repeat;
    transition: transform 0.3s ease;
    filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));
}

.accordion-button.collapsed::after {
    transform: rotate(0deg);
    background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' fill='%23333333' viewBox='0 0 16 16'><path d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
}

.accordion-button:not(.collapsed)::after {
    transform: rotate(180deg);
    background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' fill='%23333333' viewBox='0 0 16 16'><path d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
}

/* ========================================================================================================== */
/* CATEGORIAS (Nível 1) */
/* ========================================================================================================== */

.accordion-item.cat {
    margin-bottom: 0;
    border-radius: 12px;
    overflow: hidden;
    background-color: #FFFFFF; /* Fundo branco para a categoria */
    border: 1px solid #dcdcdc; /* Borda sutil */
    margin-bottom: 2px;
}

.accordion-button.cat {
    background-color: #FFFFFF;
    color: #005bb5; /* Cor do texto escura para contraste */
}

.accordion-button.cat:not(.collapsed) {
    background-color: #E6F0FF; /* Azul claro quando aberto */
    border-radius: 10px 10px 0 0;
}

.accordion-button.cat:hover {
    background-color: #E6F0FF;
}

.accordion-body.cat {
    background-color: #F8F9FA;
    padding: 15px 20px;
    font-size: 1rem;
    border-left: 5px solid #005bb5;
    border-radius: 0 0 10px 10px;
    color: #005bb5;
}

/* ========================================================================================================== */
/* SUBCATEGORIAS (Nível 2) */
/* ========================================================================================================== */

.accordion-item.sub {
    margin-bottom: 10px;
    border-radius: 8px;
    overflow: hidden;
    background-color: #FFFFFF;
    border: 1px solid #dcdcdc;
}

.accordion-button.sub {
    background-color: #FFFFFF;
    color: #005bb5;
    padding: 0.8rem 1.2rem;
    font-size: 0.9rem;
    border-radius: 8px;
    box-shadow: none;
}

.accordion-button.sub:hover {
    background-color: #E6F0FF;
    transform: none;
    box-shadow: none;
}

.accordion-body.sub {
    background-color: #f8f9fa;
    padding: 10px 18px;
    font-size: 0.8rem;
    border-left: 3px dotted #005bb5;
    border-radius: 0 0 8px 8px;
    color: #005bb5;
}

/* ========================================================================================================== */
/* MAPAS ATIVOS */
/* ========================================================================================================== */

.accordion-item.ma {
    margin-bottom: 10px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    background-color: #FFFFFF;
    border: 1px solid #dcdcdc;
}

.accordion-button.ma {
    background-color: #FFFFFF;
    color: #333333;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.7rem 1.2rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.accordion-button.ma img {
    max-height: 26px;
    width: auto;
    border-radius: 5px;
}

.accordion-button.ma:hover {
    background-color: #E6F0FF;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.12);
}

.accordion-body.ma {
    background-color: #f8f9fa;
    padding: 8px 14px;
    font-size: 0.85rem;
    border-left: 3px dotted #005bb5;
    border-right: 3px dotted #005bb5;
    border-bottom: 3px dotted #005bb5;
    border-radius: 0 0 10px 10px;
}

/* ========================================================================================================== */
/* LISTAS INTERNAS (CAMADAS) */
/* ========================================================================================================== */

.accordion-body ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.accordion-body ul li {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 6px 0;
    transition: background 0.2s ease;
    border-radius: 6px;
}

.accordion-body ul li:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.accordion-body ul li label {
    flex-grow: 1;
    color: #005bb5;
}

input[type="checkbox"] {
    accent-color: #005bb5;
    width: 18px;
    height: 18px;
}


/* Estilos gerais para as layers em Mapas Ativos */
.box-legenda {
    flex-grow: 1;
    overflow-y: auto;
}
.box-leg-item:hover {
    border: 1px solid var(--secondary-color-orange);
}
.box-leg-item {
    display: flex;
    align-items: center;
    border: 1px solid var(--secondary-color-gray);
    height: 40px;
    margin-bottom: 3px;
}
.box-leg-item img {
    height: 40px;
    width: 40px;
}
.box-leg-body {
    flex-grow: 1;
    padding: 2px 2px 2px 4px;
    height: 100%;
    display: flex;
    align-items: center;
}
.box-leg-body p {
    font-size: 10px;
    margin: 0;
}

/* Estilos gerais para os accordion de Mapas Ativos e Legendas */
.accordion {
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.accordion-item.ma.layer-deleting .accordion-button {
    z-index: 3;
    border-color: #fe8686;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(253, 13, 13, 0.25);
}
.accordion-item.ma.layer-deleting .accordion-button::after {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' stroke='%23ff0000' stroke-width='2' fill='none'%3E%3Cline x1='4' y1='4' x2='12' y2='12'/%3E%3Cline x1='4' y1='12' x2='12' y2='4'/%3E%3C/svg%3E");
}
.accordion-item.ma {
    position: relative;
    transition: transform 0.3s ease;
}
.accordion-button.ma:not(.collapsed) img {
    display: none;
}

.accordion-item.ma {
    background-color: transparent;
    /* box-shadow: rgb(69 39 12 / 25%) 0px 6px 12px -2px,
        rgb(229 165 165 / 30%) 0px 3px 7px -3px; */
    margin-bottom: 8px;
}
.accordion-button.ma {
    height: 30px;
    color: var(--text-white-color);
    background: var(--primary-dark-color);
}
.accordion-button.ma span {
    margin-left: 8px;
}
.accordion-body.ma {
    background-color: var(--background-secondary-color);
    padding: 5px;
    border-left: 0.5px dotted var(--primary-dark-color);
    border-right: 0.5px dotted var(--primary-dark-color);
    border-bottom: 0.5px dotted var(--primary-dark-color);
}
.accordion-body.ma h3 {
    font-size: medium;
}
.ma-img-box {
    height: 80px;
    /* border: 1px dotted #4b4b4b; */
}
.ma-img-box img {
    display: block;
    max-height: 100%;
    height: 100%;
    width: auto;
}
.ma-leg-box {
    margin: 5px 0 5px 0;
}
.ma-leg-box p {
    font-size: small;
    margin: 0;
}

/* Estilos gerais para a mensagem de erro  */
#error-message {
    display: none;
    position: absolute;
    top: -10px; /* Ajuste a distância do topo conforme necessário */
    left: 50%;
    transform: translateX(-50%);
    padding: 10px 20px;
    background-color: rgb(177, 57, 57);
    color: var(--text-white-color);
    font-weight: 300;
    border-radius: 5px;
    z-index: 9999; /* Para garantir que fique acima de outros elementos */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: all 0.5s ease-in-out;
}

/* Estilos gerais para o componente da caixa de seleção */
#selection-box {
    position: absolute;
    pointer-events: none; /* Não captura cliques internamente */
    left: calc(50% - 150px);
    top: calc(50% - 150px);
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    gap: 5px;
}

#selection-area {
    min-width: 347px;
    min-height: 300px;
    border: 2px dotted var(--primary-dark-color);
    resize: both;
    overflow: hidden;
    position: relative;
}

#selection-button:hover {
    background-color: var(--primary-dark-color);
}

#drag-handle {
    /* Permite arrastar pelo cabeçalho */
    display: flex;
    justify-content: space-between;
    gap: 2px;
    align-items: center;
    color: var(--text-white-color);
    background-color: var(--primary-color);
    padding: 5px;
    cursor: move;
    pointer-events: auto; /* O cabeçalho captura eventos de clique */
}
.selection-tools {
    width: 150px;
    pointer-events: auto;
}
.print-options {
    background: var(--background-color);
    padding: 8px;
    border-radius: 0px 5px 5px 0;
    color: var(--text-dark-color);
    font-size: 12px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.print-options select,
.print-options input,
.print-options span {
    height: 28px;
    font-size: 12px;
}
.print-options button {
    width: 80px;
    height: 30px;
    padding: 0;
    background: var(--primary-color);
    border: 1px solid var(--secondary-color-white);
}
.print-options button:hover {
    background-color: var(--primary-dark-color);
}

#selection-area::after {
    /* Estilo para permitir interações nas bordas de redimensionamento */
    content: "";
    position: absolute;
    bottom: 0;
    right: 0;
    border: 1px solid var(--secondary-color-dark);
    width: 15px;
    height: 15px;
    background: var(--background-color); /* Transparente, mas captura eventos */
    cursor: se-resize; /* Mostra o ícone de redimensionamento */
    pointer-events: auto; /* Permite interações nas bordas */
    z-index: 1001;
}
#drag-handle button {
    color: var(--text-white-color);
}
#drag-handle button:hover {
    background: var(--primary-dark-color);
}

.swipe-delete {
    background-color: #f8d7da;
}



</style>

<div class="sidebar" id="mainSidebar">
  <!-- Parte interna da sidebar com camadas/categorias -->
  <div class="sidebar-content">

    <div id="view-camadas" class="sidebar-camadas">
      <div class="sidebar-header d-flex">
        <i class="fas fa-layer-group" aria-hidden="true"></i>
        <span>Camadas</span>
      </div>

            <div class="accordion" id="accordionExample">
            <div class="categories-container">
                @foreach($layers->groupBy(fn($layer) => $layer->getCategory() ?? 'Sem Categoria') as $categoryName => $subcategories)
                <div class="accordion-item cat">
                    <h2 class="accordion-header cat">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat-{{ Str::slug($categoryName) }}">
                        {{ $categoryName }}
                    </button>
                    </h2>
                    <div id="cat-{{ Str::slug($categoryName) }}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body cat">
                        <div class="accordion" id="accordionSubcat-{{ Str::slug($categoryName) }}">
                        @foreach($subcategories->groupBy(fn($layer) => $layer->getSubcategory() ?? 'Sem Subcategoria') as $subcategoryName => $subcategoryLayers)
                            <div class="accordion-item sub">
                            <h2 class="accordion-header sub">
                                <button class="accordion-button sub collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subcat-{{ Str::slug($subcategoryName) }}">
                                {{ $subcategoryName }}
                                </button>
                            </h2>
                            <div id="subcat-{{ Str::slug($subcategoryName) }}" class="accordion-collapse collapse" data-bs-parent="#accordionSubcat-{{ Str::slug($categoryName) }}">
                                <div class="accordion-body sub">
                                <ul class="list-unstyled sub-list">
                                    @foreach($subcategoryLayers as $layer)
                                    @php
                                        // Criar um JSON com todos os atributos da camada
                                        $layerData = json_encode([
                                        'layer_name' => $layer->getLayerName(),
                                        'name' => $layer->getName(),
                                        'crs' => $layer->getCrs(),
                                        'max_scale' => $layer->getMaxScale(),
                                        'order' => $layer->getOrder(),
                                        'wms_link_id' => $layer->getWmsLinkId(),
                                        'legend_url' => $layer->getLegendUrl(),
                                        'symbol' => $layer->getSymbol(),
                                        'description' => $layer->getDescription()
                                        ]);
                                    @endphp

                                    <li>
                                        <input type="checkbox" id="{{ $layer->getLayerName() }}" 
                                        class="layer-toggle"
                                        data-layer='@json($layerData)'>
                                        <label for="{{ $layer->getLayerName() }}">
                                        {{ $layer->getName() }}
                                        </label>
                                        <i class="fas fa-exclamation-circle hide-layer-alert"></i>
                                    </li>
                                    @endforeach
                                </ul>
                                </div>
                            </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <div id="view-mapas-ativos" style="display: none" class="sidebar-mapas-ativos">
      <div class="sidebar-header mb-1 d-flex">
        <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
        <span>Mapas Ativos</span>
      </div>
      <!-- Mapas Ativos como um conjunto de accordion  -->
      <div class="accordion" id="accordionMapasAtivos"> 
        @foreach($layers as $layer)
          @if($layer->isPublic()) 
            <div class="accordion-item ma" id="active-layer-{{$layer->getLayerName()}}" style="display: none;">
              <div class="accordion-header ma">
                <button class="accordion-button ma collapsed" type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#ma-{{ Str::slug($layer->getLayerName()) }}" 
                        aria-expanded="false">
                  @if(!empty($layer->getLegendUrl()))
                    <img height="30px" src="{{ $layer->getLegendUrl() }}" alt="Legenda de {{ $layer->getName() }}">
                  @endif
                  <span>{{ $layer->getName() }}</span>
                </button>
              </div>
              <div id="ma-{{ Str::slug($layer->getLayerName()) }}" 
                   class="accordion-collapse ma collapse" 
                   data-bs-parent="#accordionMapasAtivos">
                <div class="accordion-body ma">
                  <h3>Legenda</h3>
                  @if(!empty($layer->getLegendUrl()))
                    <div class="ma-img-box">
                      <img src="{{ $layer->getLegendUrl() }}" alt="Legenda de {{ $layer->getName() }}">
                    </div>
                  @endif
                  @if(!empty($layer->getDescription()))
                    <div class="ma-leg-box">
                      <p> {!! $layer->getDescription() !!}</p>
                    </div>
                  @else
                    <p>ℹ️ Nenhuma descrição fornecida para esta camada.</p>
                  @endif
                </div>
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>

  </div>

  <!-- Coluna de botões que permanece visível quando retraída -->
  <div class="action-bar">
    {{-- toggle hamburguer --}}
    <button class="btn" id="toggleSidebar" data-bs-toggle="tooltip" title="Menu"><i class="fas fa-bars"></i></button>  

    {{-- pesquisar --}}
    <div class="search-container">
      <input type="text" class="input-search hidden form-control" placeholder="Pesquisar..." aria-label="Pesquisar" aria-describedby="searchButton">
      <span class="clear-search">&times;</span>
      <button id="btn-search" class="btn" data-bs-toggle="tooltip" title="Pesquisar">
        <i class="fas fa-search"></i>
      </button>
    </div>

    <button class="btn active" id="btn-camadas" data-bs-toggle="tooltip" title="Camadas"><i class="fas fa-layer-group"></i></button>
    <button class="btn" id="btn-mapas-ativos" data-bs-toggle="tooltip" title="Legenda e Mapas Ativos"><i class="fas fa-info"></i></button>
    <button class="btn" id="btn-clear-map" data-bs-toggle="tooltip" title="Limpar Mapa"><i class="fas fa-eraser"></i></button>
    <button class="btn" id="btn-imprimir" data-bs-toggle="tooltip" title="Imprimir"><i class="fas fa-print"></i></button>
    <button class="btn" id="btn-measure" data-bs-toggle="tooltip" title="Medir"><i class="fas fa-ruler"></i></button>
    <button class="btn" id="btn-expand" data-bs-toggle="tooltip" title="Expandir Tela"><i class="fas fa-expand-arrows-alt"></i></button>
  </div>

</div>
