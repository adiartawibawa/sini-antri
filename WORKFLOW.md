# BLUEPRINT — Aplikasi Antrian Digital Laravel 12

> Baca seluruh dokumen sebelum menulis kode apapun.
> Ikuti semua keputusan arsitektur, konvensi penamaan, dan alur kerja yang tercantum di sini.

---

## DAFTAR ISI

1. [Ringkasan Sistem](#1-ringkasan-sistem)
2. [Stack Teknologi](#2-stack-teknologi)
3. [Struktur Direktori](#3-struktur-direktori)
4. [Skema Database](#4-skema-database)
5. [Model & Relasi](#5-model--relasi)
6. [Arsitektur Broadcasting](#6-arsitektur-broadcasting)
7. [Routing](#7-routing)
8. [Controllers](#8-controllers)
9. [Alur Kerja Lengkap](#9-alur-kerja-lengkap)
10. [Views & Frontend](#10-views--frontend)
11. [Autentikasi](#11-autentikasi)
12. [Konfigurasi Environment](#12-konfigurasi-environment)
13. [Perintah Artisan & Scheduler](#13-perintah-artisan--scheduler)
14. [Aturan Koding](#14-aturan-koding)
15. [Urutan Implementasi](#15-urutan-implementasi)

---

## 1. RINGKASAN SISTEM

Aplikasi antrian digital real-time dengan tiga sisi yang terhubung via WebSocket (Laravel Reverb):

| Sisi           | Akses                     | Fungsi Utama                             |
| -------------- | ------------------------- | ---------------------------------------- |
| **Pengunjung** | Smartphone via QR Code    | Ambil nomor antrian, pantau status tiket |
| **Operator**   | Browser desktop per loket | Panggil, skip, complete antrian          |
| **Display**    | Browser TV/monitor        | Tampilkan nomor + TTS otomatis           |

### Prinsip Desain

- **Resource minimal** — tidak ada package berlebih, tidak ada abstraksi tidak perlu
- **Zero-install pengunjung** — hanya browser, tidak ada app native
- **Real-time via Reverb** — self-hosted, tidak bergantung layanan eksternal berbayar
- **FIFO ketat** — antrian diproses berdasarkan `queue_order` ascending
- **Multi-operator** — setiap operator punya loket sendiri, sesi independen

---

## 2. STACK TEKNOLOGI

```
PHP          >= 8.1
Laravel      ^12.0
Laravel Reverb ^1.10         # WebSocket server (self-hosted)
simple-qrcode ^4.2           # Generate QR Code
MariaDB                      # Database
Vite + laravel-echo          # Frontend bundler + WebSocket client
pusher-js    ^8.4            # Transport layer untuk Echo (wajib meski pakai Reverb)
Livewire
```

### Package yang TIDAK digunakan

```
# JANGAN tambahkan tanpa instruksi eksplisit:
- pusher/pusher-php-server   ← digantikan Reverb
- laravel/sanctum            ← tidak perlu, auth pakai session guard
- laravel/horizon            ← overkill untuk skala ini
```

---

## 3. STRUKTUR DIREKTORI

```
app/
├── Console/
│   └── Commands/
│       └── ResetDailyQueue.php         # Artisan command reset harian
├── Events/
│   ├── QueueCreated.php                # Pengunjung ambil nomor
│   ├── QueueCalled.php                 # Operator panggil nomor
│   └── QueueStatusChanged.php         # Skip / complete / status lain
├── Http/
│   └── Controllers/
│       ├── AuthController.php          # Login / logout operator
│       ├── DisplayController.php       # Layar TV + QR Code
│       ├── OperatorController.php      # Dashboard + aksi antrian
│       └── VisitorController.php       # Form registrasi + tiket
└── Models/
    ├── Antrian.php                     # ⚠️ BUKAN Queue.php (konflik namespace)
    ├── User.php
    └── QueueSetting.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_queue_settings_table.php
│   ├── 2024_01_01_000002_create_users_table.php
│   └── 2024_01_01_000003_create_queues_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── js/
│   └── echo.js                         # Konfigurasi Laravel Echo + Reverb
└── views/
    ├── display/
    │   ├── qrcode.blade.php            # Halaman cetak QR Code
    │   └── screen.blade.php            # Layar TV utama
    ├── operator/
    │   ├── dashboard.blade.php         # Control center operator
    │   └── login.blade.php
    └── visitor/
        ├── register.blade.php          # Form ambil antrian
        └── ticket.blade.php            # Tiket digital pengunjung

routes/
├── channels.php                        # Otorisasi channel broadcast
├── console.php                         # Scheduler (Laravel 12)
└── web.php

bootstrap/
└── app.php                             # ⚠️ Laravel 12: tidak ada Http/Kernel.php
```

> **PERINGATAN NAMING:** Model antrian wajib bernama `Antrian` (bukan `Queue`).
> `App\Models\Queue` konflik dengan `Illuminate\Queue\Queue` di Laravel 12.

---

## 4. SKEMA DATABASE

### Tabel: `queue_settings`

```sql
id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
prefix                VARCHAR(5)      NOT NULL DEFAULT 'A'
avg_service_minutes   INT             NOT NULL DEFAULT 5
reset_daily           BOOLEAN         NOT NULL DEFAULT TRUE
current_counter       INT             NOT NULL DEFAULT 0
last_reset_date       DATE            NULL
created_at            TIMESTAMP       NULL
updated_at            TIMESTAMP       NULL
```

### Tabel: `users`

```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
name            VARCHAR(255)    NOT NULL
email           VARCHAR(255)    NOT NULL UNIQUE
password        VARCHAR(255)    NOT NULL
loket_name      VARCHAR(255)    NOT NULL DEFAULT 'Loket 1'
is_active       BOOLEAN         NOT NULL DEFAULT TRUE
is_operator     BOOLEAN         NOT NULL DEFAULT FALSE
remember_token  VARCHAR(100)    NULL
created_at      TIMESTAMP       NULL
updated_at      TIMESTAMP       NULL
deleted_at      TIMESTAMP       NULL
```

<!-- is_operator digunakan untuk penanda sebagai admin (FALSE) atau sebagai operator (TRUE) -->

### Tabel: `queues`

```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
uuid            CHAR(36)        NOT NULL UNIQUE          -- token URL tiket pengunjung
queue_number    VARCHAR(10)     NOT NULL                 -- contoh: A001, B042
queue_order     INT             NOT NULL                 -- urutan numerik untuk sorting FIFO
visitor_name    VARCHAR(100)    NOT NULL
purpose         VARCHAR(255)    NULL
status          ENUM(
                  'waiting',    -- menunggu dipanggil
                  'called',     -- sudah dipanggil, belum dilayani
                  'serving',    -- sedang dilayani (opsional, bisa skip langsung complete)
                  'completed',  -- selesai dilayani
                  'skipped'     -- dilewati karena tidak hadir
                ) NOT NULL DEFAULT 'waiting'
operator_id     BIGINT UNSIGNED NULL   REFERENCES users(id) ON DELETE SET NULL
called_at       TIMESTAMP       NULL
served_at       TIMESTAMP       NULL
completed_at    TIMESTAMP       NULL
created_at      TIMESTAMP       NULL
updated_at      TIMESTAMP       NULL

INDEX idx_status_order  (status, queue_order)
INDEX idx_uuid          (uuid)
```

### Seed Data Default

```
operators:
  loket1@antrian.test / password  → Loket 1
  loket2@antrian.test / password  → Loket 2
  loket3@antrian.test / password  → Loket 3

queue_settings:
  prefix = 'A', avg_service_minutes = 5, reset_daily = true
```

---

## 5. MODEL & RELASI

### `App\Models\Antrian`

```php
// Atribut yang bisa diisi massal
$fillable = [
    'uuid', 'queue_number', 'queue_order',
    'visitor_name', 'purpose', 'status',
    'operator_id', 'called_at', 'served_at', 'completed_at',
];

// Cast
$casts = [
    'called_at'    => 'datetime',
    'served_at'    => 'datetime',
    'completed_at' => 'datetime',
];

// Auto-generate UUID saat creating
protected static function boot(): void {
    parent::boot();
    static::creating(fn($m) => $m->uuid ??= (string) Str::uuid());
}

// Relasi
public function operator(): BelongsTo  // → Operator

// Accessor
public function getPositionAheadAttribute(): int
// Hitung antrian waiting dengan queue_order < queue_order milik record ini

public function getEstimatedWaitMinutesAttribute(): int
// position_ahead × avg_service_minutes dari QueueSetting

// Scope
public function scopeWaiting($query)
// WHERE status = 'waiting' ORDER BY queue_order ASC

public function scopeActive($query)
// WHERE status IN ('waiting', 'called')
```

### `App\Models\User`

```php
// Extends: Illuminate\Foundation\Auth\User as Authenticatable
// Guard: 'operator'

$fillable  = ['name', 'email', 'password', 'loket_name', 'is_active', 'is_operator'];
$hidden    = ['password', 'remember_token'];
$casts     = ['is_active' => 'boolean', 'is_operator' => 'boolean', 'password' => 'hashed'];

// Relasi
public function queues(): HasMany          // → Antrian
public function activeQueue(): HasOne      // → Antrian WHERE status IN ('called', 'serving')
```

### `App\Models\QueueSetting`

```php
$fillable = [
    'prefix', 'avg_service_minutes', 'reset_daily',
    'current_counter', 'last_reset_date',
];

$casts = ['reset_daily' => 'boolean', 'last_reset_date' => 'date'];

// Method kunci
public function generateNextNumber(): string
// 1. Jika reset_daily=true dan last_reset_date bukan hari ini → reset counter ke 0
// 2. Increment current_counter
// 3. Save
// 4. Return prefix + str_pad(current_counter, 3, '0', STR_PAD_LEFT)
// Contoh: 'A' + '007' = 'A007'
```

---

## 6. ARSITEKTUR BROADCASTING

### Driver

```
BROADCAST_CONNECTION=reverb
```

### Event → Channel Mapping

| Event                | Channel yang Menerima | Payload Utama                                                |
| -------------------- | --------------------- | ------------------------------------------------------------ |
| `QueueCreated`       | `operator-dashboard`  | id, uuid, queue_number, visitor_name, purpose, waiting_count |
| `QueueCalled`        | `display-screen`      | queue_number, visitor_name, loket_name, called_at            |
| `QueueCalled`        | `ticket.{uuid}`       | uuid, queue_number, loket_name, status                       |
| `QueueCalled`        | `operator-dashboard`  | queue_number, loket_name, status                             |
| `QueueStatusChanged` | `ticket.{uuid}`       | uuid, status, previous_status, waiting_count                 |
| `QueueStatusChanged` | `operator-dashboard`  | id, status, waiting_count                                    |

### Semua Channel Bersifat Publik

```php
// routes/channels.php
Broadcast::channel('operator-dashboard', fn() => true);
Broadcast::channel('display-screen',     fn() => true);
Broadcast::channel('ticket.{uuid}',      fn($user, $uuid) =>
    Antrian::where('uuid', $uuid)->exists()
);
```

> Channel `ticket.{uuid}` tidak butuh user login — validasi cukup dengan keberadaan UUID di database.

### Konfigurasi Reverb (`config/broadcasting.php`)

```php
'default' => env('BROADCAST_CONNECTION', 'reverb'),

'connections' => [
    'reverb' => [
        'driver'  => 'reverb',
        'key'     => env('REVERB_APP_KEY'),
        'secret'  => env('REVERB_APP_SECRET'),
        'app_id'  => env('REVERB_APP_ID'),
        'options' => [
            'host'   => env('REVERB_HOST', 'localhost'),
            'port'   => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
            'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
        ],
    ],
],
```

### Inisialisasi Echo di Frontend (`resources/js/echo.js`)

```js
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? "localhost",
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
    enabledTransports: ["ws", "wss"],
    disableStats: true,
});
```

---

## 7. ROUTING

### `routes/web.php` — Semua Route

```
# ── PENGUNJUNG ───────────────────────────────────────────────────────────
GET  /visitor/{locationCode?}       VisitorController@register      visitor.register
POST /visitor/queue/take            VisitorController@takeQueue      visitor.take
GET  /ticket/{uuid}                 VisitorController@ticket         visitor.ticket
GET  /ticket/{uuid}/position        VisitorController@ticketPosition visitor.ticket.position

# ── AUTH OPERATOR ────────────────────────────────────────────────────────
GET  /login                         AuthController@showLogin         login          [guest:operator]
POST /login                         AuthController@login             login.post
POST /logout                        AuthController@logout            logout

# ── OPERATOR (butuh auth:operator) ───────────────────────────────────────
GET  /operator                      OperatorController@index         operator.dashboard
POST /operator/queue/call           OperatorController@call          operator.queue.call
POST /operator/queue/{antrian}/recall  OperatorController@recall     operator.queue.recall
POST /operator/queue/{antrian}/skip    OperatorController@skip       operator.queue.skip
POST /operator/queue/{antrian}/complete OperatorController@complete  operator.queue.complete
GET  /operator/queue/waiting        OperatorController@waitingList   operator.queue.waiting

# ── DISPLAY ──────────────────────────────────────────────────────────────
GET  /display                       DisplayController@index          display
GET  /display/status                DisplayController@status         display.status
GET  /admin/qrcode/{locationCode?}  DisplayController@qrcode         admin.qrcode
```

### `routes/channels.php`

Sudah dijelaskan di §6. Wajib didaftarkan di `bootstrap/app.php`:

```php
->withRouting(
    web      : __DIR__.'/../routes/web.php',
    channels : __DIR__.'/../routes/channels.php',
)
```

### `routes/console.php` (Laravel 12 — tanpa Kernel)

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:reset-daily')->dailyAt('00:01');
```

---

## 8. CONTROLLERS

### `VisitorController`

| Method                           | Route                       | Deskripsi                                                                       |
| -------------------------------- | --------------------------- | ------------------------------------------------------------------------------- |
| `register(string $locationCode)` | GET /visitor                | Tampilkan form + hitung waiting_count                                           |
| `takeQueue(Request $request)`    | POST /visitor/queue/take    | Validasi → generate nomor → simpan → broadcast QueueCreated → redirect ke tiket |
| `ticket(string $uuid)`           | GET /ticket/{uuid}          | Tampilkan tiket + hitung positionAhead + estimatedMinutes                       |
| `ticketPosition(string $uuid)`   | GET /ticket/{uuid}/position | JSON: position_ahead, estimated_minutes, status                                 |

**Logika `takeQueue`:**

```
1. Validasi: visitor_name required|string|max:100, purpose nullable|string|max:255
2. QueueSetting::firstOrCreate([]) dengan default dari env
3. $queueNumber = $setting->generateNextNumber()
4. $queueOrder  = $setting->current_counter
5. Antrian::create([...])
6. broadcast(new QueueCreated($antrian))->toOthers()
7. redirect()->route('visitor.ticket', $antrian->uuid)
```

### `OperatorController`

| Method                       | Deskripsi                                                                          | Response   |
| ---------------------------- | ---------------------------------------------------------------------------------- | ---------- |
| `index()`                    | Load waiting list + activeQueue milik operator ini                                 | View blade |
| `call()`                     | Ambil Antrian::waiting()->first() → update status='called' → broadcast QueueCalled | JSON       |
| `recall(Antrian $antrian)`   | Update called_at=now(), status='called' → broadcast QueueCalled                    | JSON       |
| `skip(Antrian $antrian)`     | Update status='skipped' → broadcast QueueStatusChanged                             | JSON       |
| `complete(Antrian $antrian)` | Update status='completed', completed_at=now() → broadcast QueueStatusChanged       | JSON       |
| `waitingList()`              | Return JSON list antrian waiting (fallback polling)                                | JSON       |

**Aturan akses:**

```
- recall/skip/complete: wajib cek $antrian->operator_id === Auth::id()
- call: tidak perlu cek — siapa pun operator boleh ambil antrian teratas
- Semua method dilindungi middleware auth:operator
```

**Response JSON sukses:**

```json
{
  "message"      : "Berhasil memanggil antrian.",
  "queue_number" : "A007",
  "loket_name"   : "Loket 1",
  "queue"        : { ...atribut Antrian... }
}
```

**Response JSON error:**

```json
{ "message": "Tidak ada antrian yang menunggu." }
```

HTTP status: 404 jika tidak ada antrian, 403 jika bukan milik operator.

### `AuthController`

```
showLogin() → view('operator.login')          [guard: guest:operator]
login()     → Auth::guard('operator')->attempt()
              sukses: redirect()->intended(route('operator.dashboard'))
              gagal:  back()->withErrors(['email' => '...'])
logout()    → Auth::guard('operator')->logout() → invalidate session → redirect /login
```

### `DisplayController`

```
index()    → Antrian dengan status called/serving terbaru + 5 waiting berikutnya → view
status()   → JSON: { current: {...}, next: [...] }
qrcode()   → Generate QR dari route('visitor.register', $locationCode) → view
```

---

## 9. ALUR KERJA LENGKAP

### 9.1 Pengunjung Ambil Nomor

```
Pengunjung scan QR Code
  → GET /visitor/{kode}
  → Tampil form (nama, keperluan) + info: jumlah menunggu, est. waktu
  → Submit form POST /visitor/queue/take
  → Backend:
      a. Validasi input
      b. generateNextNumber() → 'A007'
      c. INSERT queues (uuid=random, queue_number='A007', status='waiting')
      d. broadcast(QueueCreated) → channel: operator-dashboard
      e. redirect /ticket/{uuid}
  → Pengunjung lihat tiket digital (status: Menunggu)
  → Echo.channel('ticket.{uuid}') subscribe — live updates aktif
```

### 9.2 Operator Memanggil

```
Operator buka /operator (sudah login)
  → Lihat daftar antrian waiting (FIFO by queue_order)
  → Klik "Panggil"
  → POST /operator/queue/call (AJAX)
  → Backend:
      a. Antrian::waiting()->first() → ambil teratas
      b. UPDATE queues SET status='called', operator_id=X, called_at=now()
      c. broadcast(QueueCalled):
          - channel display-screen   → layar TV update + TTS
          - channel ticket.{uuid}    → HP pengunjung update
          - channel operator-dashboard → semua operator refresh list
  → Response JSON dengan data antrian
  → Dashboard operator: antrian teratas hilang dari list, box "sedang dilayani" muncul
```

### 9.3 Layar Display Menerima Panggilan

```
Display screen subscribe Echo.channel('display-screen')
  → Event QueueCalled masuk:
      a. Update DOM: tampilkan queue_number besar + loket_name
      b. Flash overlay layar (animasi)
      c. triggerTTS(queue_number, loket_name):
          - SpeechSynthesisUtterance dengan lang='id-ID'
          - Baca per karakter: "A... 0... 0... 7"
          - Ulangi setelah 5 detik
      d. Fetch /display/status → update daftar "antrian berikutnya"
```

### 9.4 HP Pengunjung Menerima Panggilan

```
Tiket pengunjung subscribe Echo.channel('ticket.{uuid}')
  → Event QueueCalled masuk (uuid cocok):
      a. Update status badge → "Silakan Menuju Loket!" (amber, blink)
      b. Tampilkan alert box dengan nama loket
      c. Update position_ahead → 0
      d. playNotificationSound() via AudioContext (nada pendek)
      e. navigator.vibrate([200,100,200])
```

### 9.5 Skip / Complete

```
Operator klik Skip atau Complete
  → POST /operator/queue/{id}/skip  atau  .../complete
  → Backend:
      a. Validasi operator_id === Auth::id()
      b. UPDATE status → 'skipped' atau 'completed'
      c. broadcast(QueueStatusChanged):
          - channel ticket.{uuid}      → HP pengunjung: status berubah
          - channel operator-dashboard → refresh waiting count
  → Operator dashboard: box "sedang dilayani" kosong, siap panggil berikutnya
```

### 9.6 Update Posisi Real-Time

```
Setiap kali QueueCalled di-broadcast:
  → Semua tiket pengunjung yang status='waiting' perlu refresh posisi
  → Caranya: Echo.channel('operator-dashboard').listen('QueueCalled', () => {
        if (status === 'waiting') fetch('/ticket/{uuid}/position')
    })
  → Response: { position_ahead: N, estimated_minutes: N×avg }
```

---

## 10. VIEWS & FRONTEND

### Pendekatan

- **Echo.js** di-load via `@vite(['resources/js/echo.js'])`

### `visitor/register.blade.php`

```
Konten: form 1 kolom (nama + keperluan)
Stats: waiting_count, avg_service_minutes, prefix antrian
Submit: disable tombol saat submit, tampil "Memproses..."
Responsif: max-width 420px, centered
```

### `visitor/ticket.blade.php`

```
Konten:
  - Nomor antrian besar (5.5rem, animasi pulse saat update)
  - Status badge (warna per status, blink jika 'called')
  - Alert box kuning jika dipanggil (muncul dengan animasi slideIn)
  - Grid 2 kolom: position_ahead + estimated_minutes
  - Info: nama, keperluan, waktu ambil, nama loket (muncul saat dipanggil)
  - Indikator live (dot hijau berkedip)

JavaScript wajib:
  - Echo.channel('ticket.{uuid}').listen('QueueCalled', ...)
  - Echo.channel('ticket.{uuid}').listen('QueueStatusChanged', ...)
  - Echo.channel('operator-dashboard').listen('QueueCalled', refreshPosition)
  - refreshPosition() → fetch('/ticket/{uuid}/position')
  - playNotificationSound() via AudioContext
  - vibrate() via navigator.vibrate
  - Polling fallback setiap 30 detik jika WebSocket disconnected
```

### `operator/dashboard.blade.php`

```
Layout: CSS Grid 2 kolom (daftar antrian kiri, panel aktif kanan)
Top bar: nama operator, loket badge, status koneksi Reverb, tombol logout

Panel kiri — daftar waiting:
  - List item per antrian: nomor | nama | keperluan | waktu
  - Item baru: animasi slideIn dari kiri
  - Item dipanggil: fade out + slide right

Panel kanan — antrian aktif:
  - Nomor besar
  - Nama + keperluan
  - 4 tombol: Panggil (full width), Recall, Skip, Complete
  - Recall/Skip/Complete: disabled jika tidak ada antrian aktif

Stats bar atas: Menunggu | Selesai Hari Ini | Dilewati

JavaScript wajib:
  - Echo.channel('operator-dashboard').listen('QueueCreated', addQueueItem)
  - Echo.channel('operator-dashboard').listen('QueueStatusChanged', removeQueueItem)
  - Monitor Echo.connector.pusher.connection untuk indikator koneksi
  - Semua aksi via fetch() → JSON response → update DOM
  - Toast notification (sukses/error) — animasi slide dari bawah
```

### `display/screen.blade.php`

```
Layout: dark theme (#0a0f1e), 2 kolom (nomor besar kiri, daftar kanan)
Header: nama sistem + jam digital real-time (update setiap detik)
Kolom kiri: nomor antrian sangat besar (font Bebas Neue), nama, loket badge
Kolom kanan: daftar 5 antrian berikutnya
Footer: ticker berjalan (animasi marquee)
Flash overlay: saat dipanggil, layar flash biru sebentar

JavaScript wajib:
  - Echo.channel('display-screen').listen('QueueCalled', updateDisplay)
  - triggerTTS(number, loket):
      window.speechSynthesis.cancel()
      SpeechSynthesisUtterance, lang='id-ID', rate=0.85
      Baca per karakter dengan jeda '... '
      Ulangi setelah 5 detik
  - setInterval(updateClock, 1000)
  - setInterval(updateNextList, 30000) — polling refresh daftar berikutnya
```

### `display/qrcode.blade.php`

```
Konten: QR Code SVG, URL teks, tombol cetak (window.print())
QR Config: ukuran 220px, gaya 'round', eye 'circle', warna biru
Tombol cetak hilang saat @media print
```

---

## 11. AUTENTIKASI

### Guard Custom (`config/auth.php`)

```php
'defaults' => ['guard' => 'operator', 'passwords' => 'operators'],

'guards' => [
    'operator' => ['driver' => 'session', 'provider' => 'operators'],
],

'providers' => [
    'operators' => ['driver' => 'eloquent', 'model' => App\Models\Operator::class],
],
```

> Default guard adalah `operator`. Panggilan `Auth::user()` langsung mengembalikan Operator.

### Middleware

```php
// Route operator dilindungi dengan:
->middleware('auth:operator')

// Route login dilindungi dengan:
->middleware('guest:operator')
```

### Tidak Ada User Biasa

Aplikasi ini tidak menggunakan model `User` default Laravel. Hapus atau abaikan migration `create_users_table` jika tidak dibutuhkan.

---

## 12. KONFIGURASI ENVIRONMENT

### `.env` Minimal

```dotenv
APP_NAME="Antrian Digital"
APP_URL=http://sini-antri.test

DB_CONNECTION=mariadb
DB_DATABASE=db_antri
DB_USERNAME=root
DB_PASSWORD=root

BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=antrian-app
REVERB_APP_KEY=antrian-key-local
REVERB_APP_SECRET=antrian-secret-local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

QUEUE_PREFIX=A
QUEUE_AVG_SERVICE_MINUTES=5
QUEUE_RESET_DAILY=true
```

### Perbedaan Production

```dotenv
APP_ENV=production
APP_DEBUG=false
REVERB_SCHEME=https
REVERB_HOST=ws.domain.com
REVERB_PORT=443
```

---

## 13. PERINTAH ARTISAN & SCHEDULER

### Perintah Custom

```
php artisan queue:reset-daily
```

**Fungsi:**

1. Cek `QueueSetting::first()->reset_daily`
2. Jika true: UPDATE semua antrian `waiting` dari hari kemarin → `skipped`
3. Reset `current_counter = 0`, `last_reset_date = today()`

### Scheduler (`routes/console.php`)

```php
Schedule::command('queue:reset-daily')->dailyAt('00:01');
```

### Proses yang Harus Berjalan

```bash
# Development (4 terminal terpisah):
php artisan serve                                      # web server
php artisan reverb:start --host=0.0.0.0 --port=8080   # WebSocket
php artisan queue:work                                 # broadcast jobs
npm run dev                                            # Vite HMR

# Production (via Supervisor):
php artisan reverb:start --host=0.0.0.0 --port=8080
php artisan queue:work --sleep=3 --tries=3
```

### Instalasi Awal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer require laravel/reverb
php artisan reverb:install
npm install
npm run build
```

---

## 14. ATURAN KODING

### Penamaan

```
Model antrian  : Antrian          (BUKAN Queue)
Route binding  : {antrian}        (sesuai nama model)
Table name     : queues            (tetap, via $table = 'queues' di model)
Guard          : operator          (bukan 'web')
```

### Broadcast

```php
// Selalu gunakan broadcast(), BUKAN event()
broadcast(new QueueCalled($antrian, $loket));

// Gunakan toOthers() hanya untuk QueueCreated dari pengunjung
broadcast(new QueueCreated($antrian))->toOthers();
```

### Response API

```php
// Sukses
return response()->json(['message' => '...', 'queue' => $antrian], 200);

// Error
return response()->json(['message' => '...'], 404);  // atau 403, 422
```

### Validasi

```php
// Semua validasi menggunakan $request->validate() langsung di controller
// Tidak perlu FormRequest untuk aplikasi sekecil ini
```

### Query Antrian

```php
// Selalu gunakan scope untuk konsistensi
Antrian::waiting()->first()           // antrian teratas (FIFO)
Antrian::waiting()->count()           // jumlah menunggu
Antrian::waiting()->where('queue_order', '<', $n)->count()  // posisi di depan
```

### Frontend

```js
// Semua POST dari dashboard operator menggunakan pola ini:
async function callQueue() {
    const res = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": CSRF_TOKEN,
        },
        body: JSON.stringify(data),
    });
    const json = await res.json();
    if (res.ok) {
        /* update DOM */
    } else {
        showToast(json.message, "error");
    }
}
```

---

## 15. URUTAN IMPLEMENTASI

Ikuti urutan ini jika membangun dari awal. Jangan lompat langkah.

```
FASE 1 — FONDASI
  [ ] 1. Buat project Laravel 12
  [ ] 2. Konfigurasi .env (DB + Reverb)
  [ ] 3. Buat semua migration
  [ ] 4. php artisan migrate
  [ ] 5. Buat Model: QueueSetting, Operator, Antrian
  [ ] 6. Buat DatabaseSeeder + php artisan db:seed
  [ ] 7. Konfigurasi config/auth.php (guard operator)
  [ ] 8. Daftarkan channels.php di bootstrap/app.php

FASE 2 — BACKEND
  [ ] 9.  Buat Events: QueueCreated, QueueCalled, QueueStatusChanged
  [ ] 10. Buat AuthController (login/logout)
  [ ] 11. Buat VisitorController (register, takeQueue, ticket, ticketPosition)
  [ ] 12. Buat OperatorController (index, call, recall, skip, complete, waitingList)
  [ ] 13. Buat DisplayController (index, status, qrcode)
  [ ] 14. Buat routes/web.php lengkap
  [ ] 15. Buat routes/channels.php
  [ ] 16. Test semua endpoint via curl/Postman

FASE 3 — REVERB & FRONTEND
  [ ] 17. composer require laravel/reverb && php artisan reverb:install
  [ ] 18. npm install pusher-js laravel-echo
  [ ] 19. Buat resources/js/echo.js
  [ ] 20. Update vite.config.js (input: echo.js)

FASE 4 — VIEWS
  [ ] 21. visitor/register.blade.php
  [ ] 22. visitor/ticket.blade.php (dengan Echo listeners)
  [ ] 23. operator/login.blade.php
  [ ] 24. operator/dashboard.blade.php (dengan Echo + fetch actions)
  [ ] 25. display/screen.blade.php (dengan Echo + TTS)
  [ ] 26. display/qrcode.blade.php

FASE 5 — FITUR PENDUKUNG
  [ ] 27. Buat Artisan command ResetDailyQueue
  [ ] 28. Daftarkan schedule di routes/console.php
  [ ] 29. Test end-to-end: scan QR → ambil nomor → panggil → layar TV berbunyi
  [ ] 30. Test multi-operator: 2 browser berbeda login loket berbeda

FASE 6 — PRODUCTION HARDENING
  [ ] 31. Konfigurasi Supervisor untuk Reverb + queue:work
  [ ] 32. Set APP_ENV=production, APP_DEBUG=false
  [ ] 33. php artisan config:cache && php artisan route:cache
  [ ] 34. npm run build
  [ ] 35. Test TTS di browser Chrome/Edge (Safari memerlukan interaksi user lebih dulu)
```

---

## CATATAN PENTING

1. **Jangan rename tabel `queues`** — nama tabel tetap `queues`, hanya nama _class_ PHP yang berubah menjadi `Antrian`.

2. **Jangan tambahkan `QUEUE_CONNECTION=sync`** — broadcast butuh queue worker. Gunakan `database` atau `redis`.

3. **`routes/channels.php` wajib didaftarkan** di `bootstrap/app.php` via parameter `channels:`. Di Laravel 12 tidak auto-load.

4. **Tidak ada `app/Console/Kernel.php`** di Laravel 12. Scheduler ada di `routes/console.php`.

5. **TTS di layar display** menggunakan Web Speech API browser — tidak butuh layanan eksternal. Pastikan browser (Chrome/Edge) di TV tidak dalam mode silent.

6. **Polling fallback** di tiket pengunjung (`setInterval 30 detik`) adalah jaring pengaman jika WebSocket putus — jangan hapus.

7. **`broadcast()->toOthers()`** hanya relevan jika pengirim juga subscribe ke channel yang sama. Untuk QueueCreated dari visitor (tidak subscribe), boleh pakai `broadcast()` biasa. Untuk konsistensi, tetap pakai `toOthers()`.

8. **Index database** `(status, queue_order)` sangat kritis untuk performa `Antrian::waiting()->first()`. Jangan hapus.
