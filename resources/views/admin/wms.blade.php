@extends('adminlte::page')

@section('title', 'Camadas WMS')

@section('adminlte::meta_tags')
    <!-- Adiciona o CSRF Token no head -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="mb-0">
            <i class="fas fa-fw fa-globe"></i> 
            Camadas WMS
        </h1>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <!-- Card de criação de camada WMS -->
        <div class="col-md-6">
            <div class="card">
               <div class="card-header d-flex align-items-center bg-primary">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i> Criar Nova Camada WMS
                    </h3>
                </div>

                <div class="card-body">
                    <!-- Formulário para criar camada WMS -->
                    <form id="wms-form" action="{{ route('admin.wms.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nome da Camada WMS</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="url">URL do WMS</label>
                            <input type="text" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-version">Versão</label>
                            <select class="form-control" id="edit-version" name="version" required>
                                <option value="1.0.0">1.0.0</option>
                                <option value="1.1.0">1.1.0</option>
                                <option value="1.3.0">1.3.0</option>
                            </select>
                        </div>


                        <button type="submit" class="btn btn-primary mt-3">Criar Camada</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card de lista de camadas WMS -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center bg-secondary">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-layer-group me-2"></i> 
                        Lista de Camadas WMS
                    </h3>
                </div>

                <div class="card-body">
                    <ul class="list-group">
                    @foreach($wmsLinks as $wmsLink)
                        <li class="list-group-item d-flex justify-content-between align-items-center" id="wms-link-{{ $wmsLink->id }}">
                            <span class="wms-link-name" data-id="{{ $wmsLink->id }}" data-name="{{ $wmsLink->name }}" data-url="{{ $wmsLink->url }}">
                                {{ $wmsLink->name }} ({{ $wmsLink->url }})
                            </span>
                            <div>
                                <button class="btn btn-warning btn-sm mx-1" onclick="editWmsLink({{ $wmsLink->id }})">Editar</button>
                                <!-- Formulário de exclusão -->
                                <form action="{{ route('admin.wms.destroy', $wmsLink->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mx-1">Deletar</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para edição -->
<div class="modal fade" id="editWmsLinkModal" tabindex="-1" role="dialog" aria-labelledby="editWmsLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWmsLinkModalLabel">Editar Camada WMS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Alerta para exibir mensagens de erro -->
                <div id="error-alert" class="alert alert-danger d-none"></div>
                <div id="success-alert" class="alert alert-success d-none"></div>

                <!-- Formulário de edição de camada WMS -->
                <form id="edit-wms-link-form" method="POST" action="{{ route('admin.wms.update', ':id') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit-name">Nome da Camada WMS</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-url">URL do WMS</label>
                        <input type="text" class="form-control" id="edit-url" name="url" required>
                    </div>

                    <input type="hidden" id="edit-wms-link-id" name="wms_link_id">
                    <button type="submit" class="btn btn-primary mt-3">Atualizar Camada</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Função para editar camada WMS ao clicar no botão Editar
    function editWmsLink(wmsLinkId) {
        const wmsLinkName = document.querySelector(`#wms-link-${wmsLinkId} .wms-link-name`).getAttribute('data-name');
        const wmsLinkUrl = document.querySelector(`#wms-link-${wmsLinkId} .wms-link-name`).getAttribute('data-url');
        const formAction = `{{ route('admin.wms.update', ':id') }}`.replace(':id', wmsLinkId);

        document.getElementById('edit-name').value = wmsLinkName;
        document.getElementById('edit-url').value = wmsLinkUrl;
        document.getElementById('edit-wms-link-id').value = wmsLinkId;

        document.getElementById('edit-wms-link-form').action = formAction;

        $('#editWmsLinkModal').modal('show');
    }

    // Função para enviar o formulário de edição via AJAX
    document.getElementById('edit-wms-link-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const wmsLinkId = document.getElementById('edit-wms-link-id').value;
        const formAction = this.action;
        const formData = new FormData(this);

        fetch(formAction, {
            method: 'POST', // Ou 'PUT', conforme sua configuração de rota
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');

            if (data.success) {
                successAlert.classList.remove('d-none');
                successAlert.textContent = data.message;

                document.querySelector(`#wms-link-${wmsLinkId} .wms-link-name`).textContent = `${data.name} (${data.url})`;
                $('#editWmsLinkModal').modal('hide');
            } else {
                errorAlert.classList.remove('d-none');
                errorAlert.textContent = data.message;
            }
        })
        .catch(error => {
            console.error("Erro inesperado:", error.message);
            alert("Ocorreu um erro inesperado ao atualizar a camada WMS: " + error.message);
        });
    });
</script>
@endsection
