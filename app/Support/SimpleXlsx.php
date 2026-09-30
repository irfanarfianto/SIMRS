<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Penulis .xlsx minimal (satu sheet) tanpa dependensi tambahan, cukup ekstensi zip.
 * Nilai numerik ditulis sebagai angka, selain itu sebagai teks; baris pertama (header) dicetak tebal.
 */
class SimpleXlsx
{
    public static function build(string $sheetName, array $header, array $rows, array $numberFormatColumns = []): string
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat berkas xlsx');
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::escape($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');

        // style 0 = normal, 1 = header tebal, 2 = angka ribuan (#,##0)
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>');

        $zip->addFromString('xl/worksheets/sheet1.xml', self::sheetXml($header, $rows, $numberFormatColumns));
        $zip->close();

        return $path;
    }

    private static function sheetXml(array $header, array $rows, array $numberFormatColumns): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<cols>';

        foreach ($header as $i => $judul) {
            $lebar = max(mb_strlen($judul), ...array_map(fn($row) => mb_strlen((string) ($row[$i] ?? '')), $rows ?: [[]])) + 3;
            $xml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . min($lebar, 60) . '" customWidth="1"/>';
        }
        $xml .= '</cols><sheetData>';

        $xml .= self::rowXml(1, $header, 1, []);
        foreach (array_values($rows) as $i => $row) {
            $xml .= self::rowXml($i + 2, array_values($row), 0, $numberFormatColumns);
        }

        return $xml . '</sheetData></worksheet>';
    }

    private static function rowXml(int $rowNumber, array $cells, int $style, array $numberFormatColumns): string
    {
        $xml = '<row r="' . $rowNumber . '">';
        foreach ($cells as $i => $value) {
            $ref = self::columnLetter($i) . $rowNumber;
            if ($style === 0 && (is_int($value) || is_float($value))) {
                $s = in_array($i, $numberFormatColumns, true) ? ' s="2"' : '';
                $xml .= '<c r="' . $ref . '"' . $s . '><v>' . $value . '</v></c>';
            } else {
                $s = $style ? ' s="' . $style . '"' : '';
                $xml .= '<c r="' . $ref . '" t="inlineStr"' . $s . '><is><t xml:space="preserve">' . self::escape((string) $value) . '</t></is></c>';
            }
        }
        return $xml . '</row>';
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';
        for ($n = $index + 1; $n > 0; $n = intdiv($n - 1, 26)) {
            $letter = chr(65 + ($n - 1) % 26) . $letter;
        }
        return $letter;
    }

    private static function escape(string $value): string
    {
        // buang karakter kontrol yang tidak sah di XML 1.0
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value);
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
