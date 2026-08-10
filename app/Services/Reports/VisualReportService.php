<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class VisualReportService
{
    /**
     * @param  iterable<string, int>  $labelCounts
     * @return array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int}
     */
    public function reportDataFromLabelCounts(iterable $labelCounts): array
    {
        $categories = [];
        $values = [];

        foreach ($labelCounts as $label => $value) {
            $categories[] = (string) $label;
            $values[] = (int) $value;
        }

        $total = array_sum($values);
        $percentages = array_map(
            static fn (int $value): float => $total > 0 ? round(($value / $total) * 100, 2) : 0.0,
            $values
        );

        return [
            'categories' => $categories,
            'values' => $values,
            'percentages' => $percentages,
            'total' => $total,
        ];
    }

    /**
     * @param  array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int}  $reportData
     * @return array<int, array{label: string, value: int, percentage: float, color: string}>
     */
    public function buildSegments(array $reportData): array
    {
        $palette = [
            '#0f766e',
            '#1d4ed8',
            '#b45309',
            '#be123c',
            '#7c3aed',
            '#0891b2',
            '#4d7c0f',
            '#c2410c',
        ];

        $segments = [];

        foreach ($reportData['categories'] as $index => $category) {
            $segments[] = [
                'label' => $category,
                'value' => $reportData['values'][$index] ?? 0,
                'percentage' => $reportData['percentages'][$index] ?? 0.0,
                'color' => $palette[$index % count($palette)],
            ];
        }

        return $segments;
    }

    /**
     * @param  array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int}  $reportData
     * @return array{
     *     key: string,
     *     title: string,
     *     description: string,
     *     total_label: string,
     *     value_label: string,
     *     data: array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int},
     *     segments: array<int, array{label: string, value: int, percentage: float, color: string}>
     * }
     */
    public function makeVisual(
        string $key,
        string $title,
        string $description,
        array $reportData,
        string $totalLabel,
        string $valueLabel = 'registros'
    ): array {
        return [
            'key' => $key,
            'title' => $title,
            'description' => $description,
            'total_label' => $totalLabel,
            'value_label' => $valueLabel,
            'data' => $reportData,
            'segments' => $this->buildSegments($reportData),
        ];
    }

    /**
     * Render a branded PDF using the shared report template.
     *
     * @param  array<int, array{label: string, value: string, caption: string}>  $reportInsights
     * @param  array<int, array{
     *     key: string,
     *     title: string,
     *     description: string,
     *     total_label: string,
     *     value_label: string,
     *     data: array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int},
     *     segments: array<int, array{label: string, value: int, percentage: float, color: string}>
     * }>  $reportVisuals
     * @param  ?array{
     *     title: string,
     *     description: string,
     *     columns: array<int, string>,
     *     rows: array<int, array<int, string>>
     * }  $reportTable
     * @param  array<int, string>  $reportFiltersSummary
     */
    public function downloadPdf(
        string $reportKey,
        string $reportTitle,
        string $reportDescription,
        array $reportInsights,
        array $reportVisuals,
        ?array $reportTable,
        array $reportFiltersSummary,
        string $reportSubject = 'Reporte institucional',
        string $filenamePrefix = 'reporte'
    ): Response {
        $generatedAt = now();

        $pdf = Pdf::loadView('projects.report-pdf', [
            'reportSubject' => $reportSubject,
            'reportTitle' => $reportTitle,
            'reportDescription' => $reportDescription,
            'reportKey' => $reportKey,
            'reportGeneratedAt' => $generatedAt,
            'reportInsights' => $reportInsights,
            'reportVisuals' => $reportVisuals,
            'reportTable' => $reportTable,
            'reportFiltersSummary' => $reportFiltersSummary,
            'logoDataUri' => $this->publicAssetDataUri('assets/tablar-logo.png'),
        ])
            ->setOptions([
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false,
                'debugCss' => false,
                'debugKeepTemp' => false,
                'debugText' => false,
            ])
            ->setPaper('a4', 'landscape');

        $filename = sprintf(
            '%s-%s-%s.pdf',
            Str::slug($filenamePrefix),
            Str::slug($reportKey),
            $generatedAt->format('Ymd-His')
        );

        return $pdf->download($filename);
    }

    private function publicAssetDataUri(string $relativePath): ?string
    {
        $absolutePath = public_path($relativePath);

        if (! is_file($absolutePath)) {
            return null;
        }

        $mimeType = mime_content_type($absolutePath) ?: 'image/png';
        $contents = file_get_contents($absolutePath);

        if ($contents === false) {
            return null;
        }

        return 'data:' . $mimeType . ';base64,' . base64_encode($contents);
    }
}
