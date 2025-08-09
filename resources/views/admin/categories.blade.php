@extends('adminlte::page')

@section('title', 'Relatórios de Acessos e Interesses')

@section('adminlte::meta_tags')
    <!-- Adiciona o CSRF Token no head -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <h1 class="mb-3">
        <i class="fas fa-folder-open mr-2"></i> Categorias
    </h1>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Card de criação de categoria -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus-circle mr-2"></i> Criar Nova Categoria
                    </h3>
                </div>
                <div class="card-body">
                    <form id="category-form" action="{{ route('categories.create') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nome da Categoria</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Digite o nome..." required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save mr-1"></i> Criar Categoria
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card de lista de categorias -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list mr-2"></i> Lista de Categorias
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($categories as $category)
                            <li class="list-group-item d-flex justify-content-between align-items-center" id="category-{{ $category->getId() }}">
                                <div>
                                    <strong class="category-name text-primary" 
                                        data-id="{{ $category->getId() }}" 
                                        data-name="{{ $category->getName() }}">
                                        {{ $category->getName() }}
                                    </strong>
                                    @if(!empty($category->getSubcategories()))
                                        <ul class="mt-1 mb-0 pl-3 text-muted small">
                                            @foreach($category->getSubcategories() as $subcategory)
                                                <li>{{ $subcategory->getName() }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-warning btn-sm" title="Editar" onclick="editCategory({{ $category->getId() }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('categories.delete', $category->getId()) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')" style="display:inline;">
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

<!-- Modal para edição -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editCategoryModalLabel">
                    <i class="fas fa-edit mr-2"></i> Editar Categoria
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-category-form" method="POST" action="{{ route('categories.update', ':id') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit-name">Nome da Categoria</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <input type="hidden" id="edit-category-id" name="category_id">
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="fas fa-check mr-1"></i> Atualizar Categoria
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
    function editCategory(categoryId) {
        const categoryName = document.querySelector(`#category-${categoryId} .category-name`).getAttribute('data-name');
        const formAction = `{{ route('categories.update', ':id') }}`.replace(':id', categoryId);

        document.getElementById('edit-name').value = categoryName;
        document.getElementById('edit-category-id').value = categoryId;
        document.getElementById('edit-category-form').action = formAction;

        $('#editCategoryModal').modal('show');
    }

    document.getElementById('edit-category-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const categoryId = document.getElementById('edit-category-id').value;
        const formAction = this.action;
        const formData = new FormData(this);

        fetch(formAction, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $(`#category-${categoryId} .category-name`).text(data.name);
                $('#editCategoryModal').modal('hide');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Erro:", error);
            alert("Ocorreu um erro ao atualizar.");
        });
    });
</script>
