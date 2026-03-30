<?php

namespace App\Exports;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Générateur Excel XML (SpreadsheetML / Excel 2003)
 * Fonctionne sans dépendance (ZipArchive ou PhpSpreadsheet non requis).
 * Le fichier .xls produit s'ouvre nativement dans Excel, LibreOffice et Google Sheets.
 */
class ExcelExport
{
    private string $sheetName;
    private array  $headers;
    private array  $rows;
    private array  $headerStyles;

    public function __construct(string $sheetName = 'Export')
    {
        $this->sheetName    = $sheetName;
        $this->headers      = [];
        $this->rows         = [];
        $this->headerStyles = [];
    }

    public function setHeaders(array $headers, array $styles = []): static
    {
        $this->headers      = $headers;
        $this->headerStyles = $styles;
        return $this;
    }

    public function addRow(array $row): static
    {
        $this->rows[] = $row;
        return $this;
    }

    public function addRows(array $rows): static
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
        return $this;
    }

    /**
     * Génère et retourne le StreamedResponse Excel XML.
     */
    public function download(string $filename): StreamedResponse
    {
        if (!str_ends_with($filename, '.xls')) {
            $filename .= '.xls';
        }

        $xml = $this->buildXml();

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function buildXml(): string
    {
        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<?mso-application progid="Excel.Sheet"?>';
        $lines[] = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $lines[] = '  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"';
        $lines[] = '  xmlns:x="urn:schemas-microsoft-com:office:excel">';

        // Styles
        $lines[] = '<Styles>';
        $lines[] = '  <Style ss:ID="header">';
        $lines[] = '    <Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>';
        $lines[] = '    <Interior ss:Color="#0A4D8C" ss:Pattern="Solid"/>';
        $lines[] = '    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>';
        $lines[] = '  </Style>';
        $lines[] = '  <Style ss:ID="default">';
        $lines[] = '    <Alignment ss:Vertical="Center"/>';
        $lines[] = '    <Font ss:Size="10"/>';
        $lines[] = '  </Style>';
        $lines[] = '  <Style ss:ID="alt">';
        $lines[] = '    <Interior ss:Color="#F0F4FA" ss:Pattern="Solid"/>';
        $lines[] = '    <Alignment ss:Vertical="Center"/>';
        $lines[] = '    <Font ss:Size="10"/>';
        $lines[] = '  </Style>';
        $lines[] = '</Styles>';

        $lines[] = "<Worksheet ss:Name=\"" . htmlspecialchars($this->sheetName, ENT_XML1) . "\">";
        $lines[] = "<Table>";

        // En-têtes
        if (!empty($this->headers)) {
            $lines[] = "<Row ss:Height=\"22\">";
            foreach ($this->headers as $header) {
                $lines[] = "<Cell ss:StyleID=\"header\"><Data ss:Type=\"String\">"
                    . htmlspecialchars((string) $header, ENT_XML1 | ENT_COMPAT, 'UTF-8')
                    . "</Data></Cell>";
            }
            $lines[] = "</Row>";
        }

        // Données
        foreach ($this->rows as $idx => $row) {
            $style = ($idx % 2 === 1) ? 'alt' : 'default';
            $lines[] = "<Row ss:Height=\"18\">";
            foreach ($row as $cell) {
                $type  = is_numeric($cell) ? 'Number' : 'String';
                $value = htmlspecialchars((string) ($cell ?? ''), ENT_XML1 | ENT_COMPAT, 'UTF-8');
                $lines[] = "<Cell ss:StyleID=\"{$style}\"><Data ss:Type=\"{$type}\">{$value}</Data></Cell>";
            }
            $lines[] = "</Row>";
        }

        $lines[] = "</Table>";
        $lines[] = "<WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">";
        $lines[] = "  <FreezePanes/><FrozenNoSplit/><SplitHorizontal>1</SplitHorizontal>";
        $lines[] = "  <TopRowBottomPane>1</TopRowBottomPane>";
        $lines[] = "</WorksheetOptions>";
        $lines[] = "</Worksheet>";
        $lines[] = "</Workbook>";

        return implode("\n", $lines);
    }
}
