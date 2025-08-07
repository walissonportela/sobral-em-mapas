<style>
    /* Estilos gerais para sidebar */
    .sidebar {
        position: absolute;
        top: 10px;
        left: 0px;
        bottom: 0;
        padding: 0;  
        width: 300px;
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
        color: var(--primary-color);
        user-select: none;
        background-color: var(--background-color);
        border-radius: 0 8px 0 0;
        box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
    }

    .sidebar-header i {
        font-size: 24px;
        color: var(--primary-color);
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
        @foreach($layers->groupBy(fn($layer) => $layer->getCategory() ?? 'Sem Categoria') as $categoryName => $subcategories)
          <div class="accordion-item cat">
            <h2 class="accordion-header cat">
              <button class="accordion-button cat" type="button" data-bs-toggle="collapse" data-bs-target="#cat-{{ Str::slug($categoryName) }}">
                {{ $categoryName }}
              </button>
            </h2>
            <div id="cat-{{ Str::slug($categoryName) }}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body cat">
                <div class="accordion" id="accordionSubcat-{{ Str::slug($categoryName) }}">
                  @foreach($subcategories->groupBy(fn($layer) => $layer->getSubcategory() ?? 'Sem Subcategoria') as $subcategoryName => $subcategoryLayers)
                    <div class="accordion-item sub">
                      <h2 class="accordion-header sub">
                        <button class="accordion-button cat collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subcat-{{ Str::slug($subcategoryName) }}">
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


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnCamadas = document.getElementById("btn-camadas");
        const btnMapasAtivos = document.getElementById("btn-mapas-ativos");
        const viewCamadas = document.getElementById("view-camadas");
        const viewMapasAtivos = document.getElementById("view-mapas-ativos");

        btnCamadas.addEventListener("click", function () {
            viewCamadas.style.display = "block";
            viewMapasAtivos.style.display = "none";
            btnCamadas.classList.add("active");
            btnMapasAtivos.classList.remove("active");
        });

        btnMapasAtivos.addEventListener("click", function () {
            viewCamadas.style.display = "none";
            viewMapasAtivos.style.display = "block";
            btnCamadas.classList.remove("active");
            btnMapasAtivos.classList.add("active");
        });
        
    });

</script>
