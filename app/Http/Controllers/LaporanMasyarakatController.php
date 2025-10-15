<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\LaporanMasyarakat;
use App\Models\LaporanStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\SmsService;

class LaporanMasyarakatController extends Controller
{
    /**
     * Tampilkan form publik
     */
    public function create()
    {
        $form = Form::where('slug', 'pengaduan_laporan')->firstOrFail();
        $fields = $form->fields;

        return view('laporan.create', compact('form', 'fields'));
    }

    /**
     * Kirim laporan + OTP
     */
    public function sendOtp(Request $request)
    {
        $form = Form::where('slug', 'pengaduan_laporan')->firstOrFail();
        $fields = $form->fields;

        $data = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'file' && $request->hasFile($name)) {
                $uploaded = $request->file($name);
                if ($uploaded->getSize() > 1024 * 1024) {
                    return back()->withErrors([$name => 'Ukuran file terlalu besar (maksimal 1MB).'])->withInput();
                }
                $data[$name] = [
                    'name' => $uploaded->getClientOriginalName(),
                    'mime' => $uploaded->getMimeType(),
                    'size' => $uploaded->getSize(),
                    'data' => 'data:' . $uploaded->getClientMimeType() . ';base64,' . base64_encode(file_get_contents($uploaded->getRealPath())),
                ];
            } else {
                $data[$name] = $request->input($name);
            }
        }

        $phone = $data['phone'] ?? $request->input('phone');
        if (!$phone) {
            return back()->withErrors(['phone' => 'Nomor WA wajib diisi.'])->withInput();
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $otpHash = hash_hmac('sha256', (string)$otp, config('app.key'));
        $otpId = (string) Str::uuid();

        // Simpan OTP di cache beserta data laporan sementara
        Cache::put("otp_verif:{$otpId}", [
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'laporan_data' => $data, // simpan data laporan sementara
            'form_id' => $form->_id,
            'attempts' => 0,
            'resend_count' => 0,
            'created_at' => now(),
        ], now()->addMinutes(10));

        Log::info("OTP Entry:", Cache::get("otp_verif:{$otpId}"));

        // Kirim OTP via WhatsApp / SMS
        SmsService::send($phone, "Kode OTP verifikasi laporan Anda: {$otp}. Berlaku 10 menit.");

        return redirect()->route('laporan.verifyForm', ['otp_id' => $otpId])
                         ->with('success', 'Kode OTP telah dikirim ke nomor WA Anda.');
    }

    /**
     * Tampilkan form verifikasi OTP
     */
    public function showVerifyForm(Request $request)
    {
        $otp_id = $request->query('otp_id');

        if (!$otp_id || !Cache::has("otp_verif:{$otp_id}")) {
            return redirect()->route('laporan.create')
                             ->withErrors(['otp' => 'Token verifikasi tidak ditemukan atau sudah kadaluarsa.']);
        }

        return view('laporan.verify', compact('otp_id'));
    }

    /**
     * Verifikasi OTP
     */
        public function verifyOtp(Request $request)
{
    $request->validate([
        'otp_id' => 'required',
        'otp' => 'required', // panjang dicek manual
    ]);

    $otpId = $request->input('otp_id');
    $cacheKey = "otp_verif:{$otpId}";
    $entry = Cache::get($cacheKey);

    if (!$entry) {
        return back()->with('error', '❌ OTP tidak ditemukan atau sudah kadaluarsa.');
    }

    $otpInput = $request->input('otp');

    // Cek panjang dan format OTP
    if (!ctype_digit($otpInput) || strlen($otpInput) !== 6) {
        $entry['attempts'] = ($entry['attempts'] ?? 0) + 1;
        if ($entry['attempts'] >= 5) {
            Cache::forget($cacheKey);
            return back()->with('error', '❌ Terlalu banyak percobaan. Laporan Anda dibatalkan.');
        }
        Cache::put($cacheKey, $entry, now()->addMinutes(10));
        return back()->with('error', '❌ Kode OTP harus 6 digit angka.');
    }

    // Hash input OTP
    $providedHash = hash_hmac('sha256', $otpInput, config('app.key'));

    if (!hash_equals($entry['otp_hash'], $providedHash)) {
        $entry['attempts'] = ($entry['attempts'] ?? 0) + 1;
        if ($entry['attempts'] >= 5) {
            Cache::forget($cacheKey);
            return back()->with('error', '❌ Terlalu banyak percobaan. Laporan Anda dibatalkan.');
        }
        Cache::put($cacheKey, $entry, now()->addMinutes(10));
        return back()->with('error', '❌ Kode OTP salah.');
    }

    // ✅ OTP valid → simpan laporan ke DB
    $laporan = LaporanMasyarakat::create([
        'form_id' => $entry['form_id'],
        'data' => $entry['laporan_data'],
        'status_id' => 1,
        'verified_at' => now(),
        'tracking_code' => substr(md5(json_encode($entry['laporan_data']) . now()), 0, 8),
    ]);

    // Simpan riwayat status
    LaporanStatusHistory::create([
        'laporan_id' => $laporan->_id,
        'status_id' => 1,
        'changed_at' => now(),
    ]);

    $trackingCode = $laporan->tracking_code;
    $laporanId = $laporan->_id;
    
    $laporanData = $entry['laporan_data'] ?? [];
if (is_string($laporanData)) {
    $laporanData = json_decode($laporanData, true) ?? [];
}

$namaPelapor = $laporanData['nama_pelapor']
    ?? $laporanData['nama']
    ?? $laporanData['name']
    ?? 'Pelapor';

$isiLaporan  = $laporanData['deskripsi_laporan']
    ?? $laporanData['laporan']
    ?? $laporanData['deskripsi']
    ?? '(detail laporan tidak tersedia)';

    // Kirim WhatsApp lengkap
    $message = "✅ Terima kasih, *{$namaPelapor}*!\n".
            "Laporan Anda telah berhasil diverifikasi dan tercatat dalam sistem.\n\n".
            "📄 *Detail laporan singkat:*\n{$isiLaporan}\n\n".
            "🔍 *Kode Pelacakan:* {$trackingCode}\n".
            "Gunakan kode ini untuk memantau status laporan Anda.";

    SmsService::send($entry['phone'], $message);

    // Hapus cache setelah ambil data
    Cache::forget($cacheKey);

    // Kirim konfirmasi WA
    
    // Kirim data ke view
    return view('laporan.success', compact('laporanId', 'trackingCode'))
           ->with('success', '✅ OTP berhasil diverifikasi dan laporan telah disimpan.');
}
    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate(['otp_id' => 'required']);
        $otpId = $request->input('otp_id');
        $cacheKey = "otp_verif:{$otpId}";
        $entry = Cache::get($cacheKey);

        if (!$entry) {
            return back()->withErrors(['otp' => 'Proses verifikasi tidak ditemukan atau sudah kadaluarsa.']);
        }

        if (($entry['resend_count'] ?? 0) >= 3) {
            return back()->withErrors(['otp' => 'Batas resend tercapai. Silakan kirim ulang laporan.']);
        }

        $otp = rand(100000, 999999);
        $entry['otp_hash'] = hash_hmac('sha256', (string)$otp, config('app.key'));
        $entry['resend_count'] += 1;
        Cache::put($cacheKey, $entry, now()->addMinutes(10));

        SmsService::send($entry['phone'], "Kode OTP baru: {$otp}. Berlaku 10 menit.");
        Log::info("Resent OTP untuk {$entry['phone']}: {$otp} (otpId={$otpId})");

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }
}
