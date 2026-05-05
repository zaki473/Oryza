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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

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

        .progress-bar-animated {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>

<body class="min-h-screen text-gray-800 antialiased pb-12">

    <nav
        class="glass-nav border-b border-gray-100 sticky top-0 z-50 py-4 px-6 md:px-10 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center shadow-md">
                <span class="text-xl text-white">🌾</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight font-serif text-brand-900">
                Smart<span class="text-brand-500">Oryza</span>
            </h1>
        </div>
        <div class="flex items-center gap-5">
            <div class="text-right hidden sm:block">
                <p id="username" class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Petani Modern' }}</p>
                <p class="text-xs text-gray-500" id="current-time">Loading...</p>
            </div>
            <a href="{{ route('home') }}"
            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold border">
                Home
            </a>
            <button onclick="logoutFirebase()"
                class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-bold border border-red-100">
                Keluar
            </button>
        </div>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <main class="max-w-7xl mx-auto px-6 md:px-10 py-8">

        <div class="mb-8 flex flex-col md:flex-row justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Monitoring Lahan Padi</h2>
                <p class="text-gray-500">Sistem Otomasi Irigasi & Pengusir Hama</p>
            </div>
            <div class="flex gap-3">
                <div id="alert-container"
                    class="bg-white border px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm">
                    <span class="relative flex h-3 w-3">
                        <span id="system-dot-ping" class="animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span id="system-dot" class="relative h-3 w-3 bg-emerald-500 rounded-full"></span>
                    </span>
                    <p id="system-status" class="text-sm font-bold text-emerald-700">Sistem Online</p>
                </div>
                <button onclick="downloadPDF()"
                    class="bg-brand-900 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md hover:bg-brand-700 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i>Laporan
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Siklus Tanam Padi (6 Bulan)</h3>
                    <p class="text-sm text-gray-500" id="planting-status">Status: Belum Dimulai</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase">Sisa Waktu</p>
                        <p class="text-xl font-black text-brand-900" id="countdown-timer">-- Hari lagi</p>
                    </div>
                    <button id="btn-planting" onclick="togglePlanting()"
                        class="bg-brand-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-brand-700 transition-all active:scale-95">
                        Mulai Tanam
                    </button>
                </div>
                <div id="dry-phase-alert"
                    class="hidden bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl shadow-sm animate-pulse">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                        <div>
                            <p class="text-amber-800 font-bold">Peringatan: Fase Pengeringan (Bulan ke-5 & 6)</p>
                            <p class="text-amber-700 text-xs">Pastikan saluran irigasi ditutup untuk pematangan bulir padi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex justify-between text-xs font-bold mb-2">
                    <span id="start-date-label">Tgl Mulai: -</span>
                    <span id="progress-percent">0%</span>
                    <span id="end-date-label">Estimasi Panen: -</span>
                </div>
                <div class="w-full h-4 bg-gray-100 rounded-full overflow-hidden">
                    <div id="planting-progress-bar" class="h-full bg-brand-500 transition-all duration-1000"
                        style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm font-medium text-gray-500 mb-2">Kelembapan Tanah</p>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-4xl font-extrabold text-gray-900" id="soil-value">0</h3>
                    <span class="text-lg text-gray-500 font-bold">%</span>
                </div>
                <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div id="soil-bar" class="h-full bg-blue-500 progress-bar-animated w-0"></div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
    <div class="flex justify-between items-start mb-2">
        <p class="text-sm font-medium text-gray-500">Irigasi (Pintu Air)</p>
        <span id="servo1-badge"
            class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400">OTOMATIS</span>
    </div>
    <div class="flex items-baseline gap-1">
        <h3 class="text-4xl font-extrabold text-gray-900" id="distance">0</h3>
        <span class="text-lg text-gray-500 font-bold">cm</span>
    </div>
    <p class="text-xs text-gray-400 mt-2 italic">D1 Irigasi</p>

    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
        <span class="text-xs font-bold text-gray-400">MODE:</span>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="mode-toggle" class="sr-only peer" onchange="toggleMode()">
            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
            <span class="ml-2 text-[10px] font-bold text-gray-500" id="mode-label">Otomatis</span>
        </label>
    </div>

    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between" id="manual-control-section">
        <span class="text-xs font-bold text-gray-400">KONTROL MANUAL:</span>
        <div class="flex gap-2">
            <button id="btn-buka" onclick="controlServo(1)"
                class="px-3 py-1 bg-blue-400 text-white text-[10px] font-bold rounded cursor-not-allowed opacity-50" disabled>BUKA</button>
            <button id="btn-tutup" onclick="controlServo(2)"
                class="px-3 py-1 bg-gray-400 text-white text-[10px] font-bold rounded cursor-not-allowed opacity-50" disabled>TUTUP</button>
        </div>
    </div>
</div>

            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-sm font-medium text-gray-500">Hama (Sensor 2)</p>
                    <span id="servo2-badge"
                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400">IDLE</span>
                </div>
                <div class="flex items-center gap-3 mt-1">
                    <span id="pir-dot" class="h-4 w-4 rounded-full bg-emerald-500"></span>
                    <h3 class="text-2xl font-bold text-emerald-600" id="pir-status">Aman</h3>
                </div>
                <p id="scarecrow-alert" class="hidden text-sm text-red-600 font-bold mt-2">Scarecrow Aktif</p>
                <p class="text-xs text-gray-400 mt-3 italic" id="hama-val">Jarak: 0 cm</p>

                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-400">PENGUSIR MANUAL:</span>
                    <div class="flex gap-2">
                        <button onclick="controlScarecrow(1)"
                            class="px-3 py-1 bg-red-500 text-white text-[10px] font-bold rounded hover:bg-red-600">TRIGGER</button>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-brand-900 rounded-2xl p-6 shadow-md text-white">
                <p class="text-sm font-medium text-brand-100 mb-2">Mode Operasi</p>
                <h3 class="text-2xl font-bold">OTOMATIS</h3>
                <div class="mt-4 flex items-center gap-2 text-xs text-brand-100">
                    <i class="fas fa-microchip"></i>
                    <span id="esp32-status">ESP32 Aktif</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <h3 class="font-bold mb-4 text-gray-700"><i class="fas fa-chart-line mr-2"></i>Grafik Kelembapan & Air</h3>
                <div class="h-64"><canvas id="historyChart"></canvas></div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <h3 class="font-bold mb-4 text-gray-700"><i class="fas fa-history mr-2"></i>Aktivitas Terbaru</h3>
                <div class="overflow-y-auto flex-1 h-64 text-sm space-y-3" id="log-container">
                    <p class="text-gray-400 italic">Menunggu transmisi data...</p>
                </div>
            </div>
        </div>
    </main>

    <script type="module">
    window.logoutFirebase = function() {
        auth.signOut()
            .then(() => {
                document.getElementById('logout-form').submit();
            })
            .catch((error) => {
                console.error("Logout error:", error);
            });
    }
        import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
    import {
        getDatabase,
        ref,
        onValue,
        set,
        get,
        query,
        limitToLast
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-database.js";

    import { getFirestore, collection, addDoc, query, where, onSnapshot, orderBy, limit } 
    from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

    // 1. KONFIGURASI FIREBASE
    const firebaseConfig = {
        apiKey: "AIzaSyC0QjC5TAy-ia1AzLLaaPLL53gcmLH1TbM",
        authDomain: "smartoryza.firebaseapp.com",
        databaseURL: "https://smartoryza-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "smartoryza",
        storageBucket: "smartoryza.firebasestorage.app",
        messagingSenderId: "63042198526",
        appId: "1:63042198526:web:fa0620b2c786a53c977eaf",
        measurementId: "G-S9Q8YM6609"
    };

    const app = initializeApp(firebaseConfig);
    const db = getDatabase(app);
    const firestore = getFirestore(app);
    let limit = 10;

    function renderLogs(snapshot) {
        const data = snapshot.val();
        const logContainer = document.getElementById('log-container');
        logContainer.innerHTML = '';

        if (data) {
            const logs = Object.values(data).reverse();

            logs.forEach(log => {
                const time = new Date(log.created_at).toLocaleTimeString('id-ID');
                const status = (log.pir || "").toLowerCase();

                const el = document.createElement('div');
                el.className = "p-3 bg-gray-50 rounded-lg border-l-4 " +
                    (status === 'bahaya' ? 'border-red-500' : 'border-emerald-500');

                el.innerHTML = `
                    <div class="flex justify-between">
                        <span class="text-xs">${time}</span>
                        <span class="text-xs font-bold">${status}</span>
                    </div>
                    <p class="text-xs">Soil: ${log.soil}% | Air: ${log.water}cm</p>
                `;

                logContainer.appendChild(el);
            });
        }
    }

    // query pertama
    let logsQuery = query(ref(db, 'iot/logs'), limitToLast(limit));
    onValue(logsQuery, renderLogs);

    const auth = getAuth(app);

    // 2. SETUP CHART (GRAFIK)
    const ctx = document.getElementById('historyChart').getContext('2d');
    let soilData = [];
    let waterData = [];
    let labels = [];

    const historyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Soil (%)',
                    data: soilData,
                    borderColor: '#3b82f6',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)'
                },
                {
                    label: 'Water (cm)',
                    data: waterData,
                    borderColor: '#22c55e',
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 3. FUNGSI KONTROL MANUAL
    window.controlServo = function(status) {
        fetch('/api/control/servo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Control sent:", data);
                const statusLabel = status === 1 ? "DIBUKA" : "DITUTUP";
                alert("Pintu Air Berhasil " + statusLabel + " secara manual.");
            })
            .catch(err => console.error("Error sending control:", err));
    };

    window.controlScarecrow = function(status) {
        const scarecrowRef = ref(db, 'iot/control/scarecrow');
        set(scarecrowRef, status)
            .then(() => {
                alert("Perintah pengusir hama berhasil dikirim ke ESP32!");
            })
            .catch(err => console.error("Error sending scarecrow control:", err));
    };

    window.toggleMode = function() {
        const toggle = document.getElementById('mode-toggle');
        const label = document.getElementById('mode-label');
        const badge = document.getElementById('servo1-badge');
        const btnBuka = document.getElementById('btn-buka');
        const btnTutup = document.getElementById('btn-tutup');

        const isManual = toggle.checked;

        if (isManual) {
            label.innerText = 'Manual';
            badge.innerText = 'MANUAL';
            badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600';

            btnBuka.disabled = false;
            btnBuka.classList.remove('bg-blue-400', 'cursor-not-allowed', 'opacity-50');
            btnBuka.classList.add('bg-blue-500', 'hover:bg-blue-600');

            btnTutup.disabled = false;
            btnTutup.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
            btnTutup.classList.add('bg-gray-500', 'hover:bg-gray-600');

            controlServo(2); // Default ke Tutup
        } else {
            label.innerText = 'Otomatis';
            badge.innerText = 'OTOMATIS';
            badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400';

            btnBuka.disabled = true;
            btnBuka.classList.add('bg-blue-400', 'cursor-not-allowed', 'opacity-50');
            btnBuka.classList.remove('bg-blue-500', 'hover:bg-blue-600');

            btnTutup.disabled = true;
            btnTutup.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
            btnTutup.classList.remove('bg-gray-500', 'hover:bg-gray-600');

            controlServo(0); // Kembali ke Otomatis
        }
    };

    // 4. LOGIKA MASA TANAM & NOTIFIKASI PENGERINGAN
    window.togglePlanting = async function() {
        const plantingRef = ref(db, 'iot/planting_cycle');
        const snapshot = await get(plantingRef);
        const data = snapshot.val();

        if (!data || !data.is_active) {
            const startTime = new Date().getTime();
            set(plantingRef, {
                is_active: true,
                start_timestamp: startTime
            });
            alert("Masa tanam dimulai! Estimasi panen adalah 6 bulan dari sekarang.");
        } else {
            if (confirm("Apakah Anda yakin ingin menyelesaikan masa tanam ini?")) {
                set(plantingRef, {
                    is_active: false,
                    start_timestamp: 0
                });
            }
        }
    };

    // Listener Masa Tanam & Notifikasi Fase Pengeringan
    onValue(ref(db, 'iot/planting_cycle'), (snapshot) => {
        const data = snapshot.val();
        const btn = document.getElementById('btn-planting');
        const alertBox = document.getElementById('dry-phase-alert');

        if (data && data.is_active) {
            btn.innerText = "Selesaikan Masa Tanam";
            btn.className = "bg-red-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg";

            const start = new Date(data.start_timestamp);
            const end = new Date(start);
            end.setMonth(start.getMonth() + 6);

            const now = new Date();
            const totalDuration = end - start;
            const timePassed = now - start;

            let percent = Math.floor((timePassed / totalDuration) * 100);
            percent = Math.min(Math.max(percent, 0), 100);

            const diffDays = Math.ceil((end - now) / (1000 * 60 * 60 * 24));
            const monthsPassed = Math.floor(timePassed / (1000 * 60 * 60 * 24 * 30));

            if (monthsPassed >= 4 && percent < 100) {
                alertBox.classList.remove('hidden');
            } else {
                alertBox.classList.add('hidden');
            }

            document.getElementById('planting-status').innerText = "Status: Berlangsung";
            document.getElementById('countdown-timer').innerText = diffDays > 0 ? diffDays + " Hari Lagi" : "Waktunya Panen!";
            document.getElementById('planting-progress-bar').style.width = percent + "%";
            document.getElementById('progress-percent').innerText = percent + "%";
            document.getElementById('start-date-label').innerText = "Mulai: " + start.toLocaleDateString('id-ID');
            document.getElementById('end-date-label').innerText = "Panen: " + end.toLocaleDateString('id-ID');

        } else {
            btn.innerText = "Mulai Masa Tanam";
            btn.className = "bg-brand-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg";
            document.getElementById('planting-status').innerText = "Status: Belum Dimulai";
            document.getElementById('countdown-timer').innerText = "-- Hari";
            document.getElementById('planting-progress-bar').style.width = "0%";
            alertBox.classList.add('hidden');
        }
    });

    // 5. LISTEN DATA SENSOR (REAL-TIME)
    onValue(ref(db, 'iot/latest'), (snapshot) => {
        const data = snapshot.val();
        const esp32Status = document.getElementById('esp32-status');
        const servo2Badge = document.getElementById('servo2-badge');

        if (data && data.soil !== undefined && data.water !== undefined) {
            esp32Status.innerText = "ESP32 Aktif";

            document.getElementById('soil-value').innerText = data.soil;
            document.getElementById('soil-bar').style.width = data.soil + "%";
            document.getElementById('distance').innerText = data.water;

            const pirStatus = document.getElementById('pir-status');
            const pirDot = document.getElementById('pir-dot');
            const scarecrowAlert = document.getElementById('scarecrow-alert');
            const status = (data.pir || "").toLowerCase();

            if (status === "bahaya") {
                pirStatus.innerText = "BAHAYA";
                pirStatus.className = "text-2xl font-bold text-red-600";
                pirDot.className = "h-4 w-4 rounded-full bg-red-600 animate-pulse";
                scarecrowAlert.classList.remove('hidden');

                servo2Badge.innerText = "AKTIF / BAHAYA";
                servo2Badge.className = "px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600";
            } else {
                pirStatus.innerText = "AMAN";
                pirStatus.className = "text-2xl font-bold text-emerald-600";
                pirDot.className = "h-4 w-4 rounded-full bg-emerald-500";
                scarecrowAlert.classList.add('hidden');

                servo2Badge.innerText = "IDLE / AMAN";
                servo2Badge.className = "px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400";
            }

            // Update Chart
            const now = new Date();
            const timeStr = now.getHours() + ":" + now.getMinutes().toString().padStart(2, '0');
            soilData.push(data.soil);
            waterData.push(data.water);
            labels.push(timeStr);

            if (labels.length > 10) {
                soilData.shift();
                waterData.shift();
                labels.shift();
            }
            historyChart.update();

            import { getAuth } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";

            const auth = getAuth();

            if (data && auth.currentUser) {
                addDoc(collection(firestore, "iot_logs"), {
                    user_id: auth.currentUser.uid,
                    soil: data.soil,
                    water: data.water,
                    pir: data.pir,
                    created_at: new Date()
                });
            }

        } else {
            esp32Status.innerText = "ESP32 Tidak Aktif";
            document.getElementById('soil-value').innerText = "-";
            document.getElementById('soil-bar').style.width = "0%";
            document.getElementById('distance').innerText = "-";
            document.getElementById('pir-status').innerText = "-";
            document.getElementById('pir-dot').className = "h-4 w-4 rounded-full bg-gray-400";
            document.getElementById('scarecrow-alert').classList.add('hidden');
            document.getElementById('hama-val').innerText = "Jarak: - cm";

            servo2Badge.innerText = "OFFLINE";
            servo2Badge.className = "px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400";
        }

        const systemStatus = document.getElementById('system-status');
        const systemDotPing = document.getElementById('system-dot-ping');
        const systemDot = document.getElementById('system-dot');
        const alertContainer = document.getElementById('alert-container');

        if (data && data.soil !== undefined && data.water !== undefined) {
            systemStatus.innerText = "Sistem Online";
            systemStatus.className = "text-sm font-bold text-emerald-700";
            systemDotPing.className = "animate-ping absolute h-full w-full rounded-full bg-emerald-400 opacity-75";
            systemDot.className = "relative h-3 w-3 bg-emerald-500 rounded-full";
            alertContainer.className = "bg-white border px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm";
        } else {
            systemStatus.innerText = "Sistem Offline";
            systemStatus.className = "text-sm font-bold text-red-700";
            systemDotPing.className = "animate-ping absolute h-full w-full rounded-full bg-red-400 opacity-75";
            systemDot.className = "relative h-3 w-3 bg-red-500 rounded-full";
            alertContainer.className = "bg-red-50 border border-red-200 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm";
        }
    });

    import { query, orderByChild, limitToLast } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-database.js";

    const logsRef = query(ref(db, 'iot/logs'), limitToLast(20));

    onValue(logsRef, (snapshot) => {
        const data = snapshot.val();
        const logContainer = document.getElementById('log-container');
        logContainer.innerHTML = '';

        if (data) {
            const logs = Object.values(data).reverse();

            logs.forEach(log => {
                const time = new Date(log.created_at).toLocaleTimeString('id-ID');
                const status = (log.pir || "").toLowerCase();

                const el = document.createElement('div');
                el.className = "p-3 bg-gray-50 rounded-lg border-l-4 " +
                    (status === 'bahaya' ? 'border-red-500' : 'border-emerald-500');

                el.innerHTML = `
                    <div class="flex justify-between">
                        <span class="text-xs">${time}</span>
                        <span class="text-xs font-bold">${status}</span>
                    </div>
                    <p class="text-xs">Soil: ${log.soil}% | Air: ${log.water}cm</p>
                `;

                logContainer.appendChild(el);
            });
        }
    });

    onAuthStateChanged(auth, (user) => {
        if (!user) {
            // ❌ kalau belum login → balikin ke landing page
            window.location.href = "/";
        } else {
            // ✅ kalau login → tampilkan user
            document.querySelector('#username').innerText = user.email;
        }
    });

    setInterval(() => {
        document.getElementById('current-time').innerText = new Date().toLocaleString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }, 1000);

    function goHome() {
        window.location.href = "/";
    }
</script>

    <script>
        async function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.text("Laporan SmartOryza (1 Bulan)", 14, 20);

            onAuthStateChanged(auth, (user) => {
                if (!user) return;

                const q = query(
                    collection(firestore, "iot_logs"),
                    where("user_id", "==", user.uid),
                    orderBy("created_at", "desc"),
                    limit(10)
                );

                onSnapshot(q, (snapshot) => {
                    const logContainer = document.getElementById('log-container');
                    logContainer.innerHTML = '';

                    snapshot.forEach(doc => {
                        const log = doc.data();
                        const time = new Date(log.created_at.seconds * 1000).toLocaleTimeString('id-ID');

                        const el = document.createElement('div');
                        el.className = "p-3 bg-gray-50 rounded-lg border-l-4 " +
                            (log.pir === 'bahaya' ? 'border-red-500' : 'border-emerald-500');

                        el.innerHTML = `
                            <div class="flex justify-between">
                                <span class="text-xs">${time}</span>
                                <span class="text-xs font-bold">${log.pir}</span>
                            </div>
                            <p class="text-xs">Soil: ${log.soil}% | Air: ${log.water}cm</p>
                        `;

                        logContainer.appendChild(el);
                    });
                });
            });
            
            const snapshot = await get(logsRef);
            const data = snapshot.val();

            let rows = [];

            if (data) {
                const now = new Date();
                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(now.getMonth() - 1);

                Object.values(data).forEach(log => {
                    const logDate = new Date(log.created_at);

                    if (logDate >= oneMonthAgo) {
                        rows.push([
                            logDate.toLocaleString('id-ID'),
                            log.soil + '%',
                            log.water + ' cm',
                            log.pir
                        ]);
                    }
                });
            }

            doc.autoTable({
                head: [['Waktu', 'Soil', 'Air', 'Status']],
                body: rows
            });

            doc.save("laporan_1_bulan.pdf");
}
    </script>
</body>

</html>
