<?php

namespace App\Services;

use ZipArchive;

class SimpleXlsxService
{
    /**
     * Generate standard OpenXML (.xlsx) binary file using PHP native ZipArchive.
     * Compatible with Microsoft Excel, LibreOffice, WPS, Google Sheets.
     * Preserves leading zeros and prevents scientific notation on NISN & Phone numbers.
     *
     * @param string $title
     * @param string $subtitle
     * @param array $headers
     * @param array $rows
     * @return string Path to temporary generated .xlsx file
     */
    public static function createStudentExport(string $title, string $subtitle, array $headers, array $rows): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_') . '.xlsx';
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat arsip Excel (.xlsx). Pastikan ekstensi zip aktif.');
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
        $zip->addFromString('_rels/.rels', $rootRels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="Data Siswa" sheetId="1" r:id="rId1"/>'
            . '</sheets>'
            . '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        // xf 0: normal
        // xf 1: Title (14pt Bold Navy)
        // xf 2: Subtitle (10pt Muted)
        // xf 3: Header (11pt Bold White, Midnight Navy Fill #101A35)
        // xf 4: Number Center (Calibri 11pt, Border)
        // xf 5: Text Left (Calibri 11pt, Border)
        // xf 6: Text Center (Calibri 11pt, Border)
        // xf 7: Number Center Tinted
        // xf 8: Text Left Tinted
        // xf 9: Text Center Tinted
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="1"><numFmt numFmtId="164" formatCode="@"/></numFmts>'
            . '<fonts count="4">'
            . '<font><sz val="11"/><name val="Calibri"/><color rgb="FF101828"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/><color rgb="FFFFFFFF"/></font>'
            . '<font><b/><sz val="14"/><name val="Calibri"/><color rgb="FF101A35"/></font>'
            . '<font><sz val="9.5"/><name val="Calibri"/><color rgb="FF555555"/></font>'
            . '</fonts>'
            . '<fills count="4">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF101A35"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFD"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFD0D7E4"/></left>'
            . '<right style="thin"><color rgb="FFD0D7E4"/></right>'
            . '<top style="thin"><color rgb="FFD0D7E4"/></top>'
            . '<bottom style="thin"><color rgb="FFD0D7E4"/></bottom>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="10">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0"><alignment vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0"><alignment vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="1" fontId="0" fillId="0" borderId="1" xfId="0"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="1" fontId="0" fillId="3" borderId="1" xfId="0"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0"><alignment horizontal="center" vertical="center"/></xf>'
            . '</cellXfs>'
            . '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $colWidths = [
            1 => 6,   // No
            2 => 14,  // NIS
            3 => 18,  // NISN
            4 => 30,  // Nama Lengkap
            5 => 14,  // L/P
            6 => 12,  // Tingkat
            7 => 34,  // Jurusan
            8 => 18,  // Rombel
            9 => 18,  // No HP
            10 => 12, // Status
        ];

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>';
        foreach ($colWidths as $colIdx => $width) {
            $sheetXml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $width . '" customWidth="1"/>';
        }
        $sheetXml .= '</cols><sheetData>';

        // Row 1: Title
        $safeTitle = htmlspecialchars($title, ENT_XML1, 'UTF-8');
        $sheetXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetXml .= '<c r="A1" s="1" t="inlineStr"><is><t>' . $safeTitle . '</t></is></c>';
        $sheetXml .= '</row>';

        // Row 2: Subtitle
        $safeSubtitle = htmlspecialchars($subtitle, ENT_XML1, 'UTF-8');
        $sheetXml .= '<row r="2" ht="18" customHeight="1">';
        $sheetXml .= '<c r="A2" s="2" t="inlineStr"><is><t>' . $safeSubtitle . '</t></is></c>';
        $sheetXml .= '</row>';

        // Row 3: Blank separator
        $sheetXml .= '<row r="3" ht="10" customHeight="1"/>';

        // Row 4: Table Headers
        $sheetXml .= '<row r="4" ht="26" customHeight="1">';
        $colLetter = 'A';
        foreach ($headers as $h) {
            $safeH = htmlspecialchars($h, ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="' . $colLetter . '4" s="3" t="inlineStr"><is><t>' . $safeH . '</t></is></c>';
            $colLetter++;
        }
        $sheetXml .= '</row>';

        // Rows 5+: Data rows
        $rowIndex = 5;
        foreach ($rows as $rData) {
            $isTinted = ($rowIndex % 2 === 0);
            $sheetXml .= '<row r="' . $rowIndex . '" ht="20" customHeight="1">';

            // Column A: No (Numeric Center)
            $noVal = (int) $rData[0];
            $styleNo = $isTinted ? 7 : 4;
            $sheetXml .= '<c r="A' . $rowIndex . '" s="' . $styleNo . '"><v>' . $noVal . '</v></c>';

            // Column B: NIS (Text Center, preserves leading zeros)
            $nisVal = htmlspecialchars((string) ($rData[1] ?? '-'), ENT_XML1, 'UTF-8');
            $styleCenter = $isTinted ? 9 : 6;
            $sheetXml .= '<c r="B' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $nisVal . '</t></is></c>';

            // Column C: NISN (Text Center, preserves leading zeros like 0071234561)
            $nisnVal = htmlspecialchars((string) ($rData[2] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="C' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $nisnVal . '</t></is></c>';

            // Column D: Nama Lengkap Siswa (Text Left)
            $nameVal = htmlspecialchars((string) ($rData[3] ?? '-'), ENT_XML1, 'UTF-8');
            $styleLeft = $isTinted ? 8 : 5;
            $sheetXml .= '<c r="D' . $rowIndex . '" s="' . $styleLeft . '" t="inlineStr"><is><t>' . $nameVal . '</t></is></c>';

            // Column E: Jenis Kelamin (Text Center)
            $genderVal = htmlspecialchars((string) ($rData[4] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="E' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $genderVal . '</t></is></c>';

            // Column F: Tingkat Kelas (Text Center)
            $gradeVal = htmlspecialchars((string) ($rData[5] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="F' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $gradeVal . '</t></is></c>';

            // Column G: Jurusan (Text Left)
            $majorVal = htmlspecialchars((string) ($rData[6] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="G' . $rowIndex . '" s="' . $styleLeft . '" t="inlineStr"><is><t>' . $majorVal . '</t></is></c>';

            // Column H: Rombel (Text Center)
            $rombelVal = htmlspecialchars((string) ($rData[7] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="H' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $rombelVal . '</t></is></c>';

            // Column I: No HP (Text Center, preserves 08... without scientific notation)
            $phoneVal = htmlspecialchars((string) ($rData[8] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="I' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $phoneVal . '</t></is></c>';

            // Column J: Status Siswa (Text Center)
            $statusVal = htmlspecialchars((string) ($rData[9] ?? '-'), ENT_XML1, 'UTF-8');
            $sheetXml .= '<c r="J' . $rowIndex . '" s="' . $styleCenter . '" t="inlineStr"><is><t>' . $statusVal . '</t></is></c>';

            $sheetXml .= '</row>';
            $rowIndex++;
        }

        $sheetXml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();

        return $tempFile;
    }
}
