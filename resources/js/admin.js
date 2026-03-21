import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Filler,
    Tooltip,
} from 'chart.js';

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip);

const css = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

/*
 * One series (revenue per day), one axis. Order counts ride along in the
 * tooltip instead of a second y-scale.
 */
function revenueChart(canvas) {
    const data = JSON.parse(canvas.dataset.series);
    const currency = canvas.dataset.currency;
    const money = (value) => currency + Math.round(value).toLocaleString('en-US');

    const theme = () => ({
        accent: css('--c-accent'),
        ink: css('--c-ink'),
        muted: css('--c-muted'),
        line: css('--c-line'),
        raised: css('--c-raised'),
    });

    const build = () => {
        const t = theme();

        return new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue',
                    data: data.revenue,
                    borderColor: t.accent,
                    borderWidth: 2,
                    cubicInterpolationMode: 'monotone',
                    fill: true,
                    backgroundColor: (ctx) => {
                        const { chartArea, ctx: c } = ctx.chart;
                        if (!chartArea) return 'transparent';
                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, t.accent + '33');
                        gradient.addColorStop(1, t.accent + '00');
                        return gradient;
                    },
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBorderWidth: 2,
                    pointHoverBorderColor: t.raised,
                    pointHoverBackgroundColor: t.accent,
                    pointHitRadius: 12,
                }],
            },
            options: {
                maintainAspectRatio: false,
                animation: matchMedia('(prefers-reduced-motion: reduce)').matches ? false : { duration: 400 },
                interaction: { mode: 'index', intersect: false },
                layout: { padding: { top: 8 } },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { color: t.line },
                        ticks: { color: t.muted, maxRotation: 0, autoSkipPadding: 16, font: { size: 11 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: t.line, drawTicks: false },
                        border: { display: false },
                        ticks: { color: t.muted, padding: 8, maxTicksLimit: 5, font: { size: 11 }, callback: (v) => money(v) },
                    },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: t.raised,
                        titleColor: t.ink,
                        bodyColor: t.ink,
                        borderColor: t.line,
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => money(ctx.parsed.y),
                            afterLabel: (ctx) => {
                                const n = data.orders[ctx.dataIndex];
                                return `${n} order${n === 1 ? '' : 's'}`;
                            },
                        },
                    },
                },
            },
            plugins: [{
                // Crosshair at the hovered day.
                id: 'crosshair',
                afterDatasetsDraw(chart) {
                    const active = chart.tooltip?.getActiveElements();
                    if (!active?.length) return;
                    const { ctx, chartArea } = chart;
                    const x = active[0].element.x;
                    ctx.save();
                    ctx.strokeStyle = t.muted;
                    ctx.setLineDash([3, 3]);
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(x, chartArea.top);
                    ctx.lineTo(x, chartArea.bottom);
                    ctx.stroke();
                    ctx.restore();
                },
            }],
        });
    };

    let chart = build();

    window.addEventListener('theme-changed', () => {
        chart.destroy();
        chart = build();
    });
}

document.querySelectorAll('canvas[data-chart="revenue"]').forEach(revenueChart);
