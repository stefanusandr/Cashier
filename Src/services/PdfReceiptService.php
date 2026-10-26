<?php
namespace SmartKasir\Services;

use SmartKasir\Models\Order;

/**
 * Class PdfReceiptService
 * Menghasilkan file struk belanja dalam format PDF digital (Sprint 2 - SKPL-F07).
 * Sesuai spesifikasi DPPL 3.2.
 */
class PdfReceiptService
{
    private string $storageDir;

    public function __construct(?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? __DIR__ . '/../../public/receipts';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
    }

    /**
     * Mengisi template struk belanja dan mengonversi menjadi file PDF (SKPL-F07).
     *
     * @param string $orderId
     * @return string|null Path file PDF yang dihasilkan
     */
    public function generateReceiptPdf(string $orderId): ?string
    {
        $order = Order::findById($orderId);
        if (!$order) {
            return null;
        }

        $data = $this->formatReceiptData($order);
        $fileName = "receipt_{$orderId}.pdf";
        $filePath = $this->storageDir . '/' . $fileName;

        // Skeleton/stub implementation untuk Sprint 2
        // Akan mengintegrasikan FPDF / TCPDF / Dompdf pada Sprint 2
        return $filePath;
    }

    /**
     * Mempersiapkan data transaksi terformat untuk dicetak ke template struk
     */
    public function formatReceiptData(Order $order): array
    {
        $user = $order->getUser();
        $store = $user ? $user->getStore() : null;

        return [
            'store_name' => $store ? $store->getStoreName() : 'SmartKasir POS',
            'store_address' => $store ? $store->getAddress() : 'Surabaya',
            'store_phone' => $store ? $store->getPhone() : '-',
            'order_id' => $order->getOrderId(),
            'order_date' => $order->getOrderDate(),
            'cashier_name' => $user ? $user->getFullName() : 'Kasir',
            'items' => $order->toArray()['items'],
            'total_amount' => $order->getTotalAmount(),
            'paid_amount' => $order->getPaidAmount(),
            'change_amount' => $order->getChangeAmount(),
        ];
    }
}
