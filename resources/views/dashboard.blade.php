<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartOryza - Dashboard Monitor</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif']
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            700: '#15803d',
                            900: '#2b5329'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #f8fafc;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .stat-card {
            transition: all 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
        }

        /* Tab Sawah */
        .sawah-tab {
            transition: all 0.2s;
            cursor: pointer;
        }

        .sawah-tab.active {
            background: #2b5329;
            color: white;
            box-shadow: 0 4px 12px -2px rgba(43, 83, 41, 0.35);
        }

        .sawah-tab:not(.active):hover {
            background: #f0fdf4;
            color: #2b5329;
        }

        .sawah-tab .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Pagination */
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #f3f4f6;
            color: #111827;
        }

        .pagination-btn.active {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #111827;
            font-weight: 600;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Dummy overlay for locked tabs */
        .locked-overlay {
            pointer-events: none;
            opacity: 0.45;
            filter: grayscale(0.3);
        }

        /* Filter tab */
        .filter-tab {
            transition: all 0.15s;
            cursor: pointer;
        }

        .filter-tab.active {
            background: #2b5329;
            color: white;
            border-color: #2b5329;
        }

        .filter-tab:not(.active):hover {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #15803d;
        }

        /* ===== FLOOD ALERT ANIMATIONS ===== */
        @keyframes flood-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.01); }
        }

        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .flood-alert-animate {
            animation: flood-pulse 2s ease-in-out infinite, slide-down 0.35s ease-out;
        }

        .water-card-danger {
            border: 2px solid #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }

        .water-card-warning {
            border: 2px solid #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12) !important;
        }

        .water-card-normal {
            border: 1px solid #f3f4f6 !important;
        }

        /* Water level bar */
        .water-level-bar {
            height: 6px;
            border-radius: 999px;
            overflow: hidden;
            background: #f3f4f6;
            margin-top: 8px;
        }

        .water-level-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.6s ease, background-color 0.4s ease;
        }

        /* Toast notification */
        #flood-toast {
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 9999;
            min-width: 320px;
            max-width: 380px;
            background: #fff;
            border-left: 4px solid #ef4444;
            border-radius: 16px;
            box-shadow: 0 8px 32px -4px rgba(0,0,0,0.18);
            padding: 16px 18px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            opacity: 0;
            transform: translateX(120%);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            pointer-events: none;
        }

        #flood-toast.show {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        #flood-toast.warning-toast {
            border-left-color: #f59e0b;
        }

        /* Print styles */
        @media print {
            nav, .sidebar, button, .btn, .refresh-btn, .pilih-lahan,
            .card-status, .alert, select, #flood-toast { display: none !important; }

            body, .container, .main-content {
                background: #fff !important;
                color: #000 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .report-header {
                margin-bottom: 25px !important;
                page-break-after: avoid;
            }

            .report-header h2 {
                font-size: 24px !important;
                margin-bottom: 5px !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 15px !important;
                table-layout: auto !important;
            }

            th, td {
                border-bottom: 1px solid #ddd !important;
                padding: 8px 12px !important;
                text-align: left !important;
                font-size: 12px !important;
                white-space: nowrap !important;
            }

            th {
                background-color: #f8f9fa !important;
                color: #000 !important;
                font-weight: bold !important;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            td i, td svg { display: none !important; }

            td:first-child, th:first-child {
                width: 25% !important;
                white-space: normal !important;
            }
        }
    </style>
</head>

<body class="min-h-screen text-gray-800 antialiased pb-12">

    <!-- ===== FLOOD TOAST NOTIFICATION ===== -->
    <div id="flood-toast">
        <div id="flood-toast-icon" class="text-2xl mt-0.5">🚨</div>
        <div class="flex-1">
            <p id="flood-toast-title" class="text-sm font-bold text-red-700 mb-0.5 leading-tight">Peringatan Luapan Air!</p>
            <p id="flood-toast-body" class="text-xs text-gray-600 leading-relaxed">Level air mendekati batas kritis.</p>
            <p class="text-[10px] text-gray-400 mt-1" id="flood-toast-time">—</p>
        </div>
        <button onclick="dismissToast()" class="text-gray-400 hover:text-gray-600 text-base leading-none mt-0.5">✕</button>
    </div>

    <nav class="glass-nav border-b border-gray-100 sticky top-0 z-50 py-4 px-6 md:px-10 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center shadow-md">
                <span class="text-xl text-white">🌾</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight font-serif text-brand-900">Smart<span class="text-brand-500">Oryza</span></h1>
        </div>
        <div class="flex items-center gap-5">
            <div class="text-right hidden sm:block">
                <p id="username" class="text-sm font-semibold text-gray-800">Petani Modern</p>
                <p class="text-xs text-gray-500" id="current-time">Loading...</p>
            </div>
            <a href="{{ route('home') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold border border-gray-300 transition-colors flex items-center gap-2">
                <i class="fas fa-home"></i> Home
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 md:px-10 py-8">

        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Monitoring Lahan Padi</h2>
                <p class="text-gray-500">Sistem Otomasi Irigasi & Pengusir Hama</p>
            </div>
            <div id="alert-container"
                class="bg-white border px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm self-start">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative h-3 w-3 bg-emerald-500 rounded-full"></span>
                </span>
                <p id="system-status" class="text-sm font-bold text-emerald-700">Sistem Online</p>
            </div>
        </div>

        <!-- ===== TAB SAWAH ===== -->
        <div class="mb-8">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Pilih Lahan</p>
            <div class="flex flex-wrap gap-3">
                <button onclick="switchSawah(1)" id="tab-1"
                    class="sawah-tab active flex items-center gap-3 px-5 py-3 rounded-2xl border border-transparent">
                    <span class="dot bg-emerald-400"></span>
                    <div class="text-left">
                        <p class="text-sm font-bold leading-tight">Sawah 1</p>
                        <p class="text-[10px] opacity-70 leading-tight">Blok A — Aktif</p>
                    </div>
                </button>
                <button onclick="switchSawah(2)" id="tab-2"
                    class="sawah-tab flex items-center gap-3 px-5 py-3 rounded-2xl border border-gray-200 bg-white text-gray-600">
                    <span class="dot bg-yellow-400"></span>
                    <div class="text-left">
                        <p class="text-sm font-bold leading-tight">Sawah 2</p>
                        <p class="text-[10px] opacity-60 leading-tight">Blok B — Standby</p>
                    </div>
                </button>
                <button onclick="switchSawah(3)" id="tab-3"
                    class="sawah-tab flex items-center gap-3 px-5 py-3 rounded-2xl border border-gray-200 bg-white text-gray-600">
                    <span class="dot bg-blue-400"></span>
                    <div class="text-left">
                        <p class="text-sm font-bold leading-tight">Sawah 3</p>
                        <p class="text-[10px] opacity-60 leading-tight">Blok C — Irigasi</p>
                    </div>
                </button>
                <button onclick="switchSawah(4)" id="tab-4"
                    class="sawah-tab flex items-center gap-3 px-5 py-3 rounded-2xl border border-gray-200 bg-white text-gray-600">
                    <span class="dot bg-gray-400"></span>
                    <div class="text-left">
                        <p class="text-sm font-bold leading-tight">Sawah 4</p>
                        <p class="text-[10px] opacity-60 leading-tight">Blok D — Offline</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- ===== FLOOD WARNING BANNER ===== -->
        <div id="alert-banjir" class="hidden mb-5">
            <!-- Danger level -->
            <div id="alert-banjir-kritis"
                class="flood-alert-animate hidden bg-red-50 border-2 border-red-300 rounded-2xl p-4 flex items-start gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-xl">🚨</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-red-800 mb-0.5">PERINGATAN KRITIS — Air Hampir Meluap ke Sawah!</p>
                    <p class="text-xs text-red-700 leading-relaxed">
                        Jarak sensor ke permukaan air hanya <b id="alert-banjir-dist-kritis">— cm</b>. 
                        Air berada di level <b>sangat tinggi (≤ 2.5 cm dari sensor)</b>. Segera tutup pintu air masuk sawah dan 
                        siapkan tindakan darurat pencegahan luapan!
                    </p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] font-bold text-red-500 bg-red-100 px-2 py-0.5 rounded-full">● LEVEL KRITIS</span>
                        <span class="text-[10px] text-red-400" id="alert-banjir-time-kritis">—</span>
                    </div>
                </div>
                <button onclick="dismissBanjirAlert()" class="text-red-400 hover:text-red-600 text-lg leading-none flex-shrink-0">✕</button>
            </div>
            <!-- Warning level -->
            <div id="alert-banjir-siaga"
                class="hidden bg-amber-50 border-2 border-amber-300 rounded-2xl p-4 flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-xl">⚠️</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-amber-800 mb-0.5">SIAGA — Debit Air Mulai Naik</p>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Jarak sensor ke permukaan air <b id="alert-banjir-dist-siaga">— cm</b> (antara 2.5–5 cm). 
                        Pantau kondisi air secara berkala. Jika terus naik hingga ≤ 2.5 cm, 
                        segera tutup pintu air sawah.
                    </p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">● LEVEL SIAGA</span>
                        <span class="text-[10px] text-amber-500" id="alert-banjir-time-siaga">—</span>
                    </div>
                </div>
                <button onclick="dismissBanjirAlert()" class="text-amber-400 hover:text-amber-600 text-lg leading-none flex-shrink-0">✕</button>
            </div>
        </div>

        <div id="siklus-tanam-container" class="mb-6 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-2 text-gray-700 font-bold">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                        <h3>Siklus Tanam Padi</h3>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5" id="siklus-info-hari">Menghitung hari siklus...</p>
                    <p class="text-xs text-gray-500 font-medium" id="siklus-estimasi-panen">Estimasi Panen: -</p>
                </div>
                <div class="flex items-center gap-2 no-print">
                    <button onclick="window.mulaiTanamBaru()"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-1">
                        <i class="fas fa-seedling"></i> Mulai Tanam Baru
                    </button>
                    <button onclick="window.resetSiklusTanam()"
                        class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 text-xs font-bold rounded-xl border transition-all flex items-center gap-1">
                        <i class="fas fa-undo"></i> Reset 0
                    </button>
                </div>
                <span id="siklus-badge-status"
                    class="px-3 py-1 rounded-full text-xs font-bold bg-gray-50 text-gray-500 border">
                    Memuat status...
                </span>
            </div>

            <div class="relative w-full h-3 bg-gray-100 rounded-full overflow-hidden mb-2">
                <div id="bar-vegetatif" class="absolute top-0 left-0 h-full bg-emerald-500 transition-all duration-500" style="width: 0%"></div>
                <div id="bar-pengeringan" class="absolute top-0 left-0 h-full bg-amber-400 transition-all duration-500" style="width: 0%"></div>
                <div id="bar-panen" class="absolute top-0 left-0 h-full bg-red-400 transition-all duration-500" style="width: 0%"></div>
                <div id="slider-posisi-hari"
                    class="absolute top-1/2 left-[0%] -translate-y-1/2 w-3 h-3 bg-white border-2 border-brand-900 rounded-full shadow transition-all duration-500">
                </div>
            </div>

            <div class="flex justify-between text-[10px] font-bold text-gray-400 uppercase tracking-wider px-1">
                <span>Mulai Tanam (Hari 1)</span>
                <span>Fase Vegetatif & Generatif</span>
                <span class="text-amber-600">Stop Irigasi (Hari 120)</span>
                <span>Panen (Hari 180)</span>
            </div>

            <div id="alert-ultrasonik"
                class="mt-5 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3 hidden">
                <span class="text-xl">🌊</span>
                <div>
                    <p class="text-xs font-bold text-red-700 mb-0.5">Cek Ultrasonik:</p>
                    <p class="text-xs text-red-600 leading-relaxed">Terdapat genangan air <b id="alert-water-level">0cm</b>. Karena saat ini memasuki fase pengeringan, segera pastikan saluran pembuangan air sawah terbuka!</p>
                </div>
            </div>

            <!-- Banner dummy untuk sawah 2/3/4 -->
            <div id="dummy-banner"
                class="hidden mb-6 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3 flex items-center gap-3">
                <i class="fas fa-circle-info text-amber-500"></i>
                <p class="text-sm text-amber-700 font-medium">Data lahan ini belum terhubung ke perangkat. Menampilkan data dummy.</p>
            </div>

            <!-- ===== KONTEN UTAMA ===== -->
            <div id="main-content">

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <!-- Kelembapan Tanah -->
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <p class="text-sm font-medium text-gray-500 mb-2">Kelembapan Tanah</p>
                        <div class="flex items-baseline gap-1">
                            <h3 class="text-4xl font-extrabold text-gray-900" id="soil-value">0</h3>
                            <span class="text-lg text-gray-500 font-bold">%</span>
                        </div>
                        <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div id="soil-bar" class="h-full bg-blue-500 w-0 transition-all duration-500"></div>
                        </div>
                    </div>

                    <!-- Irigasi (Pintu Air) — dengan water level indicator -->
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm water-card-normal transition-all duration-300" id="water-card">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-medium text-gray-500">Irigasi (Pintu Air)</p>
                            <span id="servo1-badge"
                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400">OTOMATIS</span>
                        </div>
                        <div class="flex items-baseline gap-1">
                            <h3 class="text-4xl font-extrabold text-gray-900" id="distance">0</h3>
                            <span class="text-lg text-gray-500 font-bold">cm</span>
                        </div>

                        <!-- Water level status badge -->
                        <div class="mt-2 flex items-center gap-2">
                            <span id="water-level-badge"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400">
                                <span id="water-level-dot" class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                <span id="water-level-text">Memuat...</span>
                            </span>
                        </div>

                        <!-- Water fill progress bar -->
                        <div class="water-level-bar">
                            <div id="water-level-fill" class="water-level-fill bg-emerald-400" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-[9px] text-gray-300 font-medium mt-1">
                            <span>Jauh (Aman)</span>
                            <span>Dekat (Bahaya)</span>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400">MODE:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="mode-toggle" class="sr-only peer" onchange="window.toggleMode()">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-2 text-[10px] font-bold text-gray-500" id="mode-label">Otomatis</span>
                            </label>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button id="btn-buka" onclick="window.controlServo(1)"
                                class="flex-1 py-2 bg-gray-100 text-gray-400 text-[10px] font-bold rounded cursor-not-allowed"
                                disabled>BUKA</button>
                            <button id="btn-tutup" onclick="window.controlServo(2)"
                                class="flex-1 py-2 bg-gray-100 text-gray-400 text-[10px] font-bold rounded cursor-not-allowed"
                                disabled>TUTUP</button>
                        </div>
                    </div>

                    <!-- Hama (PIR) -->
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-medium text-gray-500">Hama (PIR)</p>
                            <span id="servo2-badge"
                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400">IDLE</span>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <span id="pir-dot" class="h-4 w-4 rounded-full bg-emerald-500"></span>
                            <h3 class="text-2xl font-bold text-emerald-600" id="pir-status">Aman</h3>
                        </div>
                    </div>

                    <!-- Status Sistem -->
                    <div class="stat-card bg-brand-900 rounded-2xl p-6 shadow-md text-white">
                        <p class="text-sm font-medium text-brand-100 mb-2">Status Sistem</p>
                        <h3 class="text-2xl font-bold" id="main-mode-display">OTOMATIS</h3>
                        <div class="mt-4 flex items-center gap-2 text-xs text-brand-100">
                            <i class="fas fa-microchip"></i>
                            <span id="esp32-status">Menghubungkan...</span>
                        </div>
                        <!-- Flood status indicator in system card -->
                        <div id="flood-status-mini" class="mt-3 pt-3 border-t border-white/10 hidden">
                            <div class="flex items-center gap-2">
                                <span id="flood-status-mini-dot" class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                                <span id="flood-status-mini-text" class="text-[10px] font-bold text-red-300">Air Sungai Kritis</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== 2 GRAFIK TERPISAH ===== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Grafik Kelembapan Tanah -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <h3 class="font-bold text-gray-700">Kelembapan Tanah</h3>
                            <span class="ml-auto text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Real-Time</span>
                        </div>
                        <div class="h-56"><canvas id="soilChart"></canvas></div>
                    </div>

                    <!-- Grafik Level Air -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <h3 class="font-bold text-gray-700">Jarak Sensor ke Air (Debit Sungai)</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <!-- Threshold legend -->
                                <span class="text-[9px] text-amber-500 font-bold bg-amber-50 px-2 py-0.5 rounded-full">⚠ Siaga &lt;5cm</span>
                                <span class="text-[9px] text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded-full">🚨 Kritis ≤2.5cm</span>
                                <span class="ml-1 text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Real-Time</span>
                            </div>
                        </div>
                        <div class="h-56"><canvas id="waterChart"></canvas></div>
                    </div>
                </div>

                <!-- Aktivitas Terbaru -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-6">
                    <h3 class="font-bold mb-4 text-gray-700">Aktivitas Terbaru</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 overflow-y-auto max-h-48 text-sm" id="log-container">
                        <p class="text-gray-400 italic">Menunggu data...</p>
                    </div>
                </div>

                <!-- Tabel Log -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
                        <i class="fas fa-clipboard-list text-gray-400"></i>
                        <h3 class="font-bold text-gray-700">Log Aktivitas</h3>
                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg" id="log-table-count">Memuat...</span>

                        <div class="ml-auto flex flex-wrap items-center gap-2 no-print">
                            <div class="flex items-center gap-1 border border-gray-200 rounded-xl p-1">
                                <button onclick="setFilter('hari')" id="f-hari"
                                    class="filter-tab px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent text-gray-500">Hari Ini</button>
                                <button onclick="setFilter('minggu')" id="f-minggu"
                                    class="filter-tab px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent text-gray-500">Minggu Ini</button>
                                <button onclick="setFilter('bulan')" id="f-bulan"
                                    class="filter-tab px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent text-gray-500">Bulan Ini</button>
                                <button onclick="setFilter('tahun')" id="f-tahun"
                                    class="filter-tab px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent text-gray-500">Tahun Ini</button>
                                <button onclick="setFilter('semua')" id="f-semua"
                                    class="filter-tab active px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent">Semua</button>
                            </div>
                            <button onclick="cetakLaporan()"
                                class="flex items-center gap-2 px-4 py-2 bg-brand-900 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-all">
                                <i class="fas fa-print"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <div id="print-area">
                        <div id="print-header" style="display:none;" class="p-6 border-b border-gray-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center">
                                    <span class="text-white text-lg">🌾</span>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold font-serif text-brand-900">SmartOryza</h1>
                                    <p class="text-xs text-gray-500">Sistem Monitoring Lahan Padi</p>
                                </div>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800" id="print-title">Laporan Log Aktivitas</h2>
                            <p class="text-sm text-gray-500" id="print-subtitle"></p>
                            <p class="text-xs text-gray-400 mt-1" id="print-generated"></p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" style="table-layout: fixed;">
                                <colgroup>
                                    <col style="width: 220px">
                                    <col style="width: 140px">
                                    <col style="width: 130px">
                                    <col style="width: 130px">
                                </colgroup>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 border-b border-gray-100">
                                            <i class="fas fa-clock mr-1"></i>Waktu</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 border-b border-gray-100">Status PIR</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 border-b border-gray-100">Tanah (%)</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 border-b border-gray-100">
                                            <i class="fas fa-water mr-1"></i>Air (cm)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="log-table-body" class="divide-y divide-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">
                                            Menghubungkan ke Firebase...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="print-footer" style="display:none;" class="px-6 py-4 border-t border-gray-100 text-xs text-gray-400">
                            <span id="print-total"></span> — Dicetak dari SmartOryza Dashboard
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-3 border-t border-gray-100 flex items-center justify-between gap-4 no-print">
                        <span class="text-xs text-gray-400" id="page-info">—</span>
                        <div class="flex items-center gap-1" id="pagination-btns"></div>
                    </div>
                </div>

            </div><!-- end #main-content -->
        </div><!-- end siklus-tanam-container -->
    </main>

    <script>
        let activeSawah = 1;
        let toastDismissed = false;
        let lastFloodLevel = 'normal'; // track level changes

        const dummyData = {
            2: { soil: 62, water: 14.2, pir: 'Aman', mode: 'OTOMATIS', esp: 'Standby' },
            3: { soil: 78, water: 8.5,  pir: 'Aman', mode: 'IRIGASI',  esp: 'Aktif' },
            4: { soil: 0,  water: 0,    pir: 'Aman', mode: 'OFFLINE',  esp: 'Tidak Terhubung' },
        };

        // ===== FLOOD THRESHOLD (jarak sensor ke permukaan air, dalam cm) =====
        // Makin KECIL jarak = air makin TINGGI / hampir meluap
        // Tempat air kecil: range efektif 2–8 cm
        const THRESHOLD_SIAGA   = 5;   // kuning: air mulai naik (jarak < 5 cm)
        const THRESHOLD_KRITIS  = 2.5; // merah: air hampir meluap (jarak ≤ 2.5 cm)
        const MAX_SENSOR_RANGE  = 8;   // jarak maksimal normal sensor (cm)

        // ===== OFFLINE / STALE DATA DETECTION =====
        // Jika tidak ada data baru dalam X detik, anggap alat offline
        const OFFLINE_TIMEOUT_MS = 30000; // 30 detik tanpa data = offline
        let lastDataTimestamp    = null;
        let offlineCheckInterval = null;

        function startOfflineChecker() {
            if (offlineCheckInterval) clearInterval(offlineCheckInterval);
            offlineCheckInterval = setInterval(() => {
                if (!lastDataTimestamp) return;
                const elapsed = Date.now() - lastDataTimestamp;
                const espStatus  = document.getElementById('esp32-status');
                const sysStatus  = document.getElementById('system-status');
                const alertCont  = document.getElementById('alert-container');
                const distEl     = document.getElementById('distance');

                if (elapsed > OFFLINE_TIMEOUT_MS) {
                    // Alat offline — tampilkan indikator
                    if (espStatus)  espStatus.innerText = '⚠ ESP32 Tidak Merespons';
                    if (sysStatus)  { sysStatus.innerText = 'Perangkat Offline'; sysStatus.className = 'text-sm font-bold text-red-600'; }
                    if (alertCont)  alertCont.className = 'bg-white border border-red-200 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm self-start';
                    // Ketika offline, jangan trigger peringatan banjir dari data lama
                    const wBadge = document.getElementById('water-level-badge');
                    const wText  = document.getElementById('water-level-text');
                    const wDot   = document.getElementById('water-level-dot');
                    if (wText) wText.innerText = 'Perangkat Offline';
                    if (wDot)  wDot.className  = 'w-1.5 h-1.5 rounded-full bg-gray-400';
                    if (wBadge) wBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500';
                    // Sembunyikan alert banjir kalau alat offline (data bisa stale)
                    document.getElementById('alert-banjir')?.classList.add('hidden');
                    document.getElementById('alert-banjir-kritis')?.classList.add('hidden');
                    document.getElementById('alert-banjir-siaga')?.classList.add('hidden');
                    document.getElementById('flood-status-mini')?.classList.add('hidden');
                    const waterCard = document.getElementById('water-card');
                    if (waterCard) {
                        waterCard.className = waterCard.className.replace(/water-card-(normal|warning|danger)/g,'').trim();
                        waterCard.classList.add('water-card-normal');
                    }
                } else {
                    // Online
                    if (sysStatus) { sysStatus.innerText = 'Sistem Online'; sysStatus.className = 'text-sm font-bold text-emerald-700'; }
                    if (alertCont) alertCont.className = 'bg-white border px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm self-start';
                }
            }, 5000); // cek setiap 5 detik
        }
        startOfflineChecker();

        function getFloodLevel(dist) {
            if (dist <= THRESHOLD_KRITIS) return 'kritis';
            if (dist <= THRESHOLD_SIAGA)  return 'siaga';
            return 'normal';
        }

        // Kalkulasi persentase "penuh" (kebalikan dari jarak)
        function waterFillPercent(dist) {
            const pct = ((MAX_SENSOR_RANGE - Math.min(dist, MAX_SENSOR_RANGE)) / MAX_SENSOR_RANGE) * 100;
            return Math.max(0, Math.min(100, pct));
        }

        // ===== UPDATE FLOOD UI =====
        function updateFloodUI(dist) {
            const level = getFloodLevel(dist);
            const waterCard = document.getElementById('water-card');
            const alertBanjir = document.getElementById('alert-banjir');
            const alertKritis = document.getElementById('alert-banjir-kritis');
            const alertSiaga  = document.getElementById('alert-banjir-siaga');
            const badge       = document.getElementById('water-level-badge');
            const dot         = document.getElementById('water-level-dot');
            const text        = document.getElementById('water-level-text');
            const fill        = document.getElementById('water-level-fill');
            const floodMini   = document.getElementById('flood-status-mini');
            const floodMiniDot  = document.getElementById('flood-status-mini-dot');
            const floodMiniText = document.getElementById('flood-status-mini-text');

            const pct = waterFillPercent(dist);
            const nowStr = new Date().toLocaleTimeString('id-ID');

            // Update progress bar fill
            if (fill) {
                fill.style.width = pct + '%';
                fill.style.backgroundColor = level === 'kritis' ? '#ef4444' : level === 'siaga' ? '#f59e0b' : '#22c55e';
            }

            // Update card border
            if (waterCard) {
                waterCard.className = waterCard.className
                    .replace(/water-card-(normal|warning|danger)/g, '')
                    .trim();
                if (level === 'kritis') waterCard.classList.add('water-card-danger');
                else if (level === 'siaga') waterCard.classList.add('water-card-warning');
                else waterCard.classList.add('water-card-normal');
            }

            // Update badge
            if (level === 'kritis') {
                if (dot)  dot.className = 'w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse';
                if (text) text.innerText = 'KRITIS — Hampir Luap!';
                if (badge) badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700';
            } else if (level === 'siaga') {
                if (dot)  dot.className = 'w-1.5 h-1.5 rounded-full bg-amber-500';
                if (text) text.innerText = 'SIAGA — Pantau Debit';
                if (badge) badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700';
            } else {
                if (dot)  dot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
                if (text) text.innerText = 'Normal';
                if (badge) badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700';
            }

            // Update banner alert
            if (level === 'kritis') {
                if (alertBanjir) alertBanjir.classList.remove('hidden');
                if (alertKritis) {
                    alertKritis.classList.remove('hidden');
                    const d = document.getElementById('alert-banjir-dist-kritis');
                    const t = document.getElementById('alert-banjir-time-kritis');
                    if (d) d.innerText = dist + ' cm';
                    if (t) t.innerText = 'Terdeteksi pada ' + nowStr;
                }
                if (alertSiaga) alertSiaga.classList.add('hidden');
                if (floodMini) {
                    floodMini.classList.remove('hidden');
                    if (floodMiniDot) floodMiniDot.className = 'w-2 h-2 rounded-full bg-red-400 animate-pulse';
                    if (floodMiniText) floodMiniText.innerText = 'Air Sungai Kritis';
                    floodMiniText.className = 'text-[10px] font-bold text-red-300';
                }
            } else if (level === 'siaga') {
                if (alertBanjir) alertBanjir.classList.remove('hidden');
                if (alertSiaga) {
                    alertSiaga.classList.remove('hidden');
                    const d = document.getElementById('alert-banjir-dist-siaga');
                    const t = document.getElementById('alert-banjir-time-siaga');
                    if (d) d.innerText = dist + ' cm';
                    if (t) t.innerText = 'Terdeteksi pada ' + nowStr;
                }
                if (alertKritis) alertKritis.classList.add('hidden');
                if (floodMini) {
                    floodMini.classList.remove('hidden');
                    if (floodMiniDot) floodMiniDot.className = 'w-2 h-2 rounded-full bg-amber-400';
                    if (floodMiniText) {
                        floodMiniText.innerText = 'Air Sungai Siaga';
                        floodMiniText.className = 'text-[10px] font-bold text-amber-300';
                    }
                }
            } else {
                if (alertBanjir) alertBanjir.classList.add('hidden');
                if (alertKritis) alertKritis.classList.add('hidden');
                if (alertSiaga)  alertSiaga.classList.add('hidden');
                if (floodMini)   floodMini.classList.add('hidden');
                toastDismissed = false; // reset toast dismiss saat kembali normal
            }

            // Trigger toast hanya saat level NAIK (normal→siaga, siaga→kritis, normal→kritis)
            if (level !== lastFloodLevel && level !== 'normal' && !toastDismissed) {
                showFloodToast(level, dist);
            }
            lastFloodLevel = level;
        }

        // ===== TOAST =====
        let toastTimer = null;

        function showFloodToast(level, dist) {
            const toast = document.getElementById('flood-toast');
            const icon  = document.getElementById('flood-toast-icon');
            const title = document.getElementById('flood-toast-title');
            const body  = document.getElementById('flood-toast-body');
            const time  = document.getElementById('flood-toast-time');
            if (!toast) return;

            clearTimeout(toastTimer);

            if (level === 'kritis') {
                toast.className = toast.className.replace('warning-toast', '').trim();
                toast.style.borderLeftColor = '#ef4444';
                if (icon)  icon.innerText = '🚨';
                if (title) { title.innerText = 'KRITIS — Air Hampir Meluap!'; title.className = 'text-sm font-bold text-red-700 mb-0.5 leading-tight'; }
                if (body)  body.innerText = `Jarak sensor ke air hanya ${dist} cm (≤ 2.5 cm). Segera tutup pintu air masuk sawah!`;
            } else {
                toast.style.borderLeftColor = '#f59e0b';
                if (icon)  icon.innerText = '⚠️';
                if (title) { title.innerText = 'Siaga — Debit Air Naik'; title.className = 'text-sm font-bold text-amber-700 mb-0.5 leading-tight'; }
                if (body)  body.innerText = `Jarak sensor ke air ${dist} cm (antara 2.5–5 cm). Pantau kondisi secara berkala.`;
            }

            if (time) time.innerText = new Date().toLocaleTimeString('id-ID');
            toast.classList.add('show');

            toastTimer = setTimeout(() => {
                toast.classList.remove('show');
            }, 7000);
        }

        function dismissToast() {
            const toast = document.getElementById('flood-toast');
            if (toast) toast.classList.remove('show');
            toastDismissed = true;
            clearTimeout(toastTimer);
        }

        function dismissBanjirAlert() {
            const alertBanjir = document.getElementById('alert-banjir');
            if (alertBanjir) alertBanjir.classList.add('hidden');
        }

        // ===== SWITCH SAWAH =====
        function switchSawah(n) {
            activeSawah = n;
            for (let i = 1; i <= 4; i++) {
                const tab = document.getElementById('tab-' + i);
                if (tab) {
                    tab.className = i === n
                        ? 'sawah-tab active flex items-center gap-3 px-5 py-3 rounded-2xl border border-transparent'
                        : 'sawah-tab flex items-center gap-3 px-5 py-3 rounded-2xl border border-gray-200 bg-white text-gray-600';
                }
            }

            const banner  = document.getElementById('dummy-banner');
            const content = document.getElementById('main-content');

            if (n === 1) {
                if (banner)  banner.classList.add('hidden');
                if (content) content.classList.remove('locked-overlay');
                if (window.refreshRealtimeData) window.refreshRealtimeData();
            } else {
                if (banner)  banner.classList.remove('hidden');
                if (content) content.classList.add('locked-overlay');

                const d = dummyData[n];
                if (!d) return;

                document.getElementById('soil-value')?.setAttribute('innerText', d.soil);
                if (document.getElementById('soil-value')) document.getElementById('soil-value').innerText = d.soil;
                if (document.getElementById('soil-bar'))   document.getElementById('soil-bar').style.width = d.soil + '%';
                if (document.getElementById('distance'))   document.getElementById('distance').innerText = d.water;
                if (document.getElementById('main-mode-display')) document.getElementById('main-mode-display').innerText = d.mode;
                if (document.getElementById('esp32-status')) document.getElementById('esp32-status').innerText = d.esp;
                if (document.getElementById('alert-water-level')) document.getElementById('alert-water-level').innerText = d.water + 'cm';

                const pirStatus = document.getElementById('pir-status');
                const pirDot    = document.getElementById('pir-dot');
                if (pirStatus) {
                    pirStatus.innerText   = d.pir === 'Bahaya' ? 'BAHAYA' : 'AMAN';
                    pirStatus.className   = d.pir === 'Bahaya' ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-emerald-600';
                }
                if (pirDot) {
                    pirDot.className = d.pir === 'Bahaya'
                        ? 'h-4 w-4 rounded-full bg-red-600 animate-pulse'
                        : 'h-4 w-4 rounded-full bg-emerald-500';
                }

                // Update flood UI for dummy data
                updateFloodUI(parseFloat(d.water) || 0);
            }
        }
    </script>

    <script type="module">
        import { initializeApp }        from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
        import { getDatabase, ref, onValue, set, query, limitToLast }
            from "https://www.gstatic.com/firebasejs/10.12.0/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyC0QjC5TAy-ia1AzLLaaPLL53gcmLH1TbM",
            authDomain: "smartoryza.firebaseapp.com",
            databaseURL: "https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "smartoryza",
            storageBucket: "smartoryza.firebasestorage.app",
            appId: "1:63042198526:web:fa0620b2c786a53c977eaf"
        };

        const app = initializeApp(firebaseConfig);
        const db  = getDatabase(app);

        let databaseCache = { latest: null };

        // ===== CHART: SOIL =====
        const soilCtx = document.getElementById('soilChart').getContext('2d');
        let soilData = [], chartLabels = [];
        const soilChart = new Chart(soilCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Kelembapan (%)',
                    data: soilData,
                    borderColor: '#3b82f6',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true, max: 100,
                        ticks: { callback: v => v + '%' },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // ===== CHART: WATER (dengan threshold line) =====
        const waterCtx = document.getElementById('waterChart').getContext('2d');
        let waterData = [], waterLabels = [];
        const waterChart = new Chart(waterCtx, {
            type: 'line',
            data: {
                labels: waterLabels,
                datasets: [
                    {
                        label: 'Jarak Sensor (cm)',
                        data: waterData,
                        borderColor: '#22c55e',
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(34, 197, 94, 0.08)',
                        pointRadius: 3,
                        pointBackgroundColor: '#22c55e'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    annotation: {}
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => v + ' cm' },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // Override point color based on flood level
        function getPointColor(val) {
            if (val <= 15) return '#ef4444';
            if (val <= 30) return '#f59e0b';
            return '#22c55e';
        }

        // ===== SIKLUS TANAM =====
        window.mulaiTanamBaru = function () {
            if (confirm("Apakah Anda yakin ingin memulai siklus tanam baru hari ini? Data siklus sebelumnya akan di-reset.")) {
                const hariIni = new Date().toISOString().split('T')[0];
                set(ref(db, 'iot/siklus/tanggal_tanam'), hariIni)
                    .then(() => alert("Siklus tanam baru berhasil dimulai!"))
                    .catch(err => alert("Gagal menyimpan data: " + err));
            }
        };

        window.resetSiklusTanam = function () {
            if (confirm("Apakah Anda ingin mereset siklus tanam menjadi Hari 0 (Belum Menanam)?")) {
                set(ref(db, 'iot/siklus/tanggal_tanam'), null)
                    .then(() => alert("Siklus berhasil dikosongkan."))
                    .catch(err => alert("Gagal mereset: " + err));
            }
        };

        onValue(ref(db, 'iot/siklus/tanggal_tanam'), (snapshot) => {
            if (activeSawah !== 1) return;
            const tanggalTanamStr = snapshot.val();
            const infoHari      = document.getElementById('siklus-info-hari');
            const estimasiPanen = document.getElementById('siklus-estimasi-panen');
            const badgeStatus   = document.getElementById('siklus-badge-status');
            const alertUltrasonik = document.getElementById('alert-ultrasonik');
            const barVeg    = document.getElementById('bar-vegetatif');
            const barKering = document.getElementById('bar-pengeringan');
            const barPanen  = document.getElementById('bar-panen');
            const sliderHari = document.getElementById('slider-posisi-hari');

            if (!tanggalTanamStr) {
                if (infoHari)      infoHari.innerText = "Belum Ada Siklus Tanam Aktif (Hari 0)";
                if (estimasiPanen) estimasiPanen.innerText = "Estimasi Panen: -";
                if (badgeStatus) { badgeStatus.innerText = "N/A — Belum Mulai"; badgeStatus.className = "px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-400 border"; }
                if (alertUltrasonik) alertUltrasonik.classList.add('hidden');
                if (barVeg)    barVeg.style.width = "0%";
                if (barKering) barKering.style.width = "0%";
                if (barPanen)  barPanen.style.width = "0%";
                if (sliderHari) sliderHari.style.left = "0%";
                return;
            }

            const tglTanam    = new Date(tanggalTanamStr);
            const tglSekarang = new Date();
            tglTanam.setHours(0,0,0,0);
            tglSekarang.setHours(0,0,0,0);
            const selisihWaktu = tglSekarang.getTime() - tglTanam.getTime();
            const hitungHari   = Math.floor(selisihWaktu / (1000*60*60*24)) + 1;

            const tglPanen = new Date(tglTanam);
            tglPanen.setDate(tglPanen.getDate() + 180);
            const opsiTanggal = { year: 'numeric', month: 'long', day: 'numeric' };
            if (estimasiPanen) estimasiPanen.innerText = "Estimasi Panen: " + tglPanen.toLocaleDateString('id-ID', opsiTanggal);
            if (infoHari) infoHari.innerText = hitungHari < 1 ? "Persiapan Lahan (Hari 0)" : "Siklus Berjalan: Hari ke-" + hitungHari;

            const totalHariMaksimal = 180;
            let posisiPersen = (Math.min(Math.max(hitungHari, 0), totalHariMaksimal) / totalHariMaksimal) * 100;
            if (sliderHari) sliderHari.style.left = posisiPersen + "%";

            if (hitungHari <= 120) {
                let progressVeg = (Math.max(hitungHari, 0) / 120) * 70;
                if (barVeg)    barVeg.style.width    = progressVeg + "%";
                if (barKering) barKering.style.width = "0%";
                if (barPanen)  barPanen.style.width  = "0%";
                if (badgeStatus) { badgeStatus.innerText = "🌱 Fase Vegetatif & Generatif"; badgeStatus.className = "px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"; }
                if (alertUltrasonik) alertUltrasonik.classList.add('hidden');
            } else if (hitungHari <= 150) {
                if (barVeg) barVeg.style.width = "70%";
                let progressKering = ((hitungHari - 120) / 30) * 15;
                if (barKering) barKering.style.width = (70 + progressKering) + "%";
                if (barPanen)  barPanen.style.width = "0%";
                if (badgeStatus) { badgeStatus.innerText = "⚠️ Fase Pengeringan (Pematangan)"; badgeStatus.className = "px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200"; }
                if (alertUltrasonik) alertUltrasonik.classList.remove('hidden');
                const currentWaterLevel = document.getElementById('distance')?.innerText || '0';
                const elAlertWater = document.getElementById('alert-water-level');
                if (elAlertWater) elAlertWater.innerText = currentWaterLevel + 'cm';
            } else {
                if (barVeg)    barVeg.style.width    = "70%";
                if (barKering) barKering.style.width = "85%";
                let progressPanen = ((Math.min(hitungHari, 180) - 150) / 30) * 15;
                if (barPanen)  barPanen.style.width = (85 + progressPanen) + "%";
                if (badgeStatus) { badgeStatus.innerText = "🌾 Siap Panen!"; badgeStatus.className = "px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200"; }
                if (alertUltrasonik) alertUltrasonik.classList.add('hidden');
            }
        });

        // ===== HARDWARE CONTROL =====
        window.toggleMode = function () {
            const isManual = document.getElementById('mode-toggle').checked;
            set(ref(db, 'iot/control/pintu_air'), isManual ? 2 : 0);
        };
        window.controlServo = function (val) {
            set(ref(db, 'iot/control/pintu_air'), val);
        };
        window.controlScarecrow = function () {
            set(ref(db, 'iot/control/scarecrow'), 1);
            setTimeout(() => set(ref(db, 'iot/control/scarecrow'), 0), 2000);
            alert("Trigger dikirim!");
        };

        window.refreshRealtimeData = function () {
            if (databaseCache.latest) updateRealtimeUI(databaseCache.latest);
        };

        function updateRealtimeUI(data) {
            const dist = parseFloat(data.water) || 0;

            document.getElementById('alert-water-level') && (document.getElementById('alert-water-level').innerText = dist + 'cm');
            document.getElementById('soil-value')  && (document.getElementById('soil-value').innerText  = data.soil || 0);
            document.getElementById('soil-bar')    && (document.getElementById('soil-bar').style.width  = (data.soil || 0) + '%');
            document.getElementById('distance')    && (document.getElementById('distance').innerText    = data.water || 0);
            document.getElementById('esp32-status') && (document.getElementById('esp32-status').innerText = 'ESP32 Aktif');

            const isBahaya = (data.pir || '').toLowerCase() === 'bahaya';
            const pirStatus = document.getElementById('pir-status');
            const pirDot    = document.getElementById('pir-dot');
            const srv2Badge = document.getElementById('servo2-badge');
            if (pirStatus) { pirStatus.innerText = isBahaya ? 'BAHAYA' : 'AMAN'; pirStatus.className = isBahaya ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-emerald-600'; }
            if (pirDot)    pirDot.className = isBahaya ? 'h-4 w-4 rounded-full bg-red-600 animate-pulse' : 'h-4 w-4 rounded-full bg-emerald-500';
            if (srv2Badge) srv2Badge.innerText = isBahaya ? 'BAHAYA' : 'IDLE';

            // === FLOOD CHECK ===
            updateFloodUI(dist);
        }

        // ===== LISTEN DATA TELEMETRI TERBARU =====
        onValue(ref(db, 'iot/latest'), (snapshot) => {
            const data = snapshot.val();
            if (!data) return;
            databaseCache.latest = data;

            // Update timestamp untuk offline detection
            lastDataTimestamp = Date.now();
            if (activeSawah === 1) updateRealtimeUI(data);

            const now     = new Date();
            const timeStr = now.getHours() + ':' + now.getMinutes().toString().padStart(2,'0') + ':' + now.getSeconds().toString().padStart(2,'0');

            if (chartLabels.length > 12) { chartLabels.shift(); soilData.shift(); }
            if (waterLabels.length > 12) { waterLabels.shift(); waterData.shift(); }

            chartLabels.push(timeStr);
            soilData.push(data.soil || 0);
            waterLabels.push(timeStr);
            waterData.push(data.water || 0);

            // Dynamic point colors for water chart
            waterChart.data.datasets[0].pointBackgroundColor = waterData.map(v => getPointColor(v));
            waterChart.data.datasets[0].borderColor = getPointColor(data.water || 0);

            soilChart.update();
            waterChart.update();
        });

        // ===== LISTEN STATUS KONTROL RELAY/SERVO =====
        onValue(ref(db, 'iot/control/pintu_air'), (snapshot) => {
            if (activeSawah !== 1) return;
            const val = snapshot.val();
            const toggle   = document.getElementById('mode-toggle');
            const btnBuka  = document.getElementById('btn-buka');
            const btnTutup = document.getElementById('btn-tutup');
            const modeLabel = document.getElementById('mode-label');
            const badge     = document.getElementById('servo1-badge');
            const mainMode  = document.getElementById('main-mode-display');

            if (val === 0) {
                if (toggle)    toggle.checked = false;
                if (modeLabel) modeLabel.innerText = 'Otomatis';
                if (mainMode)  mainMode.innerText  = 'OTOMATIS';
                if (badge)     { badge.innerText = 'OTOMATIS'; badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400'; }
                [btnBuka, btnTutup].forEach(b => { if (b) { b.disabled = true; b.className = 'flex-1 py-2 bg-gray-100 text-gray-400 text-[10px] font-bold rounded cursor-not-allowed'; } });
            } else {
                if (toggle)    toggle.checked = true;
                if (modeLabel) modeLabel.innerText = 'Manual';
                if (mainMode)  mainMode.innerText  = 'MANUAL';
                if (badge)     { badge.innerText = 'MANUAL'; badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600'; }
                if (btnBuka)  { btnBuka.disabled = false;  btnBuka.className  = val === 1 ? 'flex-1 py-2 bg-blue-600 text-white text-[10px] font-bold rounded shadow-md' : 'flex-1 py-2 bg-blue-400 text-white text-[10px] font-bold rounded hover:bg-blue-500'; }
                if (btnTutup) { btnTutup.disabled = false; btnTutup.className = val === 2 ? 'flex-1 py-2 bg-gray-800 text-white text-[10px] font-bold rounded shadow-md' : 'flex-1 py-2 bg-gray-500 text-white text-[10px] font-bold rounded hover:bg-gray-600'; }
            }
        });

        // ===== LISTEN LOG AKTIVITAS (SIDEBAR SINGKAT) =====
        onValue(query(ref(db, 'iot/logs'), limitToLast(6)), (snapshot) => {
            const logContainer = document.getElementById('log-container');
            if (!logContainer) return;
            logContainer.innerHTML = '';
            const data = snapshot.val();
            if (data) {
                Object.values(data).reverse().forEach(log => {
                    const isBahaya = (log.pir || '').toLowerCase() === 'bahaya';
                    const waterDist = parseFloat(log.water) || 0;
                    const floodLevel = getFloodLevel(waterDist);
                    const div = document.createElement('div');
                    div.className = `p-3 bg-gray-50 rounded-xl border-l-4 ${isBahaya ? 'border-red-500' : floodLevel === 'kritis' ? 'border-red-400' : floodLevel === 'siaga' ? 'border-amber-400' : 'border-emerald-500'}`;
                    div.innerHTML = `
                        <div class="flex justify-between mb-1">
                            <span class="text-[10px] text-gray-400">${formatWaktu(log.created_at)}</span>
                            <span class="font-bold text-[10px] ${isBahaya ? 'text-red-600' : 'text-emerald-600'}">${log.pir}</span>
                        </div>
                        <p class="text-[11px] text-gray-600">Tanah: <b>${log.soil}%</b> | Air: <b>${log.water}cm</b>
                            ${floodLevel === 'kritis' ? '<span class="text-red-500 font-bold ml-1">🚨 Kritis</span>' : floodLevel === 'siaga' ? '<span class="text-amber-500 font-bold ml-1">⚠️ Siaga</span>' : ''}
                        </p>
                    `;
                    logContainer.appendChild(div);
                });
            }
        });

        // ===== TABEL HISTORI DATA + FILTER + PAGINASI =====
        const PER_PAGE = 10;
        let allEntries      = [];
        let filteredEntries = [];
        let currentPage     = 1;
        let activeFilter    = 'semua';

        const tbody         = document.getElementById('log-table-body');
        const logTableCount = document.getElementById('log-table-count');
        const pageInfo      = document.getElementById('page-info');
        const paginationBtns = document.getElementById('pagination-btns');

        function formatWaktu(ts) {
            if (!ts) return '—';
            const d = new Date(Number(ts));
            if (isNaN(d)) return '—';
            return d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        window.setFilter = function (f) {
            activeFilter = f;
            ['hari','minggu','bulan','tahun','semua'].forEach(k => {
                const el = document.getElementById('f-' + k);
                if (el) el.className = k === f
                    ? 'filter-tab active px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent'
                    : 'filter-tab px-3 py-1.5 rounded-lg text-xs font-semibold border border-transparent text-gray-500';
            });
            applyFilter();
        };

        function applyFilter() {
            const now = new Date();
            filteredEntries = allEntries.filter(log => {
                if (activeFilter === 'semua') return true;
                if (!log.created_at) return false;
                const d = new Date(Number(log.created_at));
                if (isNaN(d)) return false;
                if (activeFilter === 'hari') {
                    const s = new Date(); s.setHours(0,0,0,0);
                    const e = new Date(); e.setHours(23,59,59,999);
                    return d >= s && d <= e;
                }
                if (activeFilter === 'minggu') {
                    const s = new Date(); s.setHours(0,0,0,0);
                    const diff = (s.getDay()===0)?6:s.getDay()-1;
                    s.setDate(s.getDate()-diff);
                    const e = new Date(s); e.setDate(s.getDate()+6); e.setHours(23,59,59,999);
                    return d >= s && d <= e;
                }
                if (activeFilter === 'bulan') {
                    const s = new Date(now.getFullYear(), now.getMonth(), 1, 0,0,0,0);
                    const e = new Date(now.getFullYear(), now.getMonth()+1, 0, 23,59,59,999);
                    return d >= s && d <= e;
                }
                if (activeFilter === 'tahun') {
                    const s = new Date(now.getFullYear(), 0, 1, 0,0,0,0);
                    const e = new Date(now.getFullYear(), 11, 31, 23,59,59,999);
                    return d >= s && d <= e;
                }
                return true;
            });
            currentPage = 1;
            renderTable();
        }

        // ===== CETAK LAPORAN =====
        window.cetakLaporan = function () {
            if (!tbody) return;
            applyFilter();
            const labelMap = { hari:'Hari Ini', minggu:'Minggu Ini', bulan:'Bulan Ini', tahun:'Tahun Ini', semua:'Semua Data' };
            const now = new Date();
            function formatTgl(d) { return d.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }); }
            let rentangTgl = '';
            if (activeFilter==='hari') { rentangTgl = formatTgl(now); }
            else if (activeFilter==='minggu') {
                const s = new Date(now); const day = s.getDay(); const diff=(day===0)?6:day-1; s.setDate(now.getDate()-diff); s.setHours(0,0,0,0);
                const e = new Date(s); e.setDate(s.getDate()+6);
                rentangTgl = formatTgl(s)+' – '+formatTgl(e);
            } else if (activeFilter==='bulan') {
                rentangTgl = formatTgl(new Date(now.getFullYear(),now.getMonth(),1))+' – '+formatTgl(new Date(now.getFullYear(),now.getMonth()+1,0));
            } else if (activeFilter==='tahun') {
                rentangTgl = '1 Januari '+now.getFullYear()+' – 31 Desember '+now.getFullYear();
            } else if (filteredEntries.length > 0) {
                const dates = filteredEntries.map(e=>e.created_at?new Date(Number(e.created_at)):null).filter(d=>d&&!isNaN(d)).sort((a,b)=>a-b);
                if (dates.length>0) rentangTgl = formatTgl(dates[0])+' – '+formatTgl(dates[dates.length-1]);
            }
            const nowStr = now.toLocaleString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});
            const ph = document.getElementById('print-header'); if (ph) ph.style.display='block';
            const pf = document.getElementById('print-footer'); if (pf) pf.style.display='block';
            const pt = document.getElementById('print-title');  if (pt) pt.innerText='Laporan Log Aktivitas — '+labelMap[activeFilter];
            const ps = document.getElementById('print-subtitle'); if (ps) ps.innerText='Lahan: Sawah 1 (Blok A) · Periode: '+(rentangTgl||'-')+' · Total: '+filteredEntries.length+' entri';
            const pg = document.getElementById('print-generated'); if (pg) pg.innerText='Dicetak pada: '+nowStr;
            const ptt = document.getElementById('print-total'); if (ptt) ptt.innerText='Total '+filteredEntries.length+' entri';
            const savedPage = currentPage;
            renderAllForPrint();
            window.print();
            setTimeout(() => {
                if (ph) ph.style.display='none';
                if (pf) pf.style.display='none';
                currentPage = savedPage;
                renderTable();
            }, 500);
        };

        function renderAllForPrint() {
            if (!tbody) return;
            tbody.innerHTML = '';
            if (filteredEntries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="padding:2rem;text-align:center;color:#9ca3af;font-style:italic;">Tidak ada data untuk periode ini.</td></tr>';
                return;
            }
            filteredEntries.forEach((log, i) => {
                const isBahaya   = (log.pir||'').toLowerCase()==='bahaya';
                const waterDist  = parseFloat(log.water)||0;
                const floodLevel = getFloodLevel(waterDist);
                const floodColor = floodLevel==='kritis'?'#b91c1c':floodLevel==='siaga'?'#92400e':'#374151';
                const tr = document.createElement('tr');
                tr.style.background = i%2===0?'#fff':'#f9fafb';
                tr.innerHTML = `
                    <td style="padding:8px 24px;font-size:12px;color:#6b7280;">
                        <i class="fas fa-calendar-alt" style="margin-right:4px;color:#d1d5db;"></i>
                        ${formatWaktu(log.created_at)}
                    </td>
                    <td style="padding:8px 24px;text-align:center;">
                        <span style="padding:2px 10px;border-radius:999px;font-size:10px;font-weight:700;background:${isBahaya?'#fee2e2':'#d1fae5'};color:${isBahaya?'#b91c1c':'#065f46'};">
                            ${isBahaya?'Bahaya':'Aman'}
                        </span>
                    </td>
                    <td style="padding:8px 24px;text-align:center;font-weight:600;color:#374151;">${log.soil??'—'}</td>
                    <td style="padding:8px 24px;text-align:center;font-weight:700;color:${floodColor};">
                        ${log.water!=null?parseFloat(log.water).toFixed(1):'—'}
                        ${floodLevel==='kritis'?' 🚨':floodLevel==='siaga'?' ⚠️':''}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function renderTable() {
            if (!tbody) return;
            const totalPages = Math.max(1, Math.ceil(filteredEntries.length / PER_PAGE));
            currentPage = Math.min(currentPage, totalPages);
            const start = (currentPage - 1) * PER_PAGE;
            const pageEntries = filteredEntries.slice(start, start + PER_PAGE);

            tbody.innerHTML = '';
            if (pageEntries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">Tidak ada data untuk periode ini.</td></tr>';
            } else {
                pageEntries.forEach((log, i) => {
                    const isBahaya   = (log.pir||'').toLowerCase()==='bahaya';
                    const waterDist  = parseFloat(log.water)||0;
                    const floodLevel = getFloodLevel(waterDist);
                    const tr = document.createElement('tr');
                    tr.className = i%2===0?'bg-white':'bg-gray-50/40';
                    tr.innerHTML = `
                        <td class="px-6 py-3 text-gray-500 text-xs whitespace-nowrap overflow-hidden text-ellipsis">
                            <i class="fas fa-calendar-alt mr-1 text-gray-300"></i>${formatWaktu(log.created_at)}
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold ${isBahaya?'bg-red-100 text-red-700':'bg-emerald-100 text-emerald-700'}">
                                ${isBahaya?'Bahaya':'Aman'}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center font-semibold text-gray-700">${log.soil??'—'}</td>
                        <td class="px-6 py-3 text-center font-semibold ${floodLevel==='kritis'?'text-red-600':floodLevel==='siaga'?'text-amber-600':'text-gray-700'}">
                            ${log.water!=null?parseFloat(log.water).toFixed(1):'—'}
                            ${floodLevel==='kritis'?'<span class="ml-1 text-[9px]">🚨</span>':floodLevel==='siaga'?'<span class="ml-1 text-[9px]">⚠️</span>':''}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            const from = filteredEntries.length===0?0:start+1;
            const to   = Math.min(start+PER_PAGE, filteredEntries.length);
            if (pageInfo)      pageInfo.textContent = `${from}–${to} dari ${filteredEntries.length} entri`;
            if (logTableCount) logTableCount.textContent = filteredEntries.length + ' entri';
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            if (!paginationBtns) return;
            paginationBtns.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '<i class="fas fa-chevron-left" style="font-size:11px;"></i>';
            prevBtn.className = 'pagination-btn';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => { if (currentPage>1) { currentPage--; renderTable(); } };
            paginationBtns.appendChild(prevBtn);

            const maxVisible = 5;
            let startP = Math.max(1, currentPage - Math.floor(maxVisible/2));
            let endP   = Math.min(totalPages, startP + maxVisible - 1);
            if (endP - startP < maxVisible-1) startP = Math.max(1, endP - maxVisible + 1);
            if (startP > 1) { appendPageBtn(1); if (startP > 2) appendDots(); }
            for (let p = startP; p <= endP; p++) appendPageBtn(p);
            if (endP < totalPages) { if (endP < totalPages-1) appendDots(); appendPageBtn(totalPages); }

            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '<i class="fas fa-chevron-right" style="font-size:11px;"></i>';
            nextBtn.className = 'pagination-btn';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => { if (currentPage<totalPages) { currentPage++; renderTable(); } };
            paginationBtns.appendChild(nextBtn);
        }

        function appendPageBtn(p) {
            const btn = document.createElement('button');
            btn.textContent = p;
            btn.className = 'pagination-btn' + (p===currentPage?' active':'');
            btn.onclick = () => { currentPage = p; renderTable(); };
            paginationBtns.appendChild(btn);
        }

        function appendDots() {
            const dots = document.createElement('span');
            dots.textContent = '...';
            dots.style.cssText = 'font-size:13px;color:#9ca3af;padding:0 4px;';
            paginationBtns.appendChild(dots);
        }

        onValue(query(ref(db, 'iot/logs'), limitToLast(500)), (snapshot) => {
            const data = snapshot.val();
            allEntries = data ? Object.values(data).reverse() : [];
            applyFilter();
        });

        // ===== DIGITAL CLOCK =====
        setInterval(() => {
            const elTime = document.getElementById('current-time');
            if (elTime) elTime.innerText = new Date().toLocaleString('id-ID', { weekday:'long', day:'numeric', month:'long', hour:'2-digit', minute:'2-digit' });
        }, 1000);
    </script>
</body>
</html>