@extends('adminlte::page')

@section('title', 'Configurações')

@section('content_header')
    <h1><i class="fas fa-chart-line"></i> Relatórios de Acessos e Interesses</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Acessos por Mês
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="graficoBarras"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-signal mr-1"></i>
                        Interesses por Assunto
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="graficoLinha"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Acessos por Dispositivo
                    </h3>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="graficoPizza"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star mr-1"></i>
                        Popularidade por Categoria
                    </h3>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="graficoPolar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Paleta de cores baseada em temas de mapa e geoinformação
        const customColors = {
            mapPrimary: 'rgba(52, 152, 219, 0.8)', // Azul para água e dados
            mapSecondary: 'rgba(46, 204, 113, 0.8)', // Verde para vegetação e sucesso
            mapWarning: 'rgba(241, 196, 15, 0.8)', // Amarelo para alertas ou destaques
            mapDanger: 'rgba(231, 76, 60, 0.8)', // Vermelho para perigos ou erros
            mapInfo: 'rgba(155, 89, 182, 0.8)', // Roxo para pontos de interesse
            mapGrey: 'rgba(149, 165, 166, 0.8)' // Cinza para elementos de base
        };

        const borderColors = {
            mapPrimary: 'rgba(52, 152, 219, 1)',
            mapSecondary: 'rgba(46, 204, 113, 1)',
            mapWarning: 'rgba(241, 196, 15, 1)',
            mapDanger: 'rgba(231, 76, 60, 1)',
            mapInfo: 'rgba(155, 89, 182, 1)',
            mapGrey: 'rgba(149, 165, 166, 1)'
        };

        // --- Configurações Comuns de Gráfico ---
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutQuart'
            }
        };

        // --- Gráfico de Barras (Acessos por Mês) ---
        // Este gráfico pode mostrar o número de usuários que acessaram o mapa a cada mês.
        const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
        new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Usuários Ativos',
                    data: [250, 280, 310, 350, 420, 480],
                    backgroundColor: customColors.mapPrimary,
                    borderColor: borderColors.mapPrimary,
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Acessos'
                        }
                    }
                }
            }
        });

        // --- Gráfico de Linha (Interesses por Assunto) ---
        // Este gráfico pode mostrar a tendência de visualização de camadas ao longo do tempo.
        const ctxLinha = document.getElementById('graficoLinha').getContext('2d');
        new Chart(ctxLinha, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Saúde',
                    data: [10, 15, 20, 25, 30, 35],
                    fill: false,
                    borderColor: customColors.mapDanger,
                    tension: 0.3
                }, {
                    label: 'Educação',
                    data: [5, 10, 12, 15, 18, 22],
                    fill: false,
                    borderColor: customColors.mapInfo,
                    tension: 0.3
                }, {
                    label: 'Infraestrutura',
                    data: [8, 9, 11, 14, 17, 20],
                    fill: false,
                    borderColor: customColors.mapSecondary,
                    tension: 0.3
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Número de Ativações'
                        }
                    }
                }
            }
        });

        // --- Gráfico de Pizza (Distribuição de Acessos por Dispositivo) ---
        // Este gráfico mostra a proporção de acessos por tipo de dispositivo.
        const ctxPizza = document.getElementById('graficoPizza').getContext('2d');
        new Chart(ctxPizza, {
            type: 'pie',
            data: {
                labels: ['Desktop', 'Mobile', 'Tablet'],
                datasets: [{
                    data: [75, 20, 5],
                    backgroundColor: [
                        customColors.mapPrimary,
                        customColors.mapSecondary,
                        customColors.mapWarning
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + '%';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // --- Gráfico Polar (Popularidade por Categoria) ---
        // Este gráfico mostra a popularidade relativa das categorias de camadas.
        const ctxPolar = document.getElementById('graficoPolar').getContext('2d');
        new Chart(ctxPolar, {
            type: 'polarArea',
            data: {
                labels: ['Saúde', 'Educação', 'Infraestrutura', 'Mobilidade Urbana', 'Meio Ambiente'],
                datasets: [{
                    data: [25, 18, 22, 15, 20],
                    backgroundColor: [
                        customColors.mapDanger,
                        customColors.mapInfo,
                        customColors.mapSecondary,
                        customColors.mapPrimary,
                        'rgba(192, 192, 192, 0.6)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + '% de popularidade';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection