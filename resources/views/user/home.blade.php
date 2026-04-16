<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Irigasi & Perlindungan Padi IoT</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,700;1,500&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* CSS VARIABLES */
        :root {
            --bg-color: #f4f7f4;
            --text-main: #4a5347;
            --forest-green: #2b5329;
            --leaf-green: #688f4e;
            --earth-brown: #8b5a2b;
            --sunlight: #fdf5e6;
            --card-bg: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Nunito', sans-serif;
            line-height: 1.8;
            font-size: 1.05rem;
            overflow-x: hidden; /* Mencegah scroll horizontal dari animasi */
        }

        h1, h2, h3 { font-family: 'Lora', serif; }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--forest-green);
            padding: 1.2rem 10%;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(43, 83, 41, 0.2);
        }

        /* ANIMASI CAHAYA MATAHARI PADA LOGO */
        .navbar-brand {
            color: #ffffff;
            font-size: 1.6rem;
            font-family: 'Lora', serif;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(120deg, #ffffff 0%, #ffffff 40%, #a4c639 50%, #ffffff 60%, #ffffff 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: sunlight-shine 5s linear infinite;
        }

        .navbar-brand span { color: #a4c639; }

        .nav-links { display: flex; list-style: none; }
        .nav-links li { margin-left: 30px; }
        .nav-links a {
            color: var(--sunlight);
            text-decoration: none;
            font-weight: 600;
            position: relative;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0; height: 2px; bottom: -5px; left: 0;
            background-color: #a4c639;
            transition: width 0.4s ease-in-out;
        }
        .nav-links a:hover::after { width: 100%; }

        /* HERO SECTION DENGAN ANIMASI AMBIENT ZOOM */
        .header-banner {
            position: relative;
            padding: 120px 10% 100px;
            text-align: center;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
        }

        .hero-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(43, 83, 41, 0.7), rgba(139, 90, 43, 0.5)),
                        url('https://images.unsplash.com/photo-1550989460-0adf9ea622e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            z-index: -1;
            animation: ambient-zoom 20s ease-in-out infinite alternate;
        }

        .header-banner h1 {
            color: white;
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.4);
            animation: fade-up 1.5s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .header-banner p {
            color: white;
            font-size: 1.2rem;
            max-width: 800px;
            opacity: 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            animation: fade-up 1.5s ease-out 0.5s forwards;
            transform: translateY(30px);
        }

        /* MAIN CONTENT */
        .container {
            max-width: 900px;
            margin: -60px auto 60px;
            background: var(--card-bg);
            padding: 50px 60px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(43, 83, 41, 0.1);
            position: relative;
            z-index: 10;
        }

        .content-section { margin-bottom: 50px; }

        h2 {
            color: var(--forest-green);
            border-bottom: 2px dashed #e2e8de;
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ANIMASI DAUN TERTIUP ANGIN PADA JUDUL */
        .sprout-icon {
            display: inline-block;
            font-size: 1.8rem;
            transform-origin: bottom center;
            animation: sway 4s ease-in-out infinite;
        }

        p { margin-bottom: 20px; text-align: justify; }

        /* HIGHLIGHT BOX */
        .highlight-card {
            background-color: #faf9f0;
            border-left: 5px solid var(--earth-brown);
            padding: 25px 30px;
            margin: 35px 0;
            border-radius: 0 15px 15px 0;
            font-family: 'Lora', serif;
            font-style: italic;
            color: var(--earth-brown);
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .highlight-card:hover {
            transform: translateX(10px);
            background-color: #fdfbf2;
        }

        /* FEATURE GRID */
        .tech-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 35px;
        }

        .feature-item {
            background: #f8faf6;
            padding: 30px 25px;
            border-radius: 16px;
            border: 1px solid #e2e8de;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efek organik natural */
        }

        .feature-item:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(104, 143, 78, 0.15);
            border-color: var(--leaf-green);
            background: white;
        }

        /* ANIMASI IKON MELAYANG (FLOATING) */
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            display: inline-block;
            animation: float 4s ease-in-out infinite;
        }

        .feature-item:nth-child(2) .feature-icon { animation-delay: 1s; }
        .feature-item:nth-child(3) .feature-icon { animation-delay: 2s; }

        .feature-item strong {
            color: var(--forest-green);
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            display: block;
            margin-bottom: 10px;
        }

        footer {
            text-align: center;
            padding: 40px 20px;
            background-color: var(--forest-green);
            color: #a4c639;
        }

        /* --- KEYFRAMES (KAMUS ANIMASI) --- */

        /* Matahari mengkilap di teks */
        @keyframes sunlight-shine {
            to { background-position: 200% center; }
        }

        /* Zoom In Out pelan pada background awah */
        @keyframes ambient-zoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        /* Muncul dari bawah seperti tunas */
        @keyframes fade-up {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Bergoyang seperti tertiup angin */
        @keyframes sway {
            0%, 100% { transform: rotate(-8deg); }
            50% { transform: rotate(12deg); }
        }

        /* Melayang perlahan naik turun */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        /* --- KELAS UNTUK ANIMASI SCROLL (GROWTH EFFECT) --- */
        .nature-reveal {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
            transition: all 1s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .nature-reveal.active {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container { margin: -30px 20px 40px; padding: 30px 25px; }
            .nav-links { display: none; }
            .header-banner h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="#" class="navbar-brand">🌾 Smart<span>Oryza</span></a>
        <ul class="nav-links">
            <li><a href="#pendahuluan">Pendahuluan</a></li>
            <li><a href="#permasalahan">Permasalahan</a></li>
            <li><a href="#solusi">Solusi IoT</a></li>
        </ul>
    </nav>

    <header class="header-banner">
        <div class="hero-bg"></div>
        <h1>Modernisasi Pertanian Padi</h1>
        <p>Menyelaraskan Kebijaksanaan Alam dengan Inovasi Internet of Things untuk Panen yang Melimpah</p>
    </header>

    <div class="container">

        <!-- Tambahkan class 'nature-reveal' pada elemen yang ingin dianimasikan saat di-scroll -->
        <section id="pendahuluan" class="content-section nature-reveal">
            <h2><span class="sprout-icon">🌱</span> Latar Belakang</h2>
            <p>
                Pertanian merupakan detak jantung kehidupan Nusantara, khususnya tanaman padi yang menjadi sumber pangan utama masyarakat Indonesia. Dalam proses budidaya padi, air adalah elemen kehidupan; pengaturan irigasi menjadi faktor krusial yang menentukan kesuburan tanah dan hasil panen.
            </p>
            <p>
                Keseimbangan air yang pas—tidak kurang dan tidak berlebih—sangat dibutuhkan oleh tanaman untuk tumbuh dengan sehat, menjaga kelestarian unsur hara tanah, dan memastikan produktivitas pertanian yang berkelanjutan dari musim ke musim.
            </p>
        </section>

        <section id="permasalahan" class="content-section nature-reveal">
            <h2><span class="sprout-icon">🍃</span> Kondisi di Lapangan</h2>
            <p>
                Berdasarkan bincang hangat dengan <strong>Bapak Junaidi</strong>, seorang petani yang telah mengabdi pada sawahnya sejak tahun 2002, aliran air ke petak sawah saat ini masih sangat bergantung pada tenaga manusia dan intuisi semata. Air sungai dialirkan secara manual tanpa takaran pasti.
            </p>

            <div class="highlight-card nature-reveal">
                "Cara tradisional terkadang membuat kita kewalahan. Saat musim hujan lebat, sawah kebanjiran dan akar padi membusuk. Sebaliknya saat kemarau, kita terlambat menyadari tanah sudah mengering."
            </div>

            <p>
                Kewajiban untuk memantau pematang sawah secara langsung setiap pagi dan sore hari menguras banyak waktu dan tenaga. Oleh karena itu, diperlukan sebuah jembatan antara kearifan lokal petani dengan kemudahan teknologi masa kini.
            </p>
        </section>

        <section id="solusi" class="content-section nature-reveal">
            <h2><span class="sprout-icon">🌿</span> Solusi Teknologi Harmonis</h2>
            <p>
                Sentuhan teknologi <strong>Internet of Things (IoT)</strong> hadir bukan untuk menggantikan peran petani, melainkan menjadi asisten digital yang bekerja 24 jam. Sistem otomasi ini dirancang agar ramah lingkungan dan terdiri dari:
            </p>

            <div class="tech-features">
                <div class="feature-item nature-reveal" style="transition-delay: 0.1s;">
                    <div class="feature-icon">💧</div>
                    <strong>Sensor Kelembapan</strong>
                    <p>Merasakan kadar air di dalam tanah secara presisi, mengetahui kapan tanah benar-benar haus.</p>
                </div>
                <div class="feature-item nature-reveal" style="transition-delay: 0.3s;">
                    <div class="feature-icon">⚙️</div>
                    <strong>Katup Air Pintar</strong>
                    <p>Membuka dan menutup aliran air secara otomatis sesuai kebutuhan biologis tanaman padi.</p>
                </div>
                <div class="feature-item nature-reveal" style="transition-delay: 0.5s;">
                    <div class="feature-icon">🦅</div>
                    <strong>Penjaga Ekosistem</strong>
                    <p>Sensor pendeteksi hama burung yang terhubung dengan penggerak orang-orangan sawah otomatis.</p>
                </div>
            </div>

            <p style="margin-top: 35px;" class="nature-reveal">
                Melalui harmoni antara alam dan inovasi IoT ini, para petani dapat merawat lahan mereka dengan lebih efisien, menghemat penggunaan air bersih, serta melindungi tanaman padi dari ancaman hama secara manusiawi dan berkelanjutan.
            </p>
        </section>

    </div>

    <footer>
        <p>&copy; 2026 Smart Oryza - Merawat Bumi, Memberi Makan Negeri.</p>
    </footer>

    <!-- SCRIPT KECIL UNTUK ANIMASI SCROLL (Efek tumbuh dari tanah) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15 // Animasi mulai saat 15% elemen terlihat di layar
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target); // Hanya dijalankan sekali
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.nature-reveal');
            revealElements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
