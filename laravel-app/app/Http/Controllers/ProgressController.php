<?php

namespace App\Http\Controllers;

use App\Support\Progress\ProgressReporter;
use Illuminate\Http\JsonResponse;

/**
 * خواندن درصد پیشرفت یک عملیات سنگین برای لودر سراسری.
 *
 * این مسیر عمداً در گروه api (بدون سشن) ثبت شده است تا پولینگ مرورگر در حین
 * اجرای عملیات سنگین پشت قفل سشن نماند و پاسخی بلافاصله بگیرد.
 * امنیت با توکن تصادفی ۳۲ کاراکتری تأمین می‌شود که فقط خود مرورگر می‌داند.
 */
class ProgressController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $data = ProgressReporter::read($token);

        if ($data === null) {
            // هنوز چیزی گزارش نشده؛ مرورگر با همین پاسخ، حالت نامعین را نگه می‌دارد.
            return $this->json(['state' => 'idle']);
        }

        return $this->json($data);
    }

    private function json(array $data): JsonResponse
    {
        return response()->json($data, 200, [
            'Cache-Control' => 'no-store, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
