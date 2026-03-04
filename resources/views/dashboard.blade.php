<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Kabupaten Jember</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; }
    body { background: #f0f4ff; }
    .nav-active { border-bottom: 2.5px solid #2563eb; color: #2563eb; font-weight: 700; }
    .chart-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(37,99,235,0.07); }
    .badge-blue { background: #2563eb; color: white; border-radius: 8px; padding: 6px 14px; font-size: 13px; font-weight: 600; }
    .section-blue { background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); border-radius: 16px; padding: 28px 32px; color: white; box-shadow: 0 4px 24px rgba(37,99,235,0.18); }
    .input-date { border: 1.5px solid #d1d5db; border-radius: 10px; padding: 8px 14px; font-size: 13px; color: #6b7280; outline: none; width: 100%; }
    .input-date:focus { border-color: #2563eb; }
    input[type="date"]::-webkit-calendar-picker-indicator { opacity: 0; position: absolute; right: 0; width: 36px; height: 100%; cursor: pointer; }
    .btn-filter { background: #2563eb; color: white; border-radius: 10px; padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; border: none; transition: background 0.15s; }
    .btn-filter:hover { background: #1d4ed8; }
    .btn-reset { background: white; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.15s; }
    .btn-reset:hover { background: #f3f4f6; }
    .bar-item { display: flex; align-items: center; margin-bottom: 10px; gap: 10px; }
    .bar-label { width: 140px; font-size: 12px; color: #374151; text-align: right; flex-shrink: 0; }
    .bar-track { flex: 1; background: #f1f5f9; border-radius: 999px; height: 13px; overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 999px; transition: width 1s cubic-bezier(.4,0,.2,1); }
    .sector-card { background: #1d4ed8; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; cursor: pointer; transition: background 0.15s, transform 0.15s; }
    .sector-card:hover { background: #1e40af; transform: translateY(-2px); }
    .sector-icon { background: rgba(255,255,255,0.15); border-radius: 10px; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    .section-wrap { background: white; border-radius: 20px; padding: 28px; box-shadow: 0 2px 16px rgba(37,99,235,0.06); border: 1.5px solid #e8edf8; }
    .sec-head { text-align: center; margin-bottom: 22px; padding-bottom: 20px; border-bottom: 1.5px dashed #e2e8f0; }
    .sec-badge { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: 5px 16px; font-size: 11.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 10px; }
    .sec-badge-blue { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
    .sec-badge-green { background: #f0fdf4; color: #16a34a; border: 1.5px solid #bbf7d0; }
    .sec-badge-amber { background: #fffbeb; color: #b45309; border: 1.5px solid #fde68a; }
    .sec-head h2 { font-size: 19px; font-weight: 800; color: #1e293b; line-height: 1.35; margin-bottom: 5px; }
    .sec-head p { font-size: 12.5px; color: #6b7280; font-style: italic; }
    .sec-head .periode { font-size: 11.5px; color: #94a3b8; margin-top: 3px; }
    .timestamp { text-align: right; font-size: 11px; color: #94a3b8; font-style: italic; margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9; }

    .stat-btn {
      background: white; border-radius: 14px; padding: 18px 16px 14px;
      box-shadow: 0 2px 12px rgba(37,99,235,0.07);
      transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
      cursor: pointer; border: 2px solid transparent;
      text-align: left; width: 100%; position: relative; display: block;
    }
    .stat-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(37,99,235,0.15); }
    .stat-btn.active { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37,99,235,0.10), 0 8px 24px rgba(37,99,235,0.13); }
    .stat-btn.active-green { border-color: #16a34a; box-shadow: 0 0 0 4px rgba(22,163,74,0.10), 0 8px 24px rgba(22,163,74,0.13); }
    .stat-btn.active-amber { border-color: #b45309; box-shadow: 0 0 0 4px rgba(180,83,9,0.10), 0 8px 24px rgba(180,83,9,0.13); }
    .stat-btn .click-hint {
      position: absolute; bottom: 8px; right: 10px;
      font-size: 9.5px; color: #94a3b8; font-style: italic;
      display: flex; align-items: center; gap: 3px;
    }

    .kec-chart-panel {
      display: none;
      background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
      border-radius: 16px; padding: 22px 24px; margin-top: 18px;
      border: 1.5px solid #fbcfe8;
      animation: slideDown 0.3s ease;
    }
    .kec-chart-panel.green-panel { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0; }
    .kec-chart-panel.amber-panel { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #fde68a; }
    .kec-chart-panel.show { display: block; }

    .export-btn {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;
      cursor: pointer; border: none; transition: all 0.15s; white-space: nowrap;
    }
    .export-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .export-btn:active { transform: translateY(0); }
    .btn-png { background: #3b82f6; color: white; }
    .btn-png:hover { background: #2563eb; }
    .btn-excel { background: #16a34a; color: white; }
    .btn-excel:hover { background: #15803d; }
    .btn-pdf { background: #ef4444; color: white; }
    .btn-pdf:hover { background: #dc2626; }
    .export-group { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

    @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    .animate-fadeUp { animation: fadeUp 0.5s ease both; }
    .a1 { animation-delay: 0.05s; }
    .a2 { animation-delay: 0.1s; }
    .a3 { animation-delay: 0.18s; }
    .a4 { animation-delay: 0.26s; }
    .a5 { animation-delay: 0.34s; }
  </style>
</head>
<body class="min-h-screen">

<nav class="bg-white shadow-sm sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-blue-50 shadow-sm flex-shrink-0">
        <img src="https://image2url.com/r2/default/images/1772597932642-eb3b148f-9f04-4980-9c4a-fac3ed6c363e.png" 
             alt="Profile Dashboard" 
             class="w-full h-full object-cover">
      </div>
      <div>
        <div class="font-extrabold text-gray-800 text-sm leading-tight">Dashboard</div>
        <div class="text-xs text-gray-400">Kabupaten Jember</div>
      </div>
    </div>
    <div class="flex items-center gap-8">
      <a href="#" class="text-sm nav-active pb-1">BERANDA</a>
      <a href="#" class="text-sm text-gray-500 hover:text-blue-600 font-medium">EKSPLORASI DASHBOARD</a>
      <a href="#" class="text-sm text-gray-500 hover:text-blue-600 font-medium">TENTANG</a>
    </div>
    <button class="badge-blue">Executive Dashboard</button>
  </div>
</nav>
<div class="max-w-7xl mx-auto px-6 py-8 space-y-7">

 <div class="section-blue animate-fadeUp">
  <div class="text-blue-200 text-sm mb-3">Beranda &rsaquo; Eksplorasi Dashboard &rsaquo; <span class="text-white font-semibold">Rekomendasi Sekolah</span></div>
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-2xl font-extrabold mb-1">Rekomendasi Sekolah</h1>
      <p class="text-blue-100 text-sm max-w-xl">Dapatkan rekomendasi sekolah yang mendukung potensi dan perkembangan akademik Anda melalui data yang terstruktur dan akurat.</p>
    </div>
    <button class="bg-white text-blue-700 font-semibold text-sm rounded-xl px-5 py-2.5 flex items-center gap-2 hover:bg-blue-50 transition-colors shadow">
      <i class="fa-solid fa-share-nodes"></i> Bagikan
    </button>
  </div>
</div>

<div class="bg-white rounded-2xl p-5 shadow-sm flex flex-wrap items-end gap-4 animate-fadeUp a1">
  <div class="flex flex-col gap-1">
    <label class="text-xs font-semibold text-blue-600">Dari Tanggal</label>
    <div class="relative" style="width:180px">
      <input type="date" id="date-from" class="input-date pr-8" style="cursor:pointer"/>
      <i class="fa-regular fa-calendar absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs" style="cursor:pointer;pointer-events:auto" onclick="document.getElementById('date-from').showPicker()"></i>
    </div>
  </div>
  <div class="flex flex-col gap-1">
    <label class="text-xs font-semibold text-blue-600">Sampai Tanggal</label>
    <div class="relative" style="width:180px">
      <input type="date" id="date-to" class="input-date pr-8" style="cursor:pointer"/>
      <i class="fa-regular fa-calendar absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs" style="cursor:pointer;pointer-events:auto" onclick="document.getElementById('date-to').showPicker()"></i>
    </div>
    </div>
    <button class="btn-filter"><i class="fa-solid fa-filter"></i> Terapkan Filter</button>
    <button class="btn-reset"><i class="fa-solid fa-arrows-rotate"></i> Reset Filter</button>
  </div>

  <div class="section-wrap animate-fadeUp a2">
    <div class="sec-head">
      <div class="sec-badge sec-badge-blue"><i class="fa-solid fa-heart-pulse"></i><span>Ringkasan Kematian</span></div>
      <h2>Ringkasan Angka Kematian Ibu &amp; Anak <span style="color:#2563eb">(AKI/AKB)</span></h2>
      <p>Menampilkan kondisi Angka Kematian Ibu, Bayi, Balita, Abortus, dan IUFD. Klik kartu untuk melihat grafik per kecamatan.</p>
      <div class="periode">Periode Data: 01/12/2025 – 31/12/2025</div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-2" id="kematian-cards">
      <button class="stat-btn" onclick="showKecChart('kematian','total','Total Kasus Kematian','#6366f1',this,'active')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-skull-crossbones text-red-400"></i> Total Kasus Kematian</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-2">1.395</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('kematian','aki','Kematian Ibu (AKI)','#ef4444',this,'active')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-person-pregnant text-pink-400"></i> Kematian Ibu AKI</div>
        <div class="text-xs text-gray-400 mb-1">Kematian ibu selama kehamilan, persalinan, &amp; nifas</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">24</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('kematian','akb','Kematian Bayi (AKB)','#f59e0b',this,'active')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-baby text-blue-400"></i> Kematian Bayi AKB</div>
        <div class="text-xs text-gray-400 mb-1">Kematian bayi usia 0–11 bulan</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">263</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('kematian','akaba','Kematian Balita (AKABA)','#22c55e',this,'active')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-child text-yellow-400"></i> Kematian Balita</div>
        <div class="text-xs text-gray-400 mb-1">Kematian anak usia 0–59 bulan</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">278</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('kematian','abortus','Kasus Abortus','#f97316',this,'active')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-heart-crack text-orange-400"></i> Kasus Abortus</div>
        <div class="text-xs text-gray-400 mb-1">Kematian janin &lt; 20 minggu</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">649</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('kematian','iufd','Kasus IUFD','#a855f7',this,'active')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-triangle-exclamation text-purple-400"></i> Kasus IUFD</div>
        <div class="text-xs text-gray-400 mb-1">Kematian janin &ge; 20 minggu</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">181</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
    </div>

    <div class="kec-chart-panel" id="kematian-chart-panel">
      <div class="flex items-start justify-between mb-3 flex-wrap gap-2">
        <div>
          <div class="flex items-center gap-2 mb-0.5">
            <i class="fa-solid fa-chart-column text-sm" id="kematian-panel-icon" style="color:#2563eb"></i>
            <span class="font-bold text-gray-800 text-sm" id="kematian-panel-title">Grafik per Kecamatan</span>
          </div>
          <p class="text-xs text-gray-500 ml-5">Data Sebaran Kabupaten JEMBER</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <div class="export-group">
            <button class="export-btn btn-png" onclick="exportPNG('kematian')">
              <i class="fa-solid fa-image"></i> PNG
            </button>
            <button class="export-btn btn-excel" onclick="exportExcel('kematian')">
              <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <button class="export-btn btn-pdf" onclick="exportPDF('kematian')">
              <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
          </div>
          <button onclick="closeKecChart('kematian')" class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition-colors">
            <i class="fa-solid fa-xmark"></i> Tutup
          </button>
        </div>
      </div>
      <div style="position:relative; height:260px">
        <canvas id="kematian-kec-chart"></canvas>
      </div>
      <p class="text-center text-xs text-gray-400 mt-2">KECAMATAN</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5 items-stretch">
      <div class="chart-card">
        <h3 class="font-bold text-gray-700 mb-4 text-sm flex items-center gap-2"><i class="fa-solid fa-chart-pie text-blue-400"></i> Perbandingan Kasus Kematian</h3>
        <div class="flex items-center justify-center" style="height:190px"><canvas id="donutKematian"></canvas></div>
        <div class="flex flex-wrap justify-center gap-3 mt-4 text-xs text-gray-500">
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#ef4444"></span>Kematian Ibu</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#f59e0b"></span>Kematian Bayi</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#22c55e"></span>Kematian Balita</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#3b82f6"></span>Kasus Abortus</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#a855f7"></span>Kasus IUFD</span>
        </div>
      </div>
      <div class="chart-card" style="display:flex; flex-direction:column; justify-content:center;">
        <h3 class="font-bold text-gray-700 mb-5 text-sm flex items-center gap-2"><i class="fa-solid fa-chart-bar text-blue-400"></i> Ringkasan Angka Kematian Ibu &amp; Anak</h3>
        <div class="space-y-5">
          <div class="bar-item"><div class="bar-label">Kematian Ibu (AKI)</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#ef4444" data-w="92%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Kematian Bayi (AKB)</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#f59e0b" data-w="80%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Kematian Balita</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#22c55e" data-w="55%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Kasus Abortus</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#3b82f6" data-w="38%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Kasus IUFD</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#a855f7" data-w="22%"></div></div></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-5" style="padding-left:150px">
          <span>0%</span><span>10%</span><span>20%</span><span>30%</span><span>40%</span><span>50%</span>
        </div>
      </div>
    </div>
    <div class="timestamp">Data terakhir diperbarui pada: 23/2/2026, 10.28.30</div>
  </div>

  <div class="section-wrap animate-fadeUp a3">
    <div class="sec-head">
      <div class="sec-badge sec-badge-green"><i class="fa-solid fa-apple-whole"></i><span>Status Gizi Anak</span></div>
      <h2>Ringkasan Status Gizi Anak</h2>
      <p>Menampilkan kondisi status gizi anak berdasarkan indikator BB/U, BB/TB, dan TB/U. Klik kartu untuk melihat grafik per kecamatan.</p>
      <div class="periode">Periode Data: 01/12/2025 – 31/12/2025</div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-2">
      <button class="stat-btn" onclick="showKecChart('gizi','capaian','Capaian Anak Terukur','#3b82f6',this,'active-green')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-children text-blue-400"></i> Capaian Anak Terukur</div>
        <div class="text-2xl font-extrabold text-gray-800 mt-1">130.269</div>
        <div class="text-xs text-blue-500 font-bold mt-1">88.3%</div>
        <div class="text-xs text-gray-400 mt-0.5">Sasaran: 147.608</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('gizi','stunting','Stunting','#ef4444',this,'active-green')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-ruler-vertical text-orange-400"></i> Stunting</div>
        <div class="text-xs text-gray-400 mb-1">TB Sangat Pendek &amp; TB Pendek (TB/U)</div>
        <div class="text-2xl font-extrabold text-gray-800 mt-1">9.502</div>
        <div class="text-xs text-red-400 font-bold mt-1">7.3%</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('gizi','wasting','Wasting','#f59e0b',this,'active-green')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-weight-scale text-yellow-500"></i> Wasting</div>
        <div class="text-xs text-gray-400 mb-1">Gizi Buruk &amp; Gizi Kurang (BB/TB)</div>
        <div class="text-2xl font-extrabold text-gray-800 mt-1">8.799</div>
        <div class="text-xs text-yellow-500 font-bold mt-1">6.8%</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('gizi','underweight','Underweight','#fb923c',this,'active-green')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-arrow-trend-down text-red-400"></i> Underweight</div>
        <div class="text-xs text-gray-400 mb-1">BB Kurang &amp; BB Sangat Kurang (BB/U)</div>
        <div class="text-2xl font-extrabold text-gray-800 mt-1">13.574</div>
        <div class="text-xs text-orange-400 font-bold mt-1">10.4%</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('gizi','giziBaik','Gizi Baik','#22c55e',this,'active-green')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-circle-check text-green-500"></i> Gizi Baik</div>
        <div class="text-xs text-gray-400 mb-1">BB Dan TB Ideal</div>
        <div class="text-2xl font-extrabold text-blue-600 mt-1">98.394</div>
        <div class="text-xs text-green-500 font-bold mt-1">75.5%</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
    </div>

    <div class="kec-chart-panel green-panel" id="gizi-chart-panel">
      <div class="flex items-start justify-between mb-3 flex-wrap gap-2">
        <div>
          <div class="flex items-center gap-2 mb-0.5">
            <i class="fa-solid fa-chart-column text-sm" id="gizi-panel-icon" style="color:#16a34a"></i>
            <span class="font-bold text-gray-800 text-sm" id="gizi-panel-title">Grafik per Kecamatan</span>
          </div>
          <p class="text-xs text-gray-500 ml-5">Data Sebaran Status Gizi Kabupaten JEMBER</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <div class="export-group">
            <button class="export-btn btn-png" onclick="exportPNG('gizi')">
              <i class="fa-solid fa-image"></i> PNG
            </button>
            <button class="export-btn btn-excel" onclick="exportExcel('gizi')">
              <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <button class="export-btn btn-pdf" onclick="exportPDF('gizi')">
              <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
          </div>
          <button onclick="closeKecChart('gizi')" class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition-colors">
            <i class="fa-solid fa-xmark"></i> Tutup
          </button>
        </div>
      </div>
      <div style="position:relative; height:260px">
        <canvas id="gizi-kec-chart"></canvas>
      </div>
      <p class="text-center text-xs text-gray-400 mt-2">KECAMATAN</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5 items-stretch">
      <div class="chart-card">
        <h3 class="font-bold text-gray-700 mb-4 text-sm flex items-center gap-2"><i class="fa-solid fa-chart-pie text-green-500"></i> Perbandingan Status Gizi Anak</h3>
        <div class="flex items-center justify-center" style="height:190px"><canvas id="donutGizi"></canvas></div>
        <div class="flex flex-wrap justify-center gap-3 mt-4 text-xs text-gray-500">
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#22c55e"></span>Gizi Baik</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#ef4444"></span>Stunting</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#f59e0b"></span>Wasting</span>
          <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#fb923c"></span>Underweight</span>
        </div>
      </div>
      <div class="chart-card" style="display:flex; flex-direction:column; justify-content:center;">
        <h3 class="font-bold text-gray-700 mb-5 text-sm flex items-center gap-2"><i class="fa-solid fa-chart-bar text-green-500"></i> Ringkasan Status Gizi Anak</h3>
        <div class="space-y-5">
          <div class="bar-item"><div class="bar-label">Stunting</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#ef4444" data-w="73%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Wasting</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#f59e0b" data-w="68%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Underweight</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#fb923c" data-w="55%"></div></div></div>
          <div class="bar-item"><div class="bar-label">Gizi Baik</div><div class="bar-track"><div class="bar-fill" style="width:0%;background:#22c55e" data-w="95%"></div></div></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-5" style="padding-left:150px">
          <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
        </div>
      </div>
    </div>
    <div class="timestamp">Data terakhir diperbarui pada: 23/2/2026, 10.28.30</div>
  </div>

  <div class="section-wrap animate-fadeUp a4">
    <div class="sec-head">
      <div class="sec-badge sec-badge-amber"><i class="fa-solid fa-house-chimney-user"></i><span>Keluarga Risiko Stunting</span></div>
      <h2>Ringkasan Keluarga Risiko Stunting <span style="color:#2563eb">(KRS)</span></h2>
      <p>Indikator risiko keluarga berdasarkan data KRS. Klik kartu untuk melihat grafik per kecamatan.</p>
      <div class="periode">Periode Data: N/A</div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-2">
      <button class="stat-btn" onclick="showKecChart('krs','totalKel','Total Keluarga','#3b82f6',this,'active-amber')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-house-chimney-user text-blue-400"></i> Total Keluarga</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-2">798.213</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('krs','risikoSejahtera','Risiko Kesejahteraan','#ef4444',this,'active-amber')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-hand-holding-heart text-red-400"></i> Risiko Kesejahteraan</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-2">47.714</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('krs','risikoLingkungan','Risiko Lingkungan','#22c55e',this,'active-amber')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-leaf text-green-500"></i> Risiko Lingkungan</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-2">109.053</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
      <button class="stat-btn" onclick="showKecChart('krs','risikoPUS','Risiko PUS (4 Terlalu)','#a855f7',this,'active-amber')">
        <div class="flex items-center gap-2 text-sm text-gray-400 font-semibold mb-0 -mt-1"><i class="fa-solid fa-venus-mars text-purple-400"></i> Risiko PUS (4 terlalu)</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-2">156.717</div>
        <span class="click-hint"><i class="fa-solid fa-chart-column"></i> Lihat grafik</span>
      </button>
    </div>

    <div class="kec-chart-panel amber-panel" id="krs-chart-panel">
      <div class="flex items-start justify-between mb-3 flex-wrap gap-2">
        <div>
          <div class="flex items-center gap-2 mb-0.5">
            <i class="fa-solid fa-chart-column text-sm" id="krs-panel-icon" style="color:#b45309"></i>
            <span class="font-bold text-gray-800 text-sm" id="krs-panel-title">Grafik per Kecamatan</span>
          </div>
          <p class="text-xs text-gray-500 ml-5">Data Sebaran KRS Kabupaten JEMBER</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <div class="export-group">
            <button class="export-btn btn-png" onclick="exportPNG('krs')">
              <i class="fa-solid fa-image"></i> PNG
            </button>
            <button class="export-btn btn-excel" onclick="exportExcel('krs')">
              <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <button class="export-btn btn-pdf" onclick="exportPDF('krs')">
              <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
          </div>
          <button onclick="closeKecChart('krs')" class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition-colors">
            <i class="fa-solid fa-xmark"></i> Tutup
          </button>
        </div>
      </div>
      <div style="position:relative; height:260px">
        <canvas id="krs-kec-chart"></canvas>
      </div>
      <p class="text-center text-xs text-gray-400 mt-2">KECAMATAN</p>
    </div>

    <div class="timestamp">Data terakhir diperbarui pada: 23/2/2026, 10.28.30</div>
  </div>

  <div class="bg-white rounded-2xl p-8 shadow-sm animate-fadeUp a5">
    <h2 class="text-xl font-extrabold text-gray-800 text-center mb-7">Lihat Lebih Banyak Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="sector-card"><div class="sector-icon"><i class="fa-solid fa-graduation-cap text-white text-lg"></i> </div><div><div class="text-white font-bold text-sm">Bidang Pendidikan</div><div class="text-blue-200 text-xs mt-0.5">Akses data komprehensif mengenai rasio guru, jumlah siswa, dan statistik.</div></div></div>
      <div class="sector-card"><div class="sector-icon"><i class="fa-solid fa-industry text-white text-lg"></i></div><div><div class="text-white font-bold text-sm">Bidang Perindustrian</div><div class="text-blue-200 text-xs mt-0.5">Akses data komprehensif mengenai rasio guru, jumlah siswa, dan statistik.</div></div></div>
      <div class="sector-card"><div class="sector-icon"><i class="fa-solid fa-umbrella-beach text-white text-lg"></i></div><div><div class="text-white font-bold text-sm">Bidang Pariwisata</div><div class="text-blue-200 text-xs mt-0.5">Akses data komprehensif mengenai rasio guru, jumlah siswa, dan statistik.</div></div></div>
      <div class="sector-card"><div class="sector-icon"><i class="fa-solid fa-seedling text-white text-lg"></i></div><div><div class="text-white font-bold text-sm">Bidang Pertanian</div><div class="text-blue-200 text-xs mt-0.5">Akses data komprehensif mengenai rasio guru, jumlah siswa, dan statistik.</div></div></div>
      <div class="sector-card"><div class="sector-icon"><i class="fa-solid fa-store text-white text-lg"></i></div><div><div class="text-white font-bold text-sm">Bidang Perdagangan</div><div class="text-blue-200 text-xs mt-0.5">Akses data komprehensif mengenai rasio guru, jumlah siswa, dan statistik.</div></div></div>
      <div class="sector-card"><div class="sector-icon"><i class="fa-solid fa-people-arrows text-white text-lg"></i></div><div><div class="text-white font-bold text-sm">Bidang Transmigrasi</div><div class="text-blue-200 text-xs mt-0.5">Akses data komprehensif mengenai rasio guru, jumlah siswa, dan statistik.</div></div></div>
    </div>
  </div>

</div>

<footer class="mt-12 py-6 text-center text-xs text-gray-400">&copy; 2024 Dashboard Kabupaten Jember. Seluruh hak cipta dilindungi.</footer>

<script>
const kecamatan = [
  'Jenggawah','Mayang','Ambulu','Kaliwates','Pakusari','Silo',
  'Sumberjambe','Wuluhan','Arjasa','Balung','Kencong','Ledokombo',
  'Sukorambi','Sukowono','Sumberbaru','Umbulsari'
];

const barColors = [
  '#22d3ee','#0d9488','#3b82f6','#22c55e','#eab308','#60a5fa',
  '#5eead4','#67e8f9','#2dd4bf','#4ade80','#fb923c','#c084fc',
  '#38bdf8','#a3e635','#fbbf24','#34d399'
];

const chartData = {
  kematian: {
    total:   [3,3,2,2,2,2,2,2,1,1,1,1,1,1,1,0],
    aki:     [1,0,1,0,0,1,0,1,0,0,0,0,0,0,0,0],
    akb:     [3,3,2,2,2,2,2,2,1,1,1,1,1,1,1,0],
    akaba:   [2,2,1,1,2,1,2,1,0,1,1,0,1,0,1,0],
    abortus: [3,2,2,1,2,1,1,2,1,1,1,1,1,1,1,0],
    iufd:    [2,1,1,2,1,2,1,1,0,1,0,1,0,1,1,0],
  },
  gizi: {
    capaian:     [9200,8700,7500,8200,6300,7100,6800,8000,4200,5100,4800,5600,3900,4500,5200,4100],
    stunting:    [720,680,590,610,480,550,510,600,310,390,360,420,290,340,390,310],
    wasting:     [610,570,510,530,410,490,450,530,260,330,300,360,240,280,330,260],
    underweight: [980,920,810,850,660,740,700,820,420,510,480,560,380,450,510,400],
    giziBaik:    [7800,7400,6500,7100,5400,6200,5900,6900,3600,4400,4100,4800,3400,3900,4500,3500],
  },
  krs: {
    totalKel:       [52000,48000,43000,67000,28000,38000,35000,44000,22000,31000,29000,34000,21000,26000,31000,24000],
    risikoSejahtera:[3100,2900,2600,4100,1700,2300,2100,2700,1300,1900,1800,2100,1300,1600,1900,1500],
    risikoLingkungan:[6800,6200,5600,8800,3700,5000,4700,5700,2900,4100,3800,4500,2800,3400,4100,3200],
    risikoPUS:      [9800,9200,8300,12800,5400,7300,6900,8400,4200,6000,5600,6600,4100,5000,6000,4700],
  }
};

const chartInstances = { kematian: null, gizi: null, krs: null };
const activeButtons  = { kematian: null, gizi: null, krs: null };
const currentState   = { kematian: {}, gizi: {}, krs: {} }; 

function showKecChart(section, key, label, color, btn, activeClass) {
  const panel = document.getElementById(section + '-chart-panel');
  const canvasId = section + '-kec-chart';
  const titleEl  = document.getElementById(section + '-panel-title');

  if (activeButtons[section]) {
    activeButtons[section].classList.remove('active', 'active-green', 'active-amber');
  }

  if (activeButtons[section] === btn && panel.classList.contains('show')) {
    closeKecChart(section);
    return;
  }

  btn.classList.add(activeClass);
  activeButtons[section] = btn;

  titleEl.textContent = 'Grafik ' + label + ' per Kecamatan';

  currentState[section] = { key, label, color };

  panel.classList.add('show');

  if (chartInstances[section]) {
    chartInstances[section].destroy();
    chartInstances[section] = null;
  }

  const values = chartData[section][key];
  const ctx = document.getElementById(canvasId).getContext('2d');

  chartInstances[section] = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: kecamatan,
      datasets: [{
        label: label,
        data: values,
        backgroundColor: barColors,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (items) => 'Kec. ' + items[0].label,
            label: (item) => ' ' + label + ': ' + item.raw.toLocaleString('id-ID')
          }
        }
      },
      scales: {
        x: {
          ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 35 },
          grid: { display: false }
        },
        y: {
          beginAtZero: true,
          ticks: { font: { size: 11 } },
          grid: { color: 'rgba(0,0,0,0.05)' },
          title: { display: true, text: 'Jumlah', font: { size: 11 } }
        }
      },
      animation: { duration: 600, easing: 'easeOutQuart' }
    }
  });

  setTimeout(() => panel.scrollIntoView({ behavior:'smooth', block:'nearest' }), 80);
}

function closeKecChart(section) {
  const panel = document.getElementById(section + '-chart-panel');
  panel.classList.remove('show');
  if (activeButtons[section]) {
    activeButtons[section].classList.remove('active','active-green','active-amber');
    activeButtons[section] = null;
  }
  if (chartInstances[section]) {
    chartInstances[section].destroy();
    chartInstances[section] = null;
  }
}

function exportPNG(section) {
  const chart = chartInstances[section];
  if (!chart) return;
  const state = currentState[section];
  const fileName = 'Grafik_' + (state.label || section) + '_Kecamatan_Jember.png';

  const srcCanvas = chart.canvas;
  const exportCanvas = document.createElement('canvas');
  exportCanvas.width = srcCanvas.width;
  exportCanvas.height = srcCanvas.height;
  const ctx = exportCanvas.getContext('2d');
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
  ctx.drawImage(srcCanvas, 0, 0);

  const link = document.createElement('a');
  link.href = exportCanvas.toDataURL('image/png');
  link.download = fileName;
  link.click();
}

function exportExcel(section) {
  const state = currentState[section];
  if (!state.key) return;

  const values = chartData[section][state.key];
  const label = state.label || section;

  const wsData = [
    ['Dashboard Kabupaten Jember'],
    ['Grafik ' + label + ' per Kecamatan'],
    [''],
    ['No', 'Kecamatan', label]
  ];
  kecamatan.forEach((kec, i) => {
    wsData.push([i + 1, kec, values[i]]);
  });
  wsData.push(['']);
  wsData.push(['Sumber: Dashboard Kabupaten Jember']);
  wsData.push(['Tanggal Ekspor: ' + new Date().toLocaleDateString('id-ID')]);

  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.aoa_to_sheet(wsData);

  ws['!cols'] = [{ wch: 6 }, { wch: 20 }, { wch: 18 }];

  XLSX.utils.book_append_sheet(wb, ws, label.substring(0, 31));
  XLSX.writeFile(wb, 'Data_' + label + '_Kecamatan_Jember.xlsx');
}

function exportPDF(section) {
  const chart = chartInstances[section];
  if (!chart) return;
  const state = currentState[section];
  const label = state.label || section;
  const values = chartData[section][state.key];

  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

  const pageW = pdf.internal.pageSize.getWidth();
  const margin = 15;

  pdf.setFillColor(37, 99, 235);
  pdf.rect(0, 0, pageW, 18, 'F');
  pdf.setTextColor(255, 255, 255);
  pdf.setFontSize(13);
  pdf.setFont('helvetica', 'bold');
  pdf.text('Dashboard Kabupaten Jember', margin, 12);
  pdf.setFontSize(9);
  pdf.setFont('helvetica', 'normal');
  pdf.text('Data Sebaran per Kecamatan', pageW - margin, 12, { align: 'right' });

  pdf.setTextColor(30, 41, 59);
  pdf.setFontSize(12);
  pdf.setFont('helvetica', 'bold');
  pdf.text('Grafik ' + label + ' per Kecamatan – Kabupaten JEMBER', margin, 28);

  const srcCanvas = chart.canvas;
  const exportCanvas = document.createElement('canvas');
  exportCanvas.width = srcCanvas.width;
  exportCanvas.height = srcCanvas.height;
  const ctx2 = exportCanvas.getContext('2d');
  ctx2.fillStyle = '#ffffff';
  ctx2.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
  ctx2.drawImage(srcCanvas, 0, 0);

  const imgData = exportCanvas.toDataURL('image/png');
  const chartH = 90;
  const chartW = pageW - margin * 2;
  pdf.addImage(imgData, 'PNG', margin, 32, chartW, chartH);

  const tableY = 32 + chartH + 8;
  pdf.setFontSize(8);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(255, 255, 255);
  pdf.setFillColor(37, 99, 235);
  pdf.rect(margin, tableY, chartW, 6, 'F');
  pdf.text('No', margin + 2, tableY + 4);
  pdf.text('Kecamatan', margin + 12, tableY + 4);
  pdf.text(label, margin + 55, tableY + 4);

  pdf.setFont('helvetica', 'normal');
  kecamatan.forEach((kec, i) => {
    const rowY = tableY + 6 + (i * 5.5);
    if (rowY > 195) return; 
    if (i % 2 === 0) {
      pdf.setFillColor(239, 246, 255);
      pdf.rect(margin, rowY, chartW, 5.5, 'F');
    }
    pdf.setTextColor(30, 41, 59);
    pdf.text(String(i + 1), margin + 2, rowY + 4);
    pdf.text(kec, margin + 12, rowY + 4);
    pdf.text(values[i].toLocaleString('id-ID'), margin + 55, rowY + 4);
  });

  pdf.setFontSize(8);
  pdf.setTextColor(148, 163, 184);
  pdf.text('Diekspor pada: ' + new Date().toLocaleDateString('id-ID') + ' | Dashboard Kabupaten Jember', margin, 200);

  pdf.save('Grafik_' + label + '_Kecamatan_Jember.pdf');
}

new Chart(document.getElementById('donutKematian'), {
  type: 'doughnut',
  data: {
    labels: ['Kematian Ibu','Kematian Bayi','Kematian Balita','Kasus Abortus','Kasus IUFD'],
    datasets: [{ data: [24,263,278,649,181], backgroundColor: ['#ef4444','#f59e0b','#22c55e','#3b82f6','#a855f7'], borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
  },
  options: { cutout: '68%', plugins: { legend: { display: false } }, animation: { animateRotate: true, duration: 900 } }
});

new Chart(document.getElementById('donutGizi'), {
  type: 'doughnut',
  data: {
    labels: ['Gizi Baik','Stunting','Wasting','Underweight'],
    datasets: [{ data: [98394,9502,8799,13574], backgroundColor: ['#22c55e','#ef4444','#f59e0b','#fb923c'], borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
  },
  options: { cutout: '68%', plugins: { legend: { display: false } }, animation: { animateRotate: true, duration: 900 } }
});

setTimeout(() => {
  document.querySelectorAll('.bar-fill').forEach(el => { el.style.width = el.dataset.w; });
}, 400);
</script>
</body>
</html>