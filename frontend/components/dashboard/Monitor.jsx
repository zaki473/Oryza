import { useBlynk } from '../../hooks/useBlynk';
import SensorCard from './SensorCard';
import ControlSwitch from './ControlSwitch';

export default function Monitor() {
  // Fetch data dari ESP32 (V0=Kelembapan, V2=Jarak, V4=Switch Pompa Manual, V5=Status Pompa, V6=Timer, V9=Status Hama)
  const { data, isLoading, isError, updatePin } = useBlynk(['V0', 'V2', 'V4', 'V5', 'V6', 'V9']);

  if (isError) return <div className="p-4 bg-rose-50 text-[#800000] border border-maroon-200 rounded-xl">Koneksi ke perangkat IoT terputus! Pastikan modul ESP32 online.</div>;
  if (isLoading) return <div className="p-4 text-emerald-600 animate-pulse font-medium">Melakukan sinkronisasi dengan hardware...</div>;

  // Mapping data dari API Blynk
  const moisture = data?.V0 || 0;
  const pestDistance = data?.V2 || 0;
  const pumpSwitchState = data?.V4 === "1"; // Toggle Command
  const pumpStatusValue = data?.V5 === "1"; // Actual Hardware state
  const countdown = data?.V6 || "00:00:00";
  const securityStatus = data?.V9 || "0"; 

  // Logika mendeteksi hama (warna aksen maroon)
  const isPestDetected = securityStatus === "1" || parseInt(pestDistance) < 50; 

  const handleTogglePump = () => {
    const newValue = pumpSwitchState ? "0" : "1";
    updatePin('V4', newValue);
  };

  return (
    <div className="max-w-6xl mx-auto space-y-6 pt-6 font-sans">
      
      {/* Header & Status Indicator */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
          <h2 className="text-2xl font-bold text-gray-800 tracking-tight">Smart Rice Monitoring</h2>
          <p className="text-sm text-gray-500 mt-1">Sinkronisasi data real-time aktif.</p>
        </div>
        
        {/* Status Indicator Badge (Aman / Hama!) */}
        {isPestDetected ? (
          <span className="inline-flex animate-pulse items-center gap-2 py-2 px-4 rounded-full text-sm font-bold bg-red-100 text-[#800000] border border-[#800000]/20 shadow-sm">
            <span className="w-2.5 h-2.5 rounded-full bg-[#800000] shadow-[0_0_8px_#800000]"></span>
            HAMA TERDETEKSI!
          </span>
        ) : (
          <span className="inline-flex items-center gap-2 py-2 px-4 rounded-full text-sm font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
            <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_#10B981]"></span>
            Sistem Aman
          </span>
        )}
      </div>

      {/* Grid Utama Dashboard */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <SensorCard 
          title="Kelembapan Tanah" 
          value={moisture} 
          unit="%" 
          // Icon Tetesan Air
          icon={<svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>}
        />
        
        <SensorCard 
          title="Jarak Benda (Ultrasonik)" 
          value={pestDistance} 
          unit="cm" 
          alert={isPestDetected}
          // Icon Radar / Warning
          icon={<svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>}
        />
        
        {/* Countdown Timer Card dengan Gradient Emerald */}
        <div className="p-6 rounded-2xl shadow-sm border border-emerald-600 bg-gradient-to-br from-emerald-600 to-emerald-800 text-white flex flex-col justify-between hover:shadow-lg transition-shadow">
          <div>
            <p className="text-emerald-100 text-sm font-medium mb-1 uppercase tracking-wider">Durasi Sisa Penyiraman</p>
            <h3 className="tracking-widest font-mono text-4xl font-bold mt-2 dropshadow-md">
              {countdown}
            </h3>
          </div>
          <div className="mt-6 flex items-center justify-between">
             <span className="text-xs bg-black/30 px-2.5 py-1.5 rounded-md font-mono inline-block text-emerald-50">Pin: V6</span>
             {countdown !== "00:00:00" && (
                <span className="flex h-3 w-3 relative">
                  <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span className="relative inline-flex rounded-full h-3 w-3 bg-emerald-300"></span>
                </span>
             )}
          </div>
        </div>
      </div>

      {/* Control Panel Pompa */}
      <div className="pt-8">
        <h3 className="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Panel Kontrol Irigasi</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          {/* Komponen Switch Input */}
          <ControlSwitch 
            label="Kontrol Manual Pompa Air" 
            isOn={pumpSwitchState} 
            onToggle={handleTogglePump} 
          />
          
          {/* Indikator Status Pompa Hardware Aktif/Mati */}
          <div className={`flex items-center p-5 rounded-2xl border shadow-sm transition-colors ${pumpStatusValue ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100'}`}>
             <div className={`p-4 rounded-full mr-5 ${pumpStatusValue ? 'bg-blue-100 text-blue-600 shadow-sm' : 'bg-gray-100 text-gray-400'}`}>
                {/* Icon Wave / Water */}
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
             </div>
             <div>
               <span className="text-base font-semibold text-gray-800">Status Output Perangkat</span>
               <p className={`text-sm mt-1 font-medium ${pumpStatusValue ? 'text-blue-600' : 'text-gray-500'}`}>
                 {pumpStatusValue ? 'Menyemprotkan Air...' : 'Hardware Standby / Non-aktif'}
               </p>
             </div>
          </div>

        </div>
      </div>
      
    </div>
  );
}
