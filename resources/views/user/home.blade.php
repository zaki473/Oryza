<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartOryza - Sistem Irigasi & Perlindungan Padi IoT</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '#16a34a',
                            900: '#2b5329', // Forest Green
                        },
                        accent: '#a4c639', // Leaf Green
                        soft: '#f8fbf8' // Subtle nature background
                    },
                    animation: {
                        blob: "blob 7s infinite",
                    },
                    keyframes: {
                        blob: {
                            "0%": { transform: "translate(0px, 0px) scale(1)" },
                            "33%": { transform: "translate(30px, -50px) scale(1.1)" },
                            "66%": { transform: "translate(-20px, 20px) scale(0.9)" },
                            "100%": { transform: "translate(0px, 0px) scale(1)" },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading Spinner */
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid #fff;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .loading-overlay.active {
            display: flex;
        }
    </style>
</head>
<body class="font-sans text-gray-700 bg-soft antialiased selection:bg-brand-500 selection:text-white">

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
        <p class="text-white text-lg font-semibold mt-6">Sedang memproses...</p>
        <p class="text-white/70 text-sm mt-1">Mohon tunggu sebentar</p>
    </div>

    <!-- POP-UP NOTIFIKASI GLOBAL -->
    <div id="notificationPopup" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[200] transition-all duration-300 opacity-0 -translate-y-10 pointer-events-none">
        <div class="bg-white px-6 py-4 rounded-2xl shadow-2xl border border-gray-100 flex items-center gap-4 min-w-[320px]">
            <div id="notifyIcon" class="w-10 h-10 rounded-full flex items-center justify-center text-lg"></div>
            <div>
                <h4 id="notifyTitle" class="font-bold text-gray-900">Judul</h4>
                <p id="notifyMessage" class="text-sm text-gray-500">Pesan notifikasi...</p>
            </div>
            <button onclick="closePopup()" class="ml-auto pl-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        <span class="text-xl text-white">🌾</span>
                    </div>
                    <span class="text-2xl font-bold font-serif text-brand-900 tracking-tight">
                        Smart<span class="text-brand-500">Oryza</span>
                    </span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#latar-belakang" class="text-sm font-semibold text-gray-600 hover:text-brand-600 transition">Pendahuluan</a>
                    <a href="#solusi" class="text-sm font-semibold text-gray-600 hover:text-brand-600 transition">Solusi IoT</a>

                    <div class="w-px h-6 bg-gray-200"></div>

                    <div id="authButton" class="flex items-center gap-2"></div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-100"
             style="background-image: url('{{ asset('images/padi.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-white/80 via-white/30 to-[#f4f7f4]"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-xs font-bold mb-6 border border-brand-200 shadow-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                </span>
                Sistem IoT Aktif 24/7
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold font-serif text-brand-900 tracking-tight leading-tight mb-6 max-w-4xl mx-auto reveal">
                Modernisasi Pertanian Padi <br> <span class="text-brand-500 drop-shadow-sm">Berbasis Teknologi</span>
            </h1>
            <p class="text-lg text-gray-800 font-medium mb-10 max-w-2xl mx-auto leading-relaxed reveal" style="transition-delay: 0.1s;">
                Menyelaraskan kebijaksanaan alam dengan inovasi Internet of Things (IoT) untuk efisiensi irigasi, perlindungan hama, dan panen yang lebih melimpah.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 reveal" style="transition-delay: 0.2s;">
                <button onclick="openModal('login')" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold text-white bg-brand-900 rounded-full hover:bg-brand-800 transition-all shadow-lg shadow-brand-900/30 hover:-translate-y-1">
                    Mulai Sekarang <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <a href="#solusi" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold text-brand-900 bg-white/90 backdrop-blur-sm border border-brand-200 rounded-full hover:bg-white transition-all shadow-sm hover:shadow-md">
                    Pelajari Fitur
                </a>
            </div>
        </div>
    </section>

    <!-- Background & Problem -->
    <section id="latar-belakang" class="relative py-24 overflow-hidden">
        <div class="absolute top-0 left-10 w-72 h-72 bg-brand-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob"></div>
        <div class="absolute top-0 right-10 w-72 h-72 bg-[#e4eba7] rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-8 left-40 w-72 h-72 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob" style="animation-delay: 4s;"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center reveal">
                <div>
                    <div class="w-12 h-12 bg-orange-100/80 backdrop-blur text-orange-600 rounded-2xl flex items-center justify-center text-xl mb-6 shadow-sm border border-orange-200/50">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h2 class="text-3xl font-bold font-serif text-brand-900 mb-4">Akar Permasalahan di Lapangan</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed text-justify">
                        Pertanian merupakan detak jantung kehidupan Nusantara. Dalam budidaya padi, air adalah elemen kehidupan. Keseimbangan air yang pas—tidak kurang dan tidak berlebih—sangat dibutuhkan oleh tanaman untuk tumbuh dengan sehat dan memastikan produktivitas.
                    </p>
                    <p class="text-gray-600 leading-relaxed text-justify">
                        Namun, kewajiban memantau sawah secara langsung setiap pagi dan sore hari menguras banyak waktu dan tenaga. Perubahan iklim yang tidak menentu memperburuk situasi bagi petani konvensional.
                    </p>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-brand-500 rounded-3xl transform rotate-3 scale-105 opacity-10"></div>
                    <div class="relative bg-white/90 backdrop-blur-sm p-8 md:p-10 rounded-3xl border border-white shadow-xl">
                        <i class="fas fa-quote-left text-4xl text-brand-100 absolute top-8 left-8"></i>
                        <p class="relative z-10 text-xl font-serif italic text-gray-800 leading-relaxed mt-6 mb-8">
                            "Cara tradisional terkadang membuat kita kewalahan. Saat musim hujan lebat, sawah kebanjiran dan akar padi membusuk. Di musim kemarau, kita telat mengairi."
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center border border-brand-200">
                                <i class="fas fa-user text-brand-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Bapak Junaidi</h4>
                                <p class="text-sm text-brand-600 font-medium">Petani Padi sejak 2002</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Solution & Features -->
    <div class="px-4 lg:px-8 max-w-[90rem] mx-auto mb-20">
        <section id="solusi" class="relative bg-brand-900 rounded-[2.5rem] lg:rounded-[3.5rem] py-20 px-6 lg:px-12 shadow-2xl overflow-hidden">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-600 rounded-full filter blur-[100px] opacity-30 mix-blend-screen"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-accent rounded-full filter blur-[100px] opacity-20 mix-blend-screen"></div>

            <div class="relative z-10 text-center max-w-3xl mx-auto mb-16 reveal">
                <h2 class="text-3xl font-bold font-serif text-white mb-4">Solusi Teknologi Harmonis</h2>
                <p class="text-brand-100 text-lg">Sentuhan teknologi Internet of Things (IoT) hadir bukan untuk menggantikan peran petani, melainkan sebagai asisten cerdas yang bekerja 24 jam penuh.</p>
            </div>

            <div class="relative z-10 grid md:grid-cols-3 gap-6 lg:gap-8 reveal">
                <div class="bg-white p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sensor Kelembapan</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Membaca kadar air dalam tanah secara presisi dan real-time. Memastikan tanah tidak pernah kekeringan atau kelebihan air.</p>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300 group" style="transition-delay: 0.1s;">
                    <div class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-faucet"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Katup Irigasi Pintar</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Membuka dan menutup aliran air secara otomatis berdasarkan data sensor, atau dapat dikontrol secara manual lewat Dashboard.</p>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300 group" style="transition-delay: 0.2s;">
                    <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-crow"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sistem Pengusir Hama</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Sensor PIR mendeteksi pergerakan hama burung dan secara otomatis mengaktifkan penggerak orang-orangan sawah.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="border-t border-gray-200 py-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xl">🌾</span>
                <span class="text-lg font-bold font-serif text-brand-900">SmartOryza</span>
            </div>
            <p class="text-gray-500 text-sm">&copy; 2026 Smart Oryza - Merawat Bumi, Memberi Makan Negeri.</p>
        </div>
    </footer>

    <!-- ================= MODAL LOGIN & REGISTER ================= -->
    <div id="authModal" class="fixed inset-0 z-[100] hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-brand-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <div id="modalPanel" class="bg-white w-full max-w-md rounded-3xl shadow-2xl relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300 border border-white">
            <div class="bg-brand-900 p-6 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white opacity-5 rounded-full blur-xl"></div>
                <button onclick="closeModal()" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-md">
                    <i class="fas fa-lock text-white text-lg"></i>
                </div>
                <h3 id="modalTitle" class="text-xl font-bold font-serif text-white relative z-10">Selamat Datang</h3>
                <p class="text-brand-100 text-sm mt-1 relative z-10">Silakan masuk ke akun Anda</p>
            </div>

            <div class="p-8 bg-soft">
                <!-- FORM LOGIN -->
                <form id="loginForm" class="space-y-4 block">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition shadow-sm" placeholder="contoh@email.com" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kata Sandi</label>
                        <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition shadow-sm" placeholder="••••••••" required>
                    </div>
                    <button type="submit" id="loginSubmitBtn" class="w-full py-3.5 mt-2 bg-brand-900 text-white font-bold rounded-xl hover:bg-brand-800 transition shadow-lg shadow-brand-900/20 flex justify-center items-center gap-2">
                        <span id="loginBtnText">Masuk ke Dashboard</span>
                        <span id="loginBtnSpinner" class="hidden"><i class="fas fa-spinner animate-spin"></i></span>
                    </button>

                    <script>
                    document.getElementById("loginForm").addEventListener("submit", async function(e) {
                        e.preventDefault();

                        const email = this.email.value;
                        const password = this.password.value;
                        const submitBtn = document.getElementById("loginSubmitBtn");
                        const btnText = document.getElementById("loginBtnText");
                        const btnSpinner = document.getElementById("loginBtnSpinner");
                        const loadingOverlay = document.getElementById("loadingOverlay");

                        // Show loading state
                        submitBtn.disabled = true;
                        btnText.classList.add('hidden');
                        btnSpinner.classList.remove('hidden');
                        loadingOverlay.classList.add('active');

                        await setPersistence(auth, browserSessionPersistence);

                        try {
                            const userCredential = await signInWithEmailAndPassword(auth, email, password);
                            const user = userCredential.user;

                            // ✅ SIMPAN KE FIRESTORE
                            await setDoc(doc(db, "users", user.uid), {
                                email: user.email,
                                last_login: new Date().toISOString()
                            }, { merge: true });

                            // ✅ KIRIM KE LARAVEL
                            const token = await user.getIdToken();

                            const response = await fetch('/api/login-firebase', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ token })
                            });

                            if (response.ok) {
                                showPopup('success', 'Login Berhasil', 'Mengarahkan ke dashboard...');
                                setTimeout(() => {
                                    window.location.href = "/dashboard";
                                }, 1500);
                            } else {
                                throw new Error('Login ke server gagal');
                            }

                        } catch (error) {
                            // Hide loading state
                            submitBtn.disabled = false;
                            btnText.classList.remove('hidden');
                            btnSpinner.classList.add('hidden');
                            loadingOverlay.classList.remove('active');

                            showPopup('error', 'Login Gagal', error.message);
                        }
                    });
                    </script>
                </form>
            </div>
        </div>
    </div>

    <!-- Menangani Pesan Error Bawaan Laravel jika di-refresh -->
        @if ($errors->any())
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            openModal('login');
            showPopup('error', 'Gagal', '{{ $errors->first() }}');
        });
        </script>
        @endif

    <script>
        // --- FUNGSI MODAL GLOBAL ---
        function openModal(type) {
            const modal = document.getElementById('authModal');
            const panel = document.getElementById('modalPanel');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('authModal');
            const panel = document.getElementById('modalPanel');
            panel.classList.add('scale-95', 'opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // --- ANIMASI SCROLL ---
        document.addEventListener("DOMContentLoaded", function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });

        // --- SISTEM POPUP NOTIFIKASI ---
        let popupTimeout;
        function showPopup(type, title, message) {
            const popup = document.getElementById('notificationPopup');
            const icon = document.getElementById('notifyIcon');
            const titleEl = document.getElementById('notifyTitle');
            const msgEl = document.getElementById('notifyMessage');

            titleEl.textContent = title;
            msgEl.textContent = message;

            if (type === 'success') {
                icon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-lg bg-emerald-100 text-emerald-600';
                icon.innerHTML = '<i class="fas fa-check"></i>';
            } else {
                icon.className = 'w-10 h-10 rounded-full flex items-center justify-center text-lg bg-red-100 text-red-600';
                icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            }

            popup.classList.remove('opacity-0', '-translate-y-10', 'pointer-events-none');
            popup.classList.add('opacity-100', 'translate-y-0');

            clearTimeout(popupTimeout);
            popupTimeout = setTimeout(closePopup, 5000);
        }

        function closePopup() {
            const popup = document.getElementById('notificationPopup');
            popup.classList.add('opacity-0', '-translate-y-10', 'pointer-events-none');
            popup.classList.remove('opacity-100', 'translate-y-0');
        }

        function closePopup() {
            const popup = document.getElementById('notificationPopup');
            popup.classList.remove('opacity-100', 'translate-y-0');
            popup.classList.add('opacity-0', '-translate-y-10', 'pointer-events-none');
        }

        // --- LOGIKA MODAL ---
        const modal = document.getElementById('authModal');
        const modalPanel = document.getElementById('modalPanel');

        function openModal(type = 'login') {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalPanel.classList.remove('scale-95', 'opacity-0');
                modalPanel.classList.add('scale-100', 'opacity-100');
            }, 10);
            switchTab(type);
        }

        function closeModal() {
            modalPanel.classList.remove('scale-100', 'opacity-100');
            modalPanel.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function switchTab(type) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const toLogin = document.getElementById('toLogin');
            const toRegister = document.getElementById('toRegister');
            const modalTitle = document.getElementById('modalTitle');

            if (type === 'register') {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');

                toRegister.classList.add('hidden');
                toLogin.classList.remove('hidden');

                modalTitle.textContent = 'Buat Akun Baru';
            } else {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');

                toRegister.classList.remove('hidden');
                toLogin.classList.add('hidden');

                modalTitle.textContent = 'Selamat Datang Kembali';
            }
        }
    </script>

    <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
    import {
        getAuth,
        signInWithEmailAndPassword,
        setPersistence,
        browserSessionPersistence,
        onAuthStateChanged,
        signOut
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";

    import {
        getFirestore,
        doc,
        setDoc
    } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

    // CONFIG
    const firebaseConfig = {
        apiKey: "AIzaSyC0QjC5TAy-ia1AzLLaaPLL53gcmLH1TbM",
        authDomain: "smartoryza.firebaseapp.com",
        projectId: "smartoryza",
    };

    // INIT
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const db = getFirestore(app);

    // ================= LOGIN =================
    document.getElementById("loginForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const email = this.email.value;
        const password = this.password.value;

        try {
            await setPersistence(auth, browserSessionPersistence);

            const userCredential = await signInWithEmailAndPassword(auth, email, password);
            const user = userCredential.user;

            // Simpan ke Firestore
            await setDoc(doc(db, "users", user.uid), {
                email: user.email,
                last_login: new Date().toISOString()
            }, { merge: true });

            // Kirim ke Laravel
            const token = await user.getIdToken();

            await fetch('/api/login-firebase', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token })
            });

            showPopup('success', 'Berhasil', 'Login sukses!');

            setTimeout(() => {
                window.location.href = "/dashboard";
            }, 1000);

        } catch (error) {
            showPopup('error', 'Login Gagal', error.message);
        }
    });

    // ================= AUTH STATE =================
    onAuthStateChanged(auth, (user) => {
        const authDiv = document.getElementById("authButton");

        if (user) {
            authDiv.innerHTML = `
                <a href="/dashboard" class="text-sm font-bold text-white bg-brand-900 px-5 py-2.5 rounded-full hover:bg-brand-800 transition">
                    <i class="fas fa-chart-line mr-1.5"></i> Dashboard
                </a>
                <button onclick="logout()" class="text-sm font-bold text-white bg-red-600 px-5 py-2.5 rounded-full hover:bg-red-700 transition flex items-center gap-1.5">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            `;
        } else {
            authDiv.innerHTML = `
                <button onclick="openModal('login')" class="text-sm font-bold text-white bg-brand-900 px-5 py-2.5 rounded-full hover:bg-brand-800 transition">
                    <i class="fas fa-lock mr-1.5"></i> Login
                </button>
            `;
        }
    });

    // ================= LOGOUT =================
    window.logout = function() {
        if (!confirm('Apakah Anda yakin ingin logout?')) return;

        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.classList.add('active');

        signOut(auth).then(async () => {
            // Logout dari Laravel
            try {
                await fetch('/api/logout-firebase', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
            } catch (e) {
                console.log('Logout dari server:', e);
            }

            showPopup('success', 'Berhasil Logout', 'Mengarahkan ke halaman utama...');
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
                window.location.href = "/";
            }, 1500);
        }).catch(error => {
            loadingOverlay.classList.remove('active');
            showPopup('error', 'Logout Gagal', error.message);
        });
    };

    </script>

    <script>
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

    </script>
</html>
