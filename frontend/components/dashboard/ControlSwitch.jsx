export default function ControlSwitch({ label, isOn, onToggle }) {
  return (
    <div className="flex bg-white items-center justify-between p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
      <div className="flex flex-col">
        <span className="text-base font-semibold text-gray-800">{label}</span>
        <span className="text-xs text-gray-500 mt-1">{isOn ? 'Aktif - Sedang Menyiram' : 'Non-Aktif - Pompa Mati'}</span>
      </div>
      <button
        onClick={onToggle}
        className={`relative inline-flex h-8 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 ${
          isOn ? 'bg-emerald-500' : 'bg-gray-200'
        }`}
      >
        <span
          className={`pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-300 ease-in-out ${
            isOn ? 'translate-x-6' : 'translate-x-0'
          }`}
        />
      </button>
    </div>
  );
}
