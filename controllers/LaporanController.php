<?php
require_once __DIR__ . '/../models/Karyawan.php';
require_once __DIR__ . '/../models/Kriteria.php';

class LaporanController {
    private \PDO $pdo;
    
    public function __construct(\PDO $pdo) { 
        $this->pdo = $pdo; 
    }

    /**
     * Export ke CSV (Excel) - delimiter titik koma agar kompatibel Excel Indonesia
     */
    private function formatTanggalIndo(string $date): string {
        $bulan = array (
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $pecahkan = explode('-', date('Y-m-d', strtotime($date)));
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }

    public function exportExcel(string $tipe): void {
        $data = $_SESSION['hasil_' . $tipe] ?? null;
        if (!$data) die('Tidak ada data.');
        
        $filename = 'laporan_' . $tipe . '_' . date('Ymd_His') . '.xls';
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        $ranking = $data['ranking'];
        $total = count($ranking);
        
        // Jika punishment, urutkan ascending (nilai terendah di atas)
        if ($tipe === 'punishment') {
            usort($ranking, function($a, $b) {
                return $a['nilai'] <=> $b['nilai'];
            });
        }
        
        $judul = ($tipe == 'reward') ? 'REWARD (3 Karyawan Terbaik)' : 'PUNISHMENT (3 Karyawan Terendah)';
        
        if ($tipe == 'reward') {
            $mulai = isset($data['periode_mulai']) ? date('d F Y', strtotime($data['periode_mulai'])) : '-';
            $akhir = isset($data['periode_akhir']) ? date('d F Y', strtotime($data['periode_akhir'])) : '-';
            $periodeText = "$mulai s/d $akhir";
        } else {
            $periodeText = isset($data['periode']) ? date('F Y', strtotime($data['periode'])) : '-';
        }
        
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<style>';
        echo '@page { ';
        echo '  mso-page-orientation: landscape; ';
        echo '  margin: 0.5in 0.5in 0.5in 0.5in; ';
        echo '  mso-header-margin: 0.3in; ';
        echo '  mso-footer-margin: 0.3in; ';
        echo '  mso-horizontal-page-align: center; ';
        echo '}';
        echo 'table { border-collapse: collapse; width: 1400pt; font-family: Arial, sans-serif; }';
        echo 'td, th { border: 1pt solid #000000; padding: 5px; }';
        echo '</style>';
        echo '<!--[if gte mso 9]><xml>
<x:ExcelWorkbook>
 <x:ExcelWorksheets>
  <x:ExcelWorksheet>
   <x:Name>Laporan SPK</x:Name>
   <x:WorksheetOptions>
    <x:FitToPage/>
    <x:Print>
     <x:FitWidth>1</x:FitWidth>
     <x:FitHeight>0</x:FitHeight>
     <x:ValidPrinterInfo/>
     <x:PaperSizeIndex>9</x:PaperSizeIndex>
     <x:HorizontalResolution>600</x:HorizontalResolution>
     <x:VerticalResolution>600</x:VerticalResolution>
    </x:Print>
    <x:Zoom>100</x:Zoom>
    <x:PageSetup>
     <x:Layout x:Orientation="Landscape" x:CenterHorizontal="1"/>
     <x:PageMargins x:Bottom="0.5" x:Left="0.5" x:Right="0.5" x:Top="0.5"/>
    </x:PageSetup>
    <x:Selected/>
    <x:DoNotDisplayGridlines/>
    <x:ProtectContents>False</x:ProtectContents>
    <x:ProtectObjects>False</x:ProtectObjects>
    <x:ProtectScenarios>False</x:ProtectScenarios>
   </x:WorksheetOptions>
  </x:ExcelWorksheet>
 </x:ExcelWorksheets>
 <x:ProtectStructure>False</x:ProtectStructure>
 <x:ProtectWindows>False</x:ProtectWindows>
</x:ExcelWorkbook>
</xml><![endif]-->';
        echo '</head>';
        echo '<body>';
        
        echo "<table cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 1400pt;'>";
        
        // Dummy row wajib untuk memaksa lebar kolom di Excel HTML (menghindari bug colspan)
        echo "<tr style='mso-height-source:userset; height:0pt;'>";
        echo "<td width='133' style='width: 100pt; border: none; padding: 0;'></td>";
        echo "<td width='267' style='width: 200pt; border: none; padding: 0;'></td>";
        echo "<td width='667' style='width: 500pt; border: none; padding: 0;'></td>";
        echo "<td width='400' style='width: 300pt; border: none; padding: 0;'></td>";
        echo "<td width='200' style='width: 150pt; border: none; padding: 0;'></td>";
        echo "<td width='200' style='width: 150pt; border: none; padding: 0;'></td>";
        echo "</tr>";
        
        echo "<tr><th colspan='6' style='border: 1pt solid #000000; font-size: 16pt; font-weight: bold; text-align: center; height: 40px; vertical-align: middle;'>LAPORAN $judul</th></tr>";
        echo "<tr><th colspan='6' style='border: 1pt solid #000000; font-size: 12pt; text-align: center; height: 25px; vertical-align: middle;'>Sistem Pendukung Keputusan Metode TOPSIS</th></tr>";
        echo "<tr><th colspan='6' style='border: 1pt solid #000000; font-size: 12pt; text-align: center; height: 25px; vertical-align: middle;'>PT.Swadarma Griyasatya - Reward & Punishment Karyawan</th></tr>";
        
        echo "<tr><td colspan='6' style='border: 1pt solid #000000; background-color: #f8f9fa; font-weight: bold; font-size: 11pt; height: 35px; vertical-align: middle;'>  Periode Penilaian: $periodeText</td></tr>";
        
        echo "<tr style='background-color: #e9ecef; font-weight: bold; font-size: 11pt;'>";
        echo "<th style='width: 100pt; border: 1pt solid #000000; height: 35px; vertical-align: middle;'>Peringkat</th>";
        echo "<th style='width: 200pt; border: 1pt solid #000000; vertical-align: middle;'>NIK</th>";
        echo "<th style='width: 500pt; border: 1pt solid #000000; vertical-align: middle;'>Nama Karyawan</th>";
        echo "<th style='width: 300pt; border: 1pt solid #000000; vertical-align: middle;'>Divisi</th>";
        echo "<th style='width: 150pt; border: 1pt solid #000000; vertical-align: middle;'>Nilai Preferensi (V)</th>";
        echo "<th style='width: 150pt; border: 1pt solid #000000; vertical-align: middle;'>Keputusan</th>";
        echo "</tr>";
        
        $rank = 1;
        foreach ($ranking as $r) {
            $k = current(array_filter($data['karyawan'], fn($x)=>$x['id']==$r['id_karyawan']));
            if (!$k) continue;
            
            $status = '-';
            $bgColor = "";
            if ($tipe === 'reward' && $rank <= 3) {
                $status = 'REWARD';
                $bgColor = "background-color: #d4edda;";
            } elseif ($tipe === 'punishment' && $rank <= 3) {
                $status = 'PUNISHMENT';
                $bgColor = "background-color: #f8d7da;";
            }
            
            $nilaiFormat = number_format($r['nilai'], 4, '.', '');
            
            echo "<tr style='$bgColor'>";
            echo "<td style='border: 1pt solid #000000; text-align: center;'>$rank</td>";
            echo "<td style='border: 1pt solid #000000;'>" . htmlspecialchars($k['nama']) . "</td>";
            echo "<td style='border: 1pt solid #000000;'>" . htmlspecialchars($k['divisi']) . "</td>";
            echo "<td style='border: 1pt solid #000000; text-align: center;'>$nilaiFormat</td>";
            echo "<td style='border: 1pt solid #000000; text-align: center; font-weight: bold;'>$status</td>";
            echo "</tr>";
            $rank++;
        }
        echo "</table>";
        
        // Tanda tangan
        $tanggalCetak = $this->formatTanggalIndo(date('Y-m-d'));
        echo "<br><br>";
        echo "<table border='0' style='width: 100%;'>";
        echo "<tr>";
        echo "<td colspan='4' style='border: none;'></td>";
        echo "<td colspan='2' style='border: none; text-align: center; font-size: 11pt;'>";
        echo "Jakarta, " . $tanggalCetak . "<br>";
        echo "Mengetahui,<br>";
        echo "Direktur PT.Swadarma Griyasatya<br><br><br><br><br>";
        echo "<strong>( .................................... )</strong>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        
        echo "</body>";
        echo "</html>";
        
        exit;
    }

    /**
     * Export ke PDF dengan Dompdf (jika ada) atau fallback print
     */
    public function exportPdf(string $tipe): void {
        $data = $_SESSION['hasil_' . $tipe] ?? null;
        if (!$data) die('Tidak ada data.');
        
        $html = $this->generatePdfHtml($tipe, $data);
        
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            if (class_exists('Dompdf\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $dompdf->stream("laporan_{$tipe}_".date('Ymd_His').".pdf", ['Attachment' => true]);
                exit;
            }
        }
        // Fallback jika Dompdf tidak ada
        echo $html;
        echo "<script>window.onload = function() { window.print(); }</script>";
        exit;
    }

    private function generatePdfHtml(string $tipe, array $data): string {
        $title = strtoupper($tipe);
        $judul = ($tipe == 'reward') ? 'REWARD (3 Karyawan Terbaik)' : 'PUNISHMENT (3 Karyawan Terendah)';
        
        if ($tipe == 'reward') {
            $mulai = isset($data['periode_mulai']) ? date('d F Y', strtotime($data['periode_mulai'])) : '-';
            $akhir = isset($data['periode_akhir']) ? date('d F Y', strtotime($data['periode_akhir'])) : '-';
            $periodeText = "$mulai s/d $akhir";
            $keterangan = "Reward diberikan kepada 3 karyawan dengan nilai preferensi TERTINGGI berdasarkan perhitungan TOPSIS selama 6 bulan terakhir.";
        } else {
            $periodeText = isset($data['periode']) ? date('F Y', strtotime($data['periode'])) : '-';
            $keterangan = "Punishment diberikan kepada 3 karyawan dengan nilai preferensi TERENDAH berdasarkan perhitungan TOPSIS pada bulan tersebut.";
        }
        
        $ranking = $data['ranking'];
        $total = count($ranking);
        
        if ($tipe === 'punishment') {
            usort($ranking, function($a, $b) {
                return $a['nilai'] <=> $b['nilai'];
            });
        }
        
        $rows = '';
        $rank = 1;
        foreach ($ranking as $r) {
            $k = current(array_filter($data['karyawan'], fn($x)=>$x['id']==$r['id_karyawan']));
            if (!$k) continue;
            
            $status = '-';
            if ($tipe === 'reward' && $rank <= 3) {
                $status = 'REWARD';
            } elseif ($tipe === 'punishment' && $rank <= 3) {
                $status = 'PUNISHMENT';
            }
            
            $rowClass = ($status == 'REWARD') ? 'reward-row' : (($status == 'PUNISHMENT') ? 'punish-row' : '');
            $nilaiFormatted = number_format((float)$r['nilai'], 4, '.', '');
            $rows .= "<tr class='$rowClass'>
                        <td style='text-align:center; width:10%;'>$rank</td>
                        <td style='width:25%;'>" . htmlspecialchars($k['nama'] ?? '') . "</td>
                        <td style='width:25%;'>" . htmlspecialchars($k['divisi'] ?? '') . "</td>
                        <td style='text-align:center; width:20%;'>$nilaiFormatted</td>
                        <td style='text-align:center; width:20%;'><strong>$status</strong></td>
                       </tr>";
            $rank++;
        }
        
        $tanggalCetak = $this->formatTanggalIndo(date('Y-m-d'));
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan $title - SPK TOPSIS</title>
    <style>
        @page {
            size: landscape;
            margin: 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            color: #2c3e50;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0 0 5px 0;
            font-size: 18pt;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
            color: #555;
        }
        .periode-box {
            background: #f8f9fa;
            padding: 6px 12px;
            border-left: 4px solid #3498db;
            margin: 15px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px 6px;
            vertical-align: middle;
        }
        th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }
        .reward-row {
            background-color: #d4edda;
        }
        .punish-row {
            background-color: #f8d7da;
        }
        .footer {
            margin-top: 30px;
            font-size: 8pt;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }
        .keterangan {
            margin-top: 20px;
            padding: 8px;
            background: #f1f3f5;
            border-radius: 4px;
            font-size: 9pt;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN $judul</h1>
        <p>Sistem Pendukung Keputusan Metode TOPSIS</p>
        <p>PT.Swadarma Griyasatya - Reward & Punishment Karyawan</p>
    </div>
    <div class="periode-box">
        <strong>Periode Penilaian:</strong> $periodeText
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Divisi</th>
                <th>Nilai Preferensi (V)</th>
                <th>Keputusan</th>
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>
    </table>
    
    <div class="keterangan">
        <strong>Keterangan:</strong> $keterangan
    </div>
    
    <table style="width: 100%; margin-top: 50px; border: none;">
        <tr>
            <td style="width: 65%; border: none;"></td>
            <td style="width: 35%; text-align: center; border: none;">
                Jakarta, $tanggalCetak<br>
                Mengetahui,<br>
                Direktur PT.Swadarma Griyasatya<br><br><br><br><br>
                <strong>( .................................... )</strong>
            </td>
        </tr>
    </table>
    
    <div class="footer">
        Dicetak pada: <?= date('d/m/Y H:i:s') ?> | &copy; SPK TOPSIS
    </div>
</body>
</html>
HTML;
        $html = str_replace('<?= date(\'d/m/Y H:i:s\') ?>', date('d/m/Y H:i:s'), $html);
        return $html;
    }
}
?>