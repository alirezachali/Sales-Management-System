<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Models\WarehouseQRCode;
use Illuminate\Support\Facades\Storage;

/**
 * سرویس تولید و مدیریت QR Code برای واحدهای انبار
 */
class QRCodeService
{
    /**
     * ساخت QR Code جدید و ذخیره تصویر آن
     */
    public function generate(WarehouseQRCode $qrCode): string
    {
        $data = $this->buildQrData($qrCode);
        $filename = "qrcodes/{$qrCode->qr_identifier}.svg";

        // تولید تصویر QR با simple-qrcode
        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
            ->format('svg')
            ->generate($data);

        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }

    /**
     * ساخت داده JSON برای QR Code
     */
    private function buildQrData(WarehouseQRCode $qrCode): string
    {
        $data = [
            'id' => $qrCode->qr_identifier,
            'wh' => $qrCode->warehouse?->name ?? '',
            'unit' => $qrCode->unit_type,
            'qty' => $qrCode->quantity_in_unit,
            'entry' => $qrCode->entry_date?->format('Y/m/d') ?? '',
            'prod' => $qrCode->production_date?->format('Y/m/d') ?? '',
            'exp' => $qrCode->expiration_date?->format('Y/m/d') ?? '',
            'sec' => $qrCode->location_section ?? '',
            'shlf' => $qrCode->location_shelf ?? '',
            'row' => $qrCode->location_row ?? '',
            'pos' => $qrCode->location_position ?? '',
            'barcodes' => $qrCode->products_barcode ?? [],
            'note' => $qrCode->notes ?? '',
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * بازسازی QR Code (حذف و ساخت مجدد)
     */
    public function regenerate(WarehouseQRCode $qrCode): string
    {
        // حذف تصویر قبلی
        if ($qrCode->qr_image_path) {
            Storage::disk('public')->delete($qrCode->qr_image_path);
        }

        return $this->generate($qrCode);
    }

    /**
     * حذف تصویر QR Code
     */
    public function delete(WarehouseQRCode $qrCode): void
    {
        if ($qrCode->qr_image_path) {
            Storage::disk('public')->delete($qrCode->qr_image_path);
        }
    }

    /**
     * ساخت لینک دانلود SVG QR Code
     */
    public function getDownloadUrl(WarehouseQRCode $qrCode): ?string
    {
        if (! $qrCode->qr_image_path) {
            return null;
        }

        return route('warehouse-qr.download', ['qr' => $qrCode->qr_identifier]);
    }
}