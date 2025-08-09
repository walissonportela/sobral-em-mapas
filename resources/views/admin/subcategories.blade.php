@extends('adminlte::page')

@section('title', 'Subcategorias')

@section('adminlte::meta_tags')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <h1 class="mb-3">
        <i class="fas fa-tags mr-2"></i> Subcategorias
    </h1>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Card de criação -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus-circle mr-2"></i> Criar Nova Subcategoria
                    </h3>
                </div>
                <div class="card-body">
                    <form id="subcategory-form" action="{{ route('subcategories.create') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nome da Subcategoria</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Digite o nome..." required>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Categoria</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Selecione a Categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->getId() }}">{{ $category->getName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save mr-1"></i> Criar Subcategoria
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list mr-2"></i> Lista de Subcategorias
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($subcategories as $subcategory)
                            <li class="list-group-item d-flex justify-content-between align-items-center" id="subcategory-{{ $subcategory->getId() }}">
                                <span class="subcategory-name text-primary"
                                    data-id="{{ $subcategory->getId() }}"
                                    data-name="{{ $subcategory->getName() }}"
                                    data-category-id="{{ $subcategory->getCategoryId() }}">
                                    {{ $subcategory->getName() }} <small class="text-muted">({{ $subcategory->getCategory()->getName() }})</small>
                                </span>
                                <div class="btn-group">
                                    <button class="btn btn-warning btn-sm" title="Editar" onclick="editSubcategory({{ $subcategory->getId() }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('subcategories.delete', $subcategory->getId()) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Modal edição -->
<div class="modal fade" id="editSubcategoryModal" tabindex="-1" role="dialog" aria-labelledby="editSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editSubcategoryModalLabel">
                    <i class="fas fa-edit mr-2"></i> Editar Subcategoria
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="error-alert" class="alert alert-danger d-none"></div>
                <div id="success-alert" class="alert alert-success d-none"></div>

                <form id="edit-subcategory-form" method="POST" action="{{ route('subcategories.update', ':id') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit-name">Nome da Subcategoria</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-category_id">Categoria</label>
                        <select class="form-control" id="edit-category_id" name="category_id" required>
                            <option value="">Selecione a Categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->getId() }}">{{ $category->getName() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="edit-subcategory-id" name="subcategory_id">
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="fas fa-check mr-1"></i> Atualizar Subcategoria
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
function editSubcategory(subcategoryId) {
    const el = document.querySelector(`#subcategory-${subcategoryId} .subcategory-name`);
    const subcategoryName = el.getAttribute('data-name');
    const categoryId = el.getAttribute('data-category-id');
    const formAction = `{{ route('subcategories.update', ':id') }}`.replace(':id', subcategoryId);

    document.getElementById('edit-name').value = subcategoryName;
    document.getElementById('edit-category_id').value = categoryId;
    document.getElementById('edit-subcategory-id').value = subcategoryId;
    document.getElementById('edit-subcategory-form').action = formAction;

    $('#editSubcategoryModal').modal('show');
}

document.getElementById('edit-subcategory-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const subcategoryId = document.getElementById('edit-subcategory-id').value;
    const formAction = this.action;
    const formData = new FormData(this);

    fetch(formAction, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(response => {
        const type = response.headers.get("content-type");
        return (type && type.includes("application/json")) ? response.json() : response.text().then(text => { throw new Error(text); });
    })
    .then(data => {
        if (data.success) {
            document.querySelector(`#subcategory-${subcategoryId} .subcategory-name`).textContent = `${data.name} (${data.category_name})`;
            $('#editSubcategoryModal').modal('hide');
        } else {
            const errorAlert = document.getElementById('error-alert');
            errorAlert.classList.remove('d-none');
            errorAlert.textContent = data.message;
        }
    })
    .catch(error => {
        alert("Erro: " + error.message);
    });
});
</script>

