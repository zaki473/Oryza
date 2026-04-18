<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartOryza - Dashboard Monitor Profesional</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js for Graphical History -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jsPDF & AutoTable untuk Generate Laporan PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:['"Plus Jakarta Sans"', 'sans-serif'],
                        serif:['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            700: '#15803d',
                            900: '#2b5329', 
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #f8fafc; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .stat-card { transition: all 0.2s ease-in-out; }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
        }
        .progress-bar-animated { transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Custom scrollbar for table */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; rounded-full; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="min-h-screen text-gray-800 antialiased pb-12">

    <!-- Navbar -->
    <nav class="glass-nav border-b border-gray-100 sticky top-0 z-50 py-4 px-6 md:px-10 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center shadow-md">
                <span class="text-xl text-white">🌾</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight font-serif text-brand-900 hidden sm:block">
                Smart<span class="text-brand-500">Oryza</span>
            </h1>
        </div>
        <div class="flex items-center gap-5">
            <div class="hidden md:block text-right">
                <p class="text-sm font-semibold text-gray-800 capitalize">{{ Auth::user()->name ?? 'Petani Modern' }}</p>
                <p class="text-xs text-gray-500" id="current-time">Loading time...</p>
            </div>
            <div class="w-px h-8 bg-gray-200 hidden md:block"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold transition-colors border border-red-100">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 md:px-10 py-8">
        
        <!-- Header & Global Actions -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Dashboard Irigasi</h2>
                <p class="text-gray-500 font-medium">Monitoring real-time & riwayat aktivitas sistem IoT</p>
            </div>
            <div class="flex items-center gap-3 relative">
                <div class="bg-emerald-50 border border-emerald-200 px-4 py-2.5 rounded-xl flex items-center gap-3 shadow-sm" id="alert-container">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <p class="text-sm font-semibold text-emerald-700 hidden sm:block">Sistem Normal</p>
                </div>
                
                <!-- DROPDOWN UNDUH LAPORAN -->
                <div class="relative group">
                    <button id="main-btn-download" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all flex items-center gap-2 active:scale-95">
                        <i class="fas fa-download" id="main-download-icon"></i>
                        <span class="hidden sm:inline" id="main-download-text">Unduh Laporan</span>
                        <i class="fas fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                    <!-- Kotak Menu Dropdown -->
                    <div class="absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden transform origin-top-right group-hover:scale-100 scale-95">
                        <div class="p-1">
                            <button onclick="downloadCSV()" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-700 rounded-lg flex items-center gap-3 transition-colors">
                                <i class="fas fa-file-excel text-emerald-600 text-lg"></i> 
                                <div>
                                    <p class="font-bold">Format Excel</p>
                                    <p class="text-xs text-gray-500 font-normal">Data mentah (.csv)</p>
                                </div>
                            </button>
                            <button onclick="downloadPDF()" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-lg flex items-center gap-3 transition-colors mt-1">
                                <i class="fas fa-file-pdf text-red-500 text-lg"></i> 
                                <div>
                                    <p class="font-bold">Format PDF</p>
                                    <p class="text-xs text-gray-500 font-normal">Dokumen rapi (.pdf)</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- END DROPDOWN -->

            </div>
        </div>

        <!-- FITUR BARU: TIMELINE MASA TANAM -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-6 relative overflow-hidden group">
            <!-- Dekorasi Background -->
            <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-amber-50 to-transparent"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-brand-600"></i> Siklus Tanam Padi
                    </h3>
                    <p class="text-sm text-gray-500 font-medium">Estimasi Panen: 15 Juni 2026</p>
                </div>
                <div class="bg-amber-100 border border-amber-200 text-amber-800 px-4 py-2 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 animate-pulse">
                    <i class="fas fa-exclamation-circle"></i>
                    Fase Pengeringan (Pematangan)
                </div>
            </div>

            <!-- Progress Bar Tracker -->
            <div class="relative w-full h-3 bg-gray-100 rounded-full mt-6 mb-2 overflow-hidden flex">
                <!-- Fase 4 Bulan Pertama (Irigasi) -->
                <div class="h-full bg-brand-500 w-[66.6%] relative border-r-2 border-white"></div>
                <!-- Fase 2 Bulan Terakhir (Pengeringan) -->
                <div class="h-full bg-amber-400 w-[33.4%] relative">
                    <!-- Indikator Posisi Saat ini (Titik Berjalan) -->
                    <div class="absolute top-1/2 left-[40%] -translate-y-1/2 -translate-x-1/2 w-4 h-4 bg-white border-4 border-amber-600 rounded-full shadow-md z-20"></div>
                </div>
            </div>

            <div class="flex justify-between text-xs font-bold text-gray-400 uppercase tracking-wider">
                <span>Mulai Tanam (Hari 1)</span>
                <span class="text-brand-600 ml-16">Fase Vegetatif & Generatif</span>
                <span class="text-amber-600">Stop Irigasi (Hari 120)</span>
                <span>Panen (Hari 180)</span>
            </div>

            <!-- Analisis Ultrasonik Khusus Fase Ini -->
            <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                    <i class="fas fa-water"></i>
                </div>
                <p class="text-sm font-medium text-gray-600">
                    <span class="font-bold text-red-600">Cek Ultrasonik:</span> Terdapat genangan air 12cm. Karena saat ini fase pengeringan, segera pastikan saluran pembuangan air sawah terbuka!
                </p>
            </div>
        </div>

        <!-- ROW 1: Quick Stats (4 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

            <!-- 1. Soil Moisture -->
            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-tint text-blue-500 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-1 rounded-md">V1</span>
                </div>
                <p class="text-sm font-medium text-gray-500">Kelembapan Tanah</p>
                <div class="flex items-baseline gap-1 mt-1">
                    <h3 class="text-4xl font-extrabold text-gray-900" id="soil-value">68</h3>
                    <span class="text-lg font-semibold text-gray-500">%</span>
                </div>
                <div class="mt-5 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 progress-bar-animated w-[68%]" id="soil-bar"></div>
                </div>
            </div>

            <!-- 2. Water Level -->
            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-cyan-50 flex items-center justify-center">
                        <i class="fas fa-water text-cyan-500 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-1 rounded-md">V2</span>
                </div>
                <p class="text-sm font-medium text-gray-500">Jarak Permukaan Air</p>
                <div class="flex items-baseline gap-1 mt-1">
                    <h3 class="text-4xl font-extrabold text-gray-900" id="distance">45</h3>
                    <span class="text-lg font-semibold text-gray-500">cm</span>
                </div>
                <p class="text-xs text-gray-400 mt-4"><i class="fas fa-arrow-up text-emerald-500 mr-1"></i> Naik 2cm dari kemarin</p>
            </div>

            <!-- 3. PIR Detection -->
            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                        <i class="fas fa-walking text-orange-500 text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-1 rounded-md">V3</span>
                </div>
                <p class="text-sm font-medium text-gray-500">Deteksi Hama (PIR)</p>
                <div class="mt-3 flex items-center gap-3">
                    <span class="relative flex h-4 w-4">
                        <span id="pir-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span id="pir-dot" class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500"></span>
                    </span>
                    <h3 class="text-xl font-bold text-emerald-600" id="pir-status">Aman</h3>
                </div>
                <p class="text-xs text-gray-400 mt-4">Terakhir terdeteksi: 12:45 WIB</p>
            </div>

            <!-- 4. Weather Widget -->
            <div class="stat-card bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-6 shadow-md text-white relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-white/20 text-6xl"><i class="fas fa-cloud-sun"></i></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-blue-100 mb-1">Cuaca Sawah Saat Ini</p>
                    <div class="flex items-center gap-3 mt-2">
                        <i class="fas fa-cloud-sun text-4xl text-yellow-300"></i>
                        <h3 class="text-4xl font-extrabold">28°<span class="text-2xl font-semibold">C</span></h3>
                    </div>
                    <div class="mt-4 flex gap-4 text-xs text-blue-100 font-medium">
                        <span><i class="fas fa-droplet mr-1"></i> Kelembapan: 75%</span>
                        <span><i class="fas fa-wind mr-1"></i> Angin: 12 km/h</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: Charts & Controls -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Graphical History Chart -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Grafik Kelembapan & Air</h3>
                        <p class="text-sm text-gray-500">Data 7 hari terakhir</p>
                    </div>
                    <select class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block p-2 outline-none">
                        <option>Minggu Ini</option>
                        <option>Bulan Ini</option>
                    </select>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="historyChart"></canvas>
                </div>
            </div>

            <!-- Control Panels -->
            <div class="space-y-6">
                <!-- System Mode -->
                <div class="bg-brand-900 rounded-2xl p-6 shadow-md text-white relative overflow-hidden">
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <p class="text-sm font-medium text-brand-100">Mode Sistem</p>
                            <h3 class="text-2xl font-bold mt-1">OTOMATIS</h3>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-microchip text-xl text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Pump Control -->
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center" id="pump-icon-bg">
                                <i class="fas fa-faucet text-blue-500"></i>
                            </div>
                            <div>
                                <h3 class="text-md font-bold text-gray-900">Pompa Air</h3>
                                <p class="text-xs text-gray-500" id="pump-text">Status: MATI</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="pump-switch" class="sr-only peer" onchange="togglePump()">
                            <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                        </label>
                    </div>
                </div>

                <!-- Scarecrow Control -->
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center">
                                <span class="text-xl" id="scarecrow-emoji">🦅</span>
                            </div>
                            <div>
                                <h3 class="text-md font-bold text-gray-900">Pengusir Hama</h3>
                                <p class="text-xs text-gray-500" id="scarecrow-text">Status: NONAKTIF</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="scarecrow-switch" class="sr-only peer" onchange="toggleScarecrow()">
                            <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 3: Data Table (Riwayat Aktivitas) -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Aktivitas Sistem</h3>
                    <p class="text-sm text-gray-500">Log kejadian dan aktuasi perangkat harian</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="logTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4 font-semibold">Waktu</th>
                            <th class="px-6 py-4 font-semibold">Perangkat / Sensor</th>
                            <th class="px-6 py-4 font-semibold">Deskripsi Aktivitas</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700" id="activity-log-body">
                        <!-- Default Rows -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">Hari ini, 12:45</td>
                            <td class="px-6 py-4 flex items-center gap-2"><i class="fas fa-walking text-orange-500"></i> Sensor PIR</td>
                            <td class="px-6 py-4">Mendeteksi pergerakan burung di Sektor Utara</td>
                            <td class="px-6 py-4 text-center"><span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Peringatan</span></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">Hari ini, 08:00</td>
                            <td class="px-6 py-4 flex items-center gap-2"><i class="fas fa-faucet text-blue-500"></i> Pompa Air</td>
                            <td class="px-6 py-4">Pompa dimatikan (Kelembapan mencapai target 70%)</td>
                            <td class="px-6 py-4 text-center"><span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Sukses</span></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">Hari ini, 07:15</td>
                            <td class="px-6 py-4 flex items-center gap-2"><i class="fas fa-faucet text-blue-500"></i> Pompa Air</td>
                            <td class="px-6 py-4">Pompa dihidupkan otomatis (Kelembapan drop 45%)</td>
                            <td class="px-6 py-4 text-center"><span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Sukses</span></td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">Kemarin, 18:30</td>
                            <td class="px-6 py-4 flex items-center gap-2"><i class="fas fa-wifi text-gray-400"></i> Sistem IoT</td>
                            <td class="px-6 py-4">Kalibrasi sensor ultrasonik V2 selesai</td>
                            <td class="px-6 py-4 text-center"><span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">Info</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        // Update Time
        function updateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Chart.js Configuration
        const ctx = document.getElementById('historyChart').getContext('2d');
        const gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        const gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
        gradientGreen.addColorStop(0, 'rgba(34, 197, 94, 0.5)');
        gradientGreen.addColorStop(1, 'rgba(34, 197, 94, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels:['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets:[
                    {
                        label: 'Kelembapan Tanah (%)',
                        data:[65, 59, 80, 81, 56, 55, 68],
                        borderColor: '#3b82f6',
                        backgroundColor: gradientBlue,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    },
                    {
                        label: 'Level Air (cm)',
                        data:[28, 48, 40, 19, 86, 27, 45],
                        borderColor: '#22c55e',
                        backgroundColor: gradientGreen,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#22c55e',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f1f5f9' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                },
                interaction: { mode: 'index', intersect: false },
            }
        });

        // System Controls
        function addLogEntry(deviceIcon, deviceColor, deviceName, activity) {
            const tbody = document.getElementById('activity-log-body');
            const now = new Date();
            const timeString = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
            
            const newRow = document.createElement('tr');
            newRow.className = "bg-green-50/50 hover:bg-gray-50 transition-colors";
            newRow.innerHTML = `
                <td class="px-6 py-4 font-medium text-gray-900">Baru saja, ${timeString}</td>
                <td class="px-6 py-4 flex items-center gap-2"><i class="fas ${deviceIcon} ${deviceColor}"></i> ${deviceName}</td>
                <td class="px-6 py-4">${activity}</td>
                <td class="px-6 py-4 text-center"><span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Manual</span></td>
            `;
            
            tbody.insertBefore(newRow, tbody.firstChild);
            setTimeout(() => { newRow.classList.remove('bg-green-50/50'); }, 2000);
        }

        function togglePump() {
            const isChecked = document.getElementById('pump-switch').checked;
            const text = document.getElementById('pump-text');
            const iconBg = document.getElementById('pump-icon-bg');

            if (isChecked) {
                text.textContent = "Status: AKTIF";
                text.classList.add('text-blue-600', 'font-bold');
                iconBg.classList.add('animate-pulse', 'ring-4', 'ring-blue-100');
                addLogEntry('fa-faucet', 'text-blue-500', 'Pompa Air', 'Dihidupkan secara manual lewat Dashboard');
            } else {
                text.textContent = "Status: MATI";
                text.classList.remove('text-blue-600', 'font-bold');
                iconBg.classList.remove('animate-pulse', 'ring-4', 'ring-blue-100');
                addLogEntry('fa-faucet', 'text-blue-500', 'Pompa Air', 'Dimatikan secara manual lewat Dashboard');
            }
        }

        function toggleScarecrow() {
            const isChecked = document.getElementById('scarecrow-switch').checked;
            const text = document.getElementById('scarecrow-text');
            const emoji = document.getElementById('scarecrow-emoji');

            if (isChecked) {
                text.textContent = "Status: AKTIF";
                text.classList.add('text-brand-600', 'font-bold');
                emoji.classList.add('animate-bounce');
                addLogEntry('fa-crow', 'text-amber-500', 'Pengusir Hama', 'Diaktifkan paksa (Manual Override)');
            } else {
                text.textContent = "Status: NONAKTIF";
                text.classList.remove('text-brand-600', 'font-bold');
                emoji.classList.remove('animate-bounce');
                addLogEntry('fa-crow', 'text-amber-500', 'Pengusir Hama', 'Dinonaktifkan secara manual');
            }
        }

        // ============================================
        // DOWNLOAD REPORTS (CSV & PDF)
        // ============================================

        function animateDownloadButton() {
            const btnIcon = document.getElementById('main-download-icon');
            const btnText = document.getElementById('main-download-text');
            
            btnIcon.className = "fas fa-spinner fa-spin text-brand-600";
            btnText.textContent = "Memproses...";
            
            return { btnIcon, btnText };
        }

        function restoreDownloadButton(btnIcon, btnText) {
            btnIcon.className = "fas fa-check text-emerald-500";
            btnText.textContent = "Selesai!";
            
            setTimeout(() => {
                btnIcon.className = "fas fa-download";
                btnText.textContent = "Unduh Laporan";
            }, 2000);
        }

        function getFormattedDate() {
            const d = new Date();
            return `${d.getFullYear()}${(d.getMonth()+1).toString().padStart(2, '0')}${d.getDate().toString().padStart(2, '0')}`;
        }

        // 1. Download CSV
        function downloadCSV() {
            const { btnIcon, btnText } = animateDownloadButton();

            setTimeout(() => {
                const rows = document.querySelectorAll('#activity-log-body tr');
                let csvContent = "Waktu Aktivitas,Perangkat/Sensor,Deskripsi Aktivitas,Status\n";

                rows.forEach(row => {
                    const cols = row.querySelectorAll('td');
                    if (cols.length > 0) {
                        const waktu = cols[0].innerText.replace(/,/g, ' -').trim();
                        const perangkat = cols[1].innerText.trim();
                        const aktivitas = cols[2].innerText.trim();
                        const status = cols[3].innerText.trim();
                        csvContent += `"${waktu}","${perangkat}","${aktivitas}","${status}"\n`;
                    }
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                link.setAttribute("href", URL.createObjectURL(blob));
                link.setAttribute("download", `Laporan_SmartOryza_${getFormattedDate()}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                restoreDownloadButton(btnIcon, btnText);
            }, 800);
        }

// 2. Download PDF Laporan Lengkap (Executive Summary)
        function downloadPDF() {
            const { btnIcon, btnText } = animateDownloadButton();

            setTimeout(() => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4'); // Kertas A4
                let currentY = 0;

                // ==========================================
                // HALAMAN 1: EXECUTIVE SUMMARY
                // ==========================================

                // --- KOP SURAT ---
                doc.setFont("helvetica", "bold");
                doc.setFontSize(18);
                doc.setTextColor(43, 83, 41); // Forest Green
                doc.text("Laporan Analitik Sistem SmartOryza", 14, 22);
                
                doc.setFont("helvetica", "normal");
                doc.setFontSize(10);
                doc.setTextColor(100, 100, 100);
                const dateString = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                doc.text(`Periode Laporan: 7 Hari Terakhir  |  Dicetak: ${dateString}`, 14, 28);
                
                doc.setDrawColor(200, 200, 200);
                doc.line(14, 32, 196, 32); // Garis pemisah

                // --- 1. RINGKASAN IRIGASI ---
                doc.setFont("helvetica", "bold");
                doc.setFontSize(12);
                doc.setTextColor(20, 20, 20);
                doc.text("1. Ringkasan Irigasi & Pompa Air", 14, 42);
                
                doc.setFont("helvetica", "normal");
                doc.setFontSize(10);
                doc.setTextColor(60, 60, 60);
                doc.text("• Total Durasi Pompa Menyala : 42 Jam 15 Menit", 18, 48);
                doc.text("• Estimasi Volume Air Keluar : 14.500 Liter", 18, 54);
                doc.text("• Rata-rata Irigasi Harian   : 2.071 Liter / Hari", 18, 60);

                // --- 2. RATA-RATA SENSOR LINGKUNGAN ---
                doc.setFont("helvetica", "bold");
                doc.setFontSize(12);
                doc.setTextColor(20, 20, 20);
                doc.text("2. Pantauan Kondisi Lahan (Data Sensor)", 14, 72);

                doc.autoTable({
                    startY: 76,
                    head: [['Indikator', 'Nilai Tertinggi', 'Nilai Terendah', 'Rata-rata Harian']],
                    body: [['Kelembapan Tanah', '85% (Rabu)', '42% (Senin)', '68% (Optimal)'],['Suhu Udara', '34°C (Siang)', '24°C (Malam)', '28°C (Normal)'],
                        ['Level Genangan Air', '50 cm', '15 cm', '32 cm']
                    ],
                    theme: 'grid',
                    headStyles: { fillColor:[43, 83, 41] }, // Hijau Tema
                    margin: { left: 14, right: 14 }
                });

                currentY = doc.lastAutoTable.finalY + 12;

                // --- 3. ANALISIS GANGGUAN HAMA ---
                doc.setFont("helvetica", "bold");
                doc.setFontSize(12);
                doc.setTextColor(20, 20, 20);
                doc.text("3. Analisis Gangguan Hama (Sensor PIR)", 14, currentY);
                
                doc.setFont("helvetica", "normal");
                doc.setFontSize(10);
                doc.setTextColor(60, 60, 60);
                doc.text("• Total Deteksi Pergerakan   : 28 Kali dalam seminggu", 18, currentY + 6);
                doc.text("• Jam Rawan Hama Burung      : 05:30 - 07:00 WIB & 16:30 - 18:00 WIB", 18, currentY + 12);
                doc.text("• Status Pengusir Hama       : Bekerja Normal (Otomatis aktuasi)", 18, currentY + 18);

                currentY += 30;

                // --- 4. CATATAN KERUSAKAN / ERROR LOG ---
                doc.setFont("helvetica", "bold");
                doc.setFontSize(12);
                doc.setTextColor(20, 20, 20);
                doc.text("4. Catatan Pemeliharaan & Error Sistem", 14, currentY);

                doc.autoTable({
                    startY: currentY + 4,
                    head: [['Tanggal / Jam', 'Komponen', 'Status / Keterangan Error']],
                    body:[['16 April 2026, 02:15', 'Koneksi WiFi', 'Koneksi ke server terputus 15 menit (Auto-reconnect: Sukses)'],['15 April 2026, 10:00', 'Sensor V2 (Air)', 'Kalibrasi ulang jarak ultrasonik dilakukan oleh Petani.'],['12 April 2026, 14:30', 'Pompa Air V4', 'Suhu mesin pompa memanas. Mati otomatis untuk pendinginan.']
                    ],
                    theme: 'grid',
                    headStyles: { fillColor:[220, 38, 38] }, // Merah untuk log error
                    margin: { left: 14, right: 14 }
                });


                // ==========================================
                // HALAMAN 2: LOG AKTIVITAS (Tabel HTML)
                // ==========================================
                doc.addPage(); // Tambah Halaman Baru

                doc.setFont("helvetica", "bold");
                doc.setFontSize(14);
                doc.setTextColor(43, 83, 41);
                doc.text("Lampiran: Log Aktivitas Sistem Detail", 14, 20);
                
                // Ambil tabel HTML yang ada di dashboard
                doc.autoTable({
                    html: '#logTable',
                    startY: 26,
                    theme: 'striped',
                    headStyles: { fillColor:[71, 85, 105] }, // Abu-abu profesional
                    styles: { fontSize: 9, cellPadding: 3 },
                    margin: { left: 14, right: 14 }
                });

                // --- TANDA TANGAN (Pengesahan) ---
                currentY = doc.lastAutoTable.finalY + 20;
                if(currentY > 260) { doc.addPage(); currentY = 20; } // Jika mentok bawah, pindah halaman

                doc.setFont("helvetica", "normal");
                doc.setFontSize(10);
                doc.setTextColor(20, 20, 20);
                doc.text("Mengetahui,", 150, currentY);
                doc.text("Pengelola Sistem Cerdas", 142, currentY + 25);


                // ==========================================
                // FINISH & DOWNLOAD
                // ==========================================
                doc.save(`Laporan_Eksekutif_SmartOryza_${getFormattedDate()}.pdf`);

                restoreDownloadButton(btnIcon, btnText);
            }, 1000); // Simulasi render 1 detik
        }
    </script>
</body>
</html>