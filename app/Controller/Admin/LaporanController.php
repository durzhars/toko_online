<?php

namespace App\Controller\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Models\Transaksi;

class LaporanController extends Controller
{
    private Transaksi $transaksiModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->transaksiModel = new Transaksi();
    }

    public function index(): void
    {
        $startDate = $this->request->input('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $this->request->input('end_date', date('Y-m-d'));
        $status = $this->request->input('status', '');

        $laporan = $this->transaksiModel->getLaporan($startDate, $endDate, $status);

        $totalPendapatan = 0;

        foreach ($laporan as $row) {
            if (in_array($row['status'], ['PAID', 'SHIPPED', 'COMPLETED'])) {
                $totalPendapatan += $row['total_tagihan'];
            }
        }

        $this->view('admin/laporan')
            ->layout('admin')
            ->title('Laporan Penjualan')
            ->with([
                'laporan' => $laporan,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'status' => $status,
                'totalPendapatan' => $totalPendapatan,
            ])
            ->render();
    }

    public function exportCsv()
    {
        $startDate = $this->request->input('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $this->request->input('end_date', date('Y-m-d'));
        $status = $this->request->input('status', '');

        $laporan = $this->transaksiModel->getLaporan($startDate, $endDate, $status);

        $fileName = "Laporan_Penjualan_{$startDate}_sampai{$endDate}.csv";

        // Server Side Output Buffering
        ob_start();
        $output = fopen('php://output', 'w');

        fputcsv($output, ['ID Transaksi', 'Tanggal', 'Nama Pelanggan', 'Status', 'Total Tagihan']);

        foreach ($laporan as $row) {
            fputcsv($output, [
                $row['id'],
                $row['created_at'],
                $row['nama_pelanggan'],
                $row['status'],
                $row['total_tagihan'],
            ]);
        }

        fclose($output);

        // Tangkap buffered output, lalu bersihkan buffer.
        $csvContent = ob_get_clean();

        $response = new Response($csvContent);
        $response->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setStatusCode(Response::HTTP_OK)
            ->send();
    }
}
