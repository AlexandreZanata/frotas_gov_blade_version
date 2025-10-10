@props(['id', 'type' => 'bar', 'data' => [], 'height' => '350'])

<div id="{{ $id }}" style="min-height: {{ $height }}px;"></div>

@once
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
@endonce

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Detectar tema escuro
    const isDark = document.documentElement.classList.contains('dark');

    const options = {
        series: @json($data['series'] ?? []),
        chart: {
            type: '{{ $type }}',
            height: {{ $height }},
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            background: 'transparent',
            fontFamily: 'Inter, system-ui, sans-serif',
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '60%',
                dataLabels: {
                    position: 'top',
                }
            },
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            showAlways: true,
                            fontSize: '16px',
                            fontWeight: 600,
                            color: isDark ? '#e5e7eb' : '#1f2937'
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '12px',
                fontWeight: 600,
                colors: [isDark ? '#e5e7eb' : '#1f2937']
            }
        },
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'],
        labels: @json($data['labels'] ?? []),
        xaxis: {
            categories: @json($data['categories'] ?? []),
            labels: {
                style: {
                    colors: isDark ? '#9ca3af' : '#6b7280',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: isDark ? '#9ca3af' : '#6b7280',
                    fontSize: '12px'
                }
            }
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            labels: {
                colors: isDark ? '#e5e7eb' : '#1f2937'
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            style: {
                fontSize: '12px'
            }
        },
        grid: {
            borderColor: isDark ? '#374151' : '#e5e7eb',
            strokeDashArray: 4,
        },
        theme: {
            mode: isDark ? 'dark' : 'light'
        }
    };

    const chart = new ApexCharts(document.querySelector("#{{ $id }}"), options);
    chart.render();

    // Observar mudanças de tema
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const newIsDark = document.documentElement.classList.contains('dark');
                chart.updateOptions({
                    theme: { mode: newIsDark ? 'dark' : 'light' },
                    tooltip: { theme: newIsDark ? 'dark' : 'light' },
                    xaxis: { labels: { style: { colors: newIsDark ? '#9ca3af' : '#6b7280' }}},
                    yaxis: { labels: { style: { colors: newIsDark ? '#9ca3af' : '#6b7280' }}},
                    legend: { labels: { colors: newIsDark ? '#e5e7eb' : '#1f2937' }},
                    grid: { borderColor: newIsDark ? '#374151' : '#e5e7eb' }
                });
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});
</script>
@endpush
