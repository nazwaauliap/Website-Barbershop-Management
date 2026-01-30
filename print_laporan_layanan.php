<?php
require 'config/config.php';

// Ambil parameter tanggal dari URL
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

$filter_query = " AND t.Tanggal_Transaksi BETWEEN '$tgl_awal' AND '$tgl_akhir'";

// Query data
$q = mysqli_query($conn,"
    SELECT l.Nama_Layanan, 
           COUNT(dl.ID_Detail_Layanan) AS Jumlah_Terjual, 
           SUM(dl.Subtotal) AS Total_Pendapatan
    FROM detail_layanan dl
    JOIN layanan l ON dl.ID_Layanan = l.ID_Layanan
    JOIN transaksi t ON dl.ID_Transaksi = t.ID_Transaksi
    WHERE 1=1 $filter_query
    GROUP BY dl.ID_Layanan
    ORDER BY Total_Pendapatan DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Layanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        .periode {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-row {
            background-color: #E7E6E6;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            body {
                margin: 15mm;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <h2>REKAP LAYANAN</h2>
    <div class="periode">Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s/d <?= date('d-m-Y', strtotime($tgl_akhir)) ?></div>
    
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="40%">Layanan</th>
                <th width="20%">Jumlah Terjual</th>
                <th width="30%">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_keseluruhan = 0;
            while($row = mysqli_fetch_array($q)):
                $total_keseluruhan += $row['Total_Pendapatan'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['Nama_Layanan'] ?></td>
                <td><?= $row['Jumlah_Terjual'] ?></td>
                <td>Rp <?= number_format($row['Total_Pendapatan'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL:</td>
                <td>Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>