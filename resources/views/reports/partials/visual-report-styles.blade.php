<style>
    .project-report-shell {
        display: grid;
        gap: 1.5rem;
    }

    .project-report-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: space-between;
        align-items: center;
    }

    .project-report-switch {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .project-report-switch__button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 999px;
        padding: 0.55rem 0.9rem;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .project-report-switch__button.is-active {
        background: #0f766e;
        border-color: #0f766e;
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(15, 118, 110, 0.18);
    }

    .project-report-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .project-report-visuals {
        display: grid;
        gap: 1.5rem;
    }

    .project-report-stat {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .project-report-stat__label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
    }

    .project-report-stat__value {
        margin-top: 0.35rem;
        font-size: 1.9rem;
        font-weight: 700;
        color: #0f172a;
    }

    .project-report-visual {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: minmax(280px, 420px) minmax(0, 1fr);
        align-items: start;
    }

    .project-report-panel-wrap {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 20px;
        padding: 1.25rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
        min-height: 360px;
        display: grid;
        overflow: hidden;
    }

    .project-report-panel {
        display: none;
        height: 100%;
        min-width: 0;
    }

    .project-report-panel.is-active {
        display: grid;
    }

    .project-report-donut-wrap {
        display: grid;
        place-items: center;
        gap: 1rem;
        height: 100%;
    }

    .project-report-donut {
        width: 240px;
        height: 240px;
        border-radius: 50%;
        position: relative;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.06);
    }

    .project-report-donut::after {
        content: '';
        position: absolute;
        inset: 48px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08);
    }

    .project-report-donut__center {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        text-align: center;
        z-index: 1;
        padding: 0 1.5rem;
    }

    .project-report-donut__center strong {
        display: block;
        font-size: 2rem;
        color: #0f172a;
    }

    .project-report-donut__center span {
        color: #64748b;
        font-size: 0.9rem;
    }

    .project-report-columns {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(88px, 1fr);
        align-items: end;
        justify-content: stretch;
        justify-items: center;
        gap: 1rem;
        min-height: 260px;
        padding: 0 0.5rem 0.75rem;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .project-report-column {
        width: 100%;
        min-width: 88px;
        max-width: 120px;
        display: grid;
        gap: 0.75rem;
        justify-items: center;
        align-self: end;
    }

    .project-report-column__value {
        font-weight: 700;
        color: #0f172a;
    }

    .project-report-column__bar {
        width: 100%;
        max-width: 72px;
        min-height: 14px;
        border-radius: 18px 18px 0 0;
        display: flex;
        align-items: start;
        justify-content: center;
        padding: 0.5rem 0.35rem 0;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.18);
    }

    .project-report-column__label {
        width: 100%;
        text-align: center;
        font-size: 0.85rem;
        color: #475569;
        line-height: 1.3;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .project-report-rows {
        display: grid;
        align-content: center;
        gap: 1rem;
    }

    .project-report-row {
        display: grid;
        gap: 0.45rem;
    }

    .project-report-row__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        color: #334155;
        font-weight: 600;
    }

    .project-report-row__track {
        height: 18px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .project-report-row__fill {
        height: 100%;
        border-radius: inherit;
        min-width: 0;
    }

    .project-report-legend {
        display: grid;
        gap: 0.75rem;
    }

    .project-report-legend__item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 0.75rem;
        align-items: center;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        background: #f8fafc;
    }

    .project-report-legend__swatch {
        width: 0.9rem;
        height: 0.9rem;
        border-radius: 999px;
    }

    .project-report-empty {
        display: grid;
        place-items: center;
        text-align: center;
        color: #64748b;
        min-height: 220px;
    }

    .project-report-table-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
    }

    .project-report-table-card .table td {
        vertical-align: top;
    }

    @media (max-width: 991px) {
        .project-report-visual {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575px) {
        .project-report-columns {
            gap: 0.75rem;
        }

        .project-report-column__bar {
            width: 100%;
        }
    }
</style>
