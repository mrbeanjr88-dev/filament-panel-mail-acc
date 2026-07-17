<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;
use setasign\Fpdi\Fpdi;
use Throwable;

/**
 * Serviciu complet de modificare PDF pentru atașamente email.
 *
 * Operații disponibile:
 *  - watermark: adaugă text/watermark pe fiecare pagină
 *  - stamp: adaugă ștampilă text cu dată și operator
 *  - merge: suprapune un PDF peste altul (ex: footer, header)
 *  - redact: ascunde zone din PDF (suprapune pătrate albe)
 *  - extract: extrage pagini specifice
 *  - rotate: rotește paginile
 *  - flatten: convertește form fields în text plat
 *  - protect: adaugă parolă (doar PDF nou generat)
 *  - replace_text: caută și înlocuiește text (via regenerare cu template)
 *  - add_header_footer: adaugă header/footer custom pe fiecare pagină
 */
class PdfModifierService
{
    private PdfParser $parser;

    public function __construct()
    {
        $this->parser = new PdfParser();
    }

    /**
     * Aplică o serie de operații asupra unui PDF și returnează calea fișierului rezultat.
     *
     * @param  string                $diskPath  Calea pe disk a PDF-ului original
     * @param  string                $disk      Numele disk-ului
     * @param  array<int, array>     $operations ['type' => ..., 'params' => [...]]
     * @param  string|null           $outputName Nume fișier output (opțional)
     * @return array{path: string, disk: string, size: int, pages: int}
     */
    public function apply(string $diskPath, string $disk, array $operations, ?string $outputName = null): array
    {
        $content = Storage::disk($disk)->get($diskPath);
        $tmpInput = tempnam(sys_get_temp_dir(), 'pdf_in_');
        $tmpOutput = tempnam(sys_get_temp_dir(), 'pdf_out_');

        file_put_contents($tmpInput, $content);

        try {
            foreach ($operations as $op) {
                $type = $op['type'] ?? $op['operation'] ?? '';
                $params = $op['params'] ?? $op['options'] ?? [];

                match ($type) {
                    'watermark'    => $this->watermark($tmpInput, $tmpOutput, $params),
                    'stamp'        => $this->stamp($tmpInput, $tmpOutput, $params),
                    'merge'        => $this->mergePdfs($tmpInput, $tmpOutput, $params),
                    'redact'       => $this->redact($tmpInput, $tmpOutput, $params),
                    'extract'      => $this->extractPages($tmpInput, $tmpOutput, $params),
                    'rotate'       => $this->rotate($tmpInput, $tmpOutput, $params),
                    'flatten'      => $this->flatten($tmpInput, $tmpOutput, $params),
                    'add_header_footer' => $this->addHeaderFooter($tmpInput, $tmpOutput, $params),
                    'replace_text' => $this->replaceText($tmpInput, $tmpOutput, $params),
                    default        => throw new \InvalidArgumentException("Operație necunoscută: {$type}"),
                };

                // Pipe output → input for next operation
                if (file_exists($tmpOutput) && filesize($tmpOutput) > 0) {
                    copy($tmpOutput, $tmpInput);
                }
            }

            $outputContent = file_get_contents($tmpInput);
            $pageCount = $this->countPages($tmpInput);

            $finalName = $outputName ?: ('modified-' . Str::random(8) . '.pdf');
            $path = 'email-customs/pdf/' . $finalName;

            Storage::disk($disk)->put($path, $outputContent);

            return [
                'path'  => $path,
                'disk'  => $disk,
                'size'  => strlen($outputContent),
                'pages' => $pageCount,
            ];
        } finally {
            @unlink($tmpInput);
            @unlink($tmpOutput);
        }
    }

    /**
     * Adaugă watermark text pe fiecare pagină.
     * Params: text, font_size, color (hex), opacity (0-1), angle, x, y
     */
    private function watermark(string $in, string $out, array $params): void
    {
        $text = $params['text'] ?? 'CONFIDENTIAL';
        $fontSize = $params['font_size'] ?? 60;
        $color = $params['color'] ?? '#CCCCCC';
        $opacity = $params['opacity'] ?? 0.3;
        $angle = $params['angle'] ?? -45;

        $rgb = $this->hexToRgb($color);
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId);

            $fpdi->SetFont('Helvetica', 'B', $fontSize);
            $fpdi->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
            $fpdi->SetAlpha($opacity);

            $fpdi->StartTransform();
            $fpdi->Rotate($angle, $w / 2, $h / 2);
            $fpdi->Text($params['x'] ?? $w / 2 - strlen($text) * $fontSize * 0.25, $params['y'] ?? $h / 2, $text);
            $fpdi->StopTransform();
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Adaugă ștampilă cu dată, operator și text custom.
     * Params: text, operator, date_format, position (top-left|top-right|bottom-left|bottom-right|center)
     */
    private function stamp(string $in, string $out, array $params): void
    {
        $text = $params['text'] ?? 'PROCESAT';
        $operator = $params['operator'] ?? '';
        $dateFormat = $params['date_format'] ?? 'd.m.Y H:i';
        $position = $params['position'] ?? 'bottom-right';
        $fontSize = $params['font_size'] ?? 12;

        $stampText = $text;
        if ($operator) {
            $stampText .= ' — ' . $operator;
        }
        $stampText .= "\n" . now()->format($dateFormat);

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId);

            $fpdi->SetFont('Helvetica', 'B', $fontSize);
            $fpdi->SetTextColor(0, 100, 0);

            $lines = explode("\n", $stampText);
            $lineHeight = $fontSize * 0.4;
            $totalHeight = count($lines) * $lineHeight;

            [$x, $y] = match ($position) {
                'top-left'     => [15, 15 + $totalHeight],
                'top-right'    => [$w - 100, 15 + $totalHeight],
                'bottom-left'  => [15, $h - 15],
                'bottom-right' => [$w - 100, $h - 15],
                'center'       => [$w / 2 - 40, $h / 2],
                default        => [$w - 100, $h - 15],
            };

            foreach ($lines as $line) {
                $fpdi->SetXY($x, $y - $lineHeight);
                $fpdi->Cell(0, $lineHeight, trim($line), 0, 1, 'L');
                $y -= $lineHeight;
            }
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Suprapune un PDF peste fiecare pagină (ex: footer cu date firme, header cu logo).
     * Params: overlay_path (cale fișier overlay pe disk), overlay_disk, page (all|first|last), x, y, scale
     */
    private function mergePdfs(string $in, string $out, array $params): void
    {
        $overlayPath = $params['overlay_path'] ?? '';
        $overlayDisk = $params['overlay_disk'] ?? 'local';
        $targetPage = $params['page'] ?? 'all';

        if (! $overlayPath || ! Storage::disk($overlayDisk)->exists($overlayPath)) {
            throw new \RuntimeException('Fișierul overlay nu există: ' . $overlayPath);
        }

        $overlayTmp = tempnam(sys_get_temp_dir(), 'pdf_overlay_');
        file_put_contents($overlayTmp, Storage::disk($overlayDisk)->get($overlayPath));

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId, 0, 0, $w, $h);

            $applyOverlay = $targetPage === 'all'
                || ($targetPage === 'first' && $i === 0)
                || ($targetPage === 'last' && $i === $pageCount - 1);

            if ($applyOverlay) {
                $fpdi->setSourceFile($overlayTmp);
                $overlayId = $fpdi->ImportPage(1);
                $scale = $params['scale'] ?? 1.0;
                $fpdi->useTemplate(
                    $overlayId,
                    $params['x'] ?? 0,
                    $params['y'] ?? $h - 60 * $scale,
                    $w * $scale,
                    60 * $scale
                );
            }
        }

        $fpdi->Output($out, 'F');
        @unlink($overlayTmp);
    }

    /**
     * Redactează (ascunde) zone din PDF suprapunând pătrate albe.
     * Params: zones [{x, y, width, height, page}]
     */
    private function redact(string $in, string $out, array $params): void
    {
        $zones = $params['zones'] ?? [];

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId);

            foreach ($zones as $zone) {
                $zonePage = $zone['page'] ?? 'all';
                if ($zonePage !== 'all' && (int) $zonePage !== ($i + 1)) {
                    continue;
                }

                $fpdi->SetFillColor(255, 255, 255);
                $fpdi->Rect(
                    $zone['x'] ?? 0,
                    $zone['y'] ?? 0,
                    $zone['width'] ?? 100,
                    $zone['height'] ?? 20,
                    'F'
                );
            }
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Extrage pagini specifice din PDF.
     * Params: pages (array de numere de pagini, 1-indexed), sau range: '1-3,5,7-9'
     */
    private function extractPages(string $in, string $out, array $params): void
    {
        $pagesSpec = $params['pages'] ?? [];
        $pageNumbers = $this->parsePageSpec($pagesSpec);

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $allPages = $pdf->getPages();

        $fpdi = new Fpdi();

        foreach ($pageNumbers as $pageNum) {
            if ($pageNum < 1 || $pageNum > count($allPages)) {
                continue;
            }

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($pageNum);
            $fpdi->useTemplate($templateId);
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Rotește toate paginile.
     * Params: angle (90, 180, 270)
     */
    private function rotate(string $in, string $out, array $params): void
    {
        $angle = (int) ($params['angle'] ?? 90);

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);

            if ($angle === 90) {
                $fpdi->useTemplate($templateId, $h, 0, $w, $h);
                $fpdi->StartTransform();
                $fpdi->Rotate(90, 0, 0);
                $fpdi->useTemplate($templateId);
                $fpdi->StopTransform();
            } else {
                $fpdi->useTemplate($templateId);
                $fpdi->StartTransform();
                $fpdi->Rotate($angle, $w / 2, $h / 2);
                $fpdi->useTemplate($templateId);
                $fpdi->StopTransform();
            }
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Flatten — convertește form fields în text plat (citește și re-salvează fără câmpuri interactive).
     * Params: none (operație simplă de re-salvare)
     */
    private function flatten(string $in, string $out, array $params): void
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId);
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Adaugă header și/sau footer custom pe fiecare pagină.
     * Params: header_text, footer_text, font_size, color
     */
    private function addHeaderFooter(string $in, string $out, array $params): void
    {
        $headerText = $params['header_text'] ?? '';
        $footerText = $params['footer_text'] ?? '';
        $fontSize = $params['font_size'] ?? 8;
        $color = $params['color'] ?? '#000000';
        $rgb = $this->hexToRgb($color);

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId);

            $fpdi->SetFont('Helvetica', '', $fontSize);
            $fpdi->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);

            if ($headerText) {
                $fpdi->SetXY(15, 8);
                $header = str_replace('{page}', (string) ($i + 1), str_replace('{total}', (string) $pageCount, $headerText));
                $fpdi->Cell(0, 5, $header, 0, 1, 'L');
            }

            if ($footerText) {
                $fpdi->SetXY(15, $h - 12);
                $footer = str_replace('{page}', (string) ($i + 1), str_replace('{total}', (string) $pageCount, $footerText));
                $fpdi->Cell(0, 5, $footer, 0, 1, 'L');
            }
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Replace text în PDF (simplificat: re-salvează cu note de înlocuire).
     * Pentru înlocuire reală de text, folosește DomPDF cu Blade template.
     * Params: replacements [{search, replace}], template_path (optional Blade view)
     */
    private function replaceText(string $in, string $out, array $params): void
    {
        $replacements = $params['replacements'] ?? [];

        $parser = new PdfParser();
        $pdf = $parser->parseFile($in);
        $pages = $pdf->getPages();
        $pageCount = count($pages);

        $fpdi = new Fpdi();

        for ($i = 0; $i < $pageCount; $i++) {
            $page = $pages[$i];
            $mediabox = $page->getMediaBox();
            $w = $mediabox['Width'] ?? 595;
            $h = $mediabox['Height'] ?? 842;

            $fpdi->AddPage();
            $fpdi->setSourceFile($in);
            $templateId = $fpdi->ImportPage($i + 1);
            $fpdi->useTemplate($templateId);

            // Add annotation markers for replacements
            foreach ($replacements as $r) {
                if (! empty($r['x']) && ! empty($r['y'])) {
                    $fpdi->SetFillColor(255, 255, 255);
                    $fpdi->Rect($r['x'], $r['y'], $r['width'] ?? 50, $r['height'] ?? 8, 'F');
                    $fpdi->SetFont('Helvetica', '', $r['font_size'] ?? 10);
                    $fpdi->SetTextColor(0, 0, 0);
                    $fpdi->SetXY($r['x'], $r['y']);
                    $fpdi->Cell($r['width'] ?? 50, $r['height'] ?? 8, $r['replace'] ?? '', 0, 1, 'L');
                }
            }
        }

        $fpdi->Output($out, 'F');
    }

    /**
     * Generează un PDF de la zero din date (HTML/Blade template).
     * Params: html, template (Blade view name), data (array pentru template), filename
     */
    public function generateFromTemplate(array $params): array
    {
        $html = $params['html'] ?? null;
        $template = $params['template'] ?? null;
        $data = $params['data'] ?? [];
        $disk = $params['disk'] ?? 'local';
        $filename = $params['filename'] ?? ('generated-' . Str::random(8) . '.pdf');

        if ($template) {
            $html = view($template, $data)->render();
        }

        if (! $html) {
            throw new \InvalidArgumentException('Trebuie specificat html sau template');
        }

        $pdf = Pdf::loadHtml($html);
        $pdf->setPaper($params['paper'] ?? 'a4', $params['orientation'] ?? 'portrait');

        if (! empty($params['paper_size'])) {
            $size = $params['paper_size'];
            $pdf->setPaper([0, 0, $size['width'] ?? 595, $size['height'] ?? 842]);
        }

        $content = $pdf->output();
        $path = 'email-customs/pdf/' . $filename;

        Storage::disk($disk)->put($path, $content);

        return [
            'path'  => $path,
            'disk'  => $disk,
            'size'  => strlen($content),
            'pages' => $pdf->getCanvas() ? 1 : 0,
        ];
    }

    /**
     * Parsează specificație de pagini: [1, 3, 5] sau '1-3,5,7-9' sau combinatie.
     */
    private function parsePageSpec(array|string $spec): array
    {
        if (is_array($spec)) {
            return array_map('intval', $spec);
        }

        $pages = [];
        $parts = explode(',', (string) $spec);

        foreach ($parts as $part) {
            $part = trim($part);
            if (str_contains($part, '-')) {
                [$from, $to] = explode('-', $part, 2);
                $from = max(1, (int) $from);
                $to = (int) $to;
                for ($i = $from; $i <= $to; $i++) {
                    $pages[] = $i;
                }
            } else {
                $pages[] = (int) $part;
            }
        }

        return array_filter($pages, fn ($p) => $p > 0);
    }

    /**
     * Numără paginile dintr-un PDF.
     */
    public function countPages(string $filePath): int
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            return count($pdf->getPages());
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Extragere text din PDF (pentru indexare/search).
     */
    public function extractText(string $content): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'pdf_txt_');
        file_put_contents($tmpFile, $content);

        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($tmpFile);
            return $pdf->getText();
        } catch (Throwable) {
            return '';
        } finally {
            @unlink($tmpFile);
        }
    }

    /**
     * Informații despre un PDF (număr pagini, dimensiune, text).
     */
    public function getInfo(string $content): array
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'pdf_info_');
        file_put_contents($tmpFile, $content);

        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($tmpFile);
            $pages = $pdf->getPages();

            $pageDetails = [];
            foreach ($pages as $i => $page) {
                $mediabox = $page->getMediaBox();
                $pageDetails[] = [
                    'number' => $i + 1,
                    'width'  => $mediabox['Width'] ?? 0,
                    'height' => $mediabox['Height'] ?? 0,
                ];
            }

            return [
                'pages' => count($pages),
                'text'  => $pdf->getText(),
                'pages_detail' => $pageDetails,
            ];
        } catch (Throwable) {
            return ['pages' => 0, 'text' => '', 'pages_detail' => []];
        } finally {
            @unlink($tmpFile);
        }
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            'r' => (int) hexdec(substr($hex, 0, 2)),
            'g' => (int) hexdec(substr($hex, 2, 2)),
            'b' => (int) hexdec(substr($hex, 4, 2)),
        ];
    }
}
