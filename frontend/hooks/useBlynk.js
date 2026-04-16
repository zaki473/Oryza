import useSWR from 'swr';

// Endpoint dari .env (Contoh: NEXT_PUBLIC_BLYNK_TOKEN="gixrkPVNMu2y...")
const BLYNK_TOKEN = process.env.NEXT_PUBLIC_BLYNK_TOKEN;
const BLYNK_URL = 'https://blynk.cloud/external/api';

const fetcher = (url) => fetch(url).then((res) => res.json());

export function useBlynk(pins = ['V0', 'V2', 'V3', 'V5', 'V6', 'V9']) {
  // Membuat query string: ...&V0&V2&V3...
  const query = pins.join('&');
  
  // Mengambil data setiap 2 detik (2000ms)
  const { data, error, mutate } = useSWR(
    `${BLYNK_URL}/get?token=${BLYNK_TOKEN}&${query}`,
    fetcher,
    { refreshInterval: 2000 }
  );

  // Fungsi untuk mengontrol switch/pompa (V4 manual toggle)
  const updatePin = async (pin, value) => {
    try {
      await fetch(`${BLYNK_URL}/update?token=${BLYNK_TOKEN}&${pin}=${value}`);
      // Refresh data lokal segera setelah update agar UI terasa responsif
      mutate();
    } catch (err) {
      console.error("Gagal mengupdate pin Blynk", err);
    }
  };

  return {
    data,
    isLoading: !error && !data,
    isError: error,
    updatePin
  };
}
