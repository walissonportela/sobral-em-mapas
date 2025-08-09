@extends('adminlte::page')

@section('title', 'Camadas')

@section('adminlte::meta_tags')
    <!-- Adiciona o CSRF Token no head -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <h1><i class="fas fa-layer-group"></i> Camadas</h1>
@endsection

@section('css')
<style>

    h1 i {
        margin-right: 8px;
        color: #3c8dbc; /* uma cor legal pro ícone */
    }

    .btn-close {
        position: absolute;
        top: 0.5rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.25rem;
        color: #000;
        cursor: pointer;
    }

    .btn-close:hover {
        color: red;
    }

    #server-response {
        margin-top: 20px;
        position: relative;
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Mensagens de Sucesso ou Erro -->
    @if(session('success'))
        <div id="server-response" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)">X</button>
        </div>
    @endif

    @if(session('error'))
        <div id="server-response" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)">X</button>
        </div>
    @endif

    <!-- Mensagens de Erro de Validação -->
    @if ($errors->any())
        <div id="server-response" class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)">X</button>
        </div>
    @endif

    <div class="row">
        <!-- Card de criação de camada -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bd-primary" style="background-color: #007bff; color: white; display: flex; align-items: center;">
                    <i class="fas fa-plus" style="margin-right: 10px;"></i>
                    <h3 class="card-title mb-0">Criar Nova Camada</h3>
                </div>

                <div class="card-body">
                    <!-- Formulário para criar camada -->
                    <form id="layer-form" action="{{ route('admin.layers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="name">Nome da Camada</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="subcategory">Subcategoria</label>
                            <select class="form-control" id="subcategory" name="subcategory" required>
                                <option value="">Selecione uma Subcategoria</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->getId() }}">{{ $subcategory->getName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="wms_link_id">Selecione o Link WMS</label>
                            <select class="form-control" id="wms_link_id" name="wms_link_id" required>
                                <option value="">Selecione um Link WMS</option>
                                @foreach($wmsLinks as $link)
                                    <option value="{{ $link->id }}">{{ $link->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="layers-select">Camada</label>
                            <select id="layers-select" class="form-control">
                                <option value="">Selecione um geoserver primeiro</option>
                            </select>
                        </div>
                        <input type="hidden" id="layer_name" name="layer_name">

                        <div class="form-group">
                            <label for="crs">CRS:</label>
                            <input type="text" class="form-control" id="crs" name="crs" placeholder="CRS da Camada" readonly>
                        </div>

                        <div class="form-group">
                            <label for="formats">Formats:</label>
                            <input type="text" class="form-control" id="formats" name="formats" placeholder="Formats disponíveis" readonly>
                        </div>

                        <div class="form-group">
                            <label for="legend_url">Legend URL:</label>
                            <input type="text" class="form-control" id="legend_url" name="legend_url" placeholder="URL da Legenda" readonly>
                        </div>

                        <div class="form-group">
                            <label for="use_legend">Usar Legend URL?</label>
                            <select class="form-control" id="use_legend" name="use_legend">
                                <option value="yes">Sim</option>
                                <option value="no">Não</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Imagem</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>

                        <div class="form-group">
                            <label for="max_scale">Escala Máxima</label>
                            <input type="number" class="form-control" id="max_scale" name="max_scale">
                        </div>

                        <div class="form-group">
                            <label for="symbol">Símbolo</label>
                            <input type="text" class="form-control" id="symbol" name="symbol">
                        </div>

                        <div class="form-group">
                            <label for="order">Ordem</label>
                            <input type="number" class="form-control" id="order" name="order" required>
                        </div>
                        <div class="form-group">
                            <label for="isPublic">É público?</label>
                            <select class="form-control" id="isPublic" name="isPublic" required>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>


                        <button type="submit" class="btn btn-primary mt-3">Criar Camada</button>
                    </form>
                </div>
            </div>
        </div>


        <!-- Card de lista de camadas -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary" style="color: white; display: flex; align-items: center;">
                    <i class="fas fa-layer-group" style="margin-right: 10px;"></i>
                    <h3 class="card-title">Lista de Camadas</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($layers as $layer)
                        <li class="list-group-item d-flex justify-content-between align-items-center" id="layer-{{ $layer->getId() }}">
                            <span class="layer-name" data-id="{{ $layer->getId() }}" data-name="{{ $layer->getName() }}">
                                {{ $layer->getName() }}({{$layer->getSubcategory()}} )
                            </span>
                            <div>
                            <button 
                                class="btn btn-warning btn-sm mx-1" 
                                onclick="editLayer(this)"
                                data-id="{{ $layer->getId() }}"
                                data-name="{{ $layer->getName() }}"
                                data-layer-name="{{ $layer->getLayerName() }}"
                                data-subcategory="{{ $layer->getSubcategory() }}"
                                data-wms-link="{{ $layer->getWmsLinkId() }}"
                                data-crs="{{ $layer->getCrs() }}"
                                data-legend-url="{{ $layer->getLegendUrl() }}"
                                data-description="{{ $layer->getDescription() }}"
                                data-max-scale="{{ $layer->getMaxScale() }}"
                                data-symbol="{{ $layer->getSymbol() }}"
                                data-order="{{ $layer->getOrder() }}"
                                data-is-public="{{ $layer->isPublic()}}">
                                Editar
                            </button>
                                <form action="{{ route('admin.layers.destroy', $layer->getId()) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

<!-- Modal para edição -->
<div class="modal fade" id="editLayerModal" tabindex="-1" role="dialog" aria-labelledby="editLayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLayerModalLabel">Editar Camada</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="edit-layer-form" method="POST" action="{{ route('admin.layers.update', ['id' => ':id']) }}">
                    @csrf
                    @method('PUT')

                    <!-- Campos do formulário de criação -->
                    <div class="form-group">
                        <label for="edit-name">Nome da Camada</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-subcategory">Subcategoria</label>
                        <select class="form-control" id="edit-subcategory" name="subcategory" required>
                            <option value="">Selecione uma Subcategoria</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->getId() }}">{{ $subcategory->getName() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit-wms-link">Selecione o Link WMS</label>
                        <select class="form-control" id="edit-wms-link" name="wms_link_id" required>
                            <option value="">Selecione um Link WMS</option>
                            @foreach($wmsLinks as $link)
                                <option value="{{ $link->id }}">{{ $link->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit-layer-name">Camada</label>
                        <input type="text" class="form-control" id="edit-layer-name" name="layer_name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-crs">CRS:</label>
                        <input type="text" class="form-control" id="edit-crs" name="crs" readonly>
                    </div>

                    <div class="form-group">
                        <label for="edit-formats">Formats:</label>
                        <input type="text" class="form-control" id="edit-formats" name="formats" readonly>
                    </div>

                    <div class="form-group">
                        <label for="edit-legend-url">Legend URL:</label>
                        <input type="text" class="form-control" id="edit-legend-url" name="legend_url" readonly>
                    </div>

                    <div class="form-group">
                        <label for="edit-description">Descrição</label>
                        <textarea class="form-control" id="edit-description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit-image">Imagem</label>
                        <input type="file" class="form-control" id="edit-image" name="image">
                    </div>

                    <div class="form-group">
                        <label for="edit-max-scale">Escala Máxima</label>
                        <input type="number" class="form-control" id="edit-max-scale" name="max_scale">
                    </div>

                    <div class="form-group">
                        <label for="edit-symbol">Símbolo</label>
                        <input type="text" class="form-control" id="edit-symbol" name="symbol">
                    </div>

                    <div class="form-group">
                        <label for="edit-order">Ordem</label>
                        <input type="number" class="form-control" id="edit-order" name="order" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-is-public">É público?</label>
                        <select class="form-control" id="edit-is-public" name="isPublic" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>


                    <input type="hidden" id="edit-layer-id" name="layer_id">

                    <button type="submit" class="btn btn-primary mt-3">Salvar Alterações</button>
                </form>
            </div>
           
        </div>
    </div>
</div>

<script>
    document.getElementById('wms_link_id').addEventListener('change', function () {
        const wmsLinkId = this.value;
        const layersSelect = document.getElementById('layers-select');

        layersSelect.innerHTML = '<option value="">Carregando...</option>';

        if (wmsLinkId) {
            const serverURL = window.location.origin;
            const endpoint = `${serverURL}/sobralmapas/public/admin/wms/${wmsLinkId}/layers`;
            fetch(endpoint)
                .then(response => response.json())
                .then(data => {
                    layersSelect.innerHTML = '<option value="">Selecione uma camada</option>';
                    if (data.error) {
                        showServerResponse('Erro ao carregar camadas: ' + data.error, 'danger');
                        return;
                    }

                    data.forEach(layer => {
                        const option = document.createElement('option');
                        option.value = layer.id;
                        option.textContent = layer.layer_name;
                        option.dataset.crs = layer.crs || '';
                        option.dataset.formats = layer.formats || '';
                        option.dataset.description = layer.description || '';
                        layersSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    layersSelect.innerHTML = '<option value="">Erro ao carregar camadas</option>';
                    showServerResponse('Erro: ' + error.message, 'danger');
                });
        } else {
            layersSelect.innerHTML = '<option value="">Selecione um WMS Link primeiro</option>';
        }
    });

    document.getElementById('layers-select').addEventListener('change', function () {
        const selectedLayer = this.options[this.selectedIndex];
        const layerNameField = document.getElementById('layer_name');
        if (selectedLayer) {
            layerNameField.value = selectedLayer.textContent;
            document.getElementById('crs').value = selectedLayer.dataset.crs || '';
            document.getElementById('formats').value = selectedLayer.dataset.formats || '';
            document.getElementById('legend_url').value = selectedLayer.dataset.description || '';
        } else {
            layerNameField.value = '';
            document.getElementById('crs').value = '';
            document.getElementById('formats').value = '';
            document.getElementById('legend_url').value = '';
        }
    });

    function showServerResponse(message, type) {
        const responseDiv = document.getElementById('server-response');
        responseDiv.className = `alert alert-${type} alert-dismissible fade show`;
        responseDiv.textContent = message;
        responseDiv.classList.remove('d-none');

        // Botão de fechar
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn-close';
        closeButton.setAttribute('data-bs-dismiss', 'alert');
        closeButton.setAttribute('aria-label', 'Close');
        responseDiv.appendChild(closeButton);
    }
    function closeAlert(button) {
        const alertDiv = button.parentElement;
        alertDiv.remove();
    }
    function closeModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editLayerModal'));
        modal.hide();
    }

    function editLayer(button) {
    const id = button.dataset.id;

    // Atualizar o action do formulário com o ID
    const form = document.getElementById('edit-layer-form');
    form.action = form.action.replace(':id', id);

    // Preencher os campos do formulário com os dados do botão
    document.getElementById('edit-name').value = button.dataset.name;
    document.getElementById('edit-layer-name').value = button.dataset.layerName;
    document.getElementById('edit-crs').value = button.dataset.crs;
    document.getElementById('edit-formats').value = button.dataset.formats;
    document.getElementById('edit-legend-url').value = button.dataset.legendUrl;
    document.getElementById('edit-description').value = button.dataset.description;
    document.getElementById('edit-max-scale').value = button.dataset.maxScale;
    document.getElementById('edit-symbol').value = button.dataset.symbol;
    document.getElementById('edit-order').value = button.dataset.order;
    document.getElementById('edit-is-public').value = button.dataset.isPublic;


    // Abrir o modal
    const modal = new bootstrap.Modal(document.getElementById('editLayerModal'));
    modal.show();
}

</script>
@endsection
