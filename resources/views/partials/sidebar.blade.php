<div class="sidebar" id="mainSidebar">
    <!-- Parte interna da sidebar com camadas/categorias -->
    <div class="sidebar-content">

        <div id="view-camadas" class="sidebar-camadas">
            <div class="sidebar-header d-flex justify-content-between">
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
            <div class="sidebar-header mb-1 d-flex justify-content-between">
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
