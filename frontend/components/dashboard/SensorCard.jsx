export default function SensorCard({ title, value, unit, icon, alert }) {
  return (
    <div className={`p-6 rounded-2xl shadow-sm border transition-all duration-300 bg-white hover:shadow-md ${alert ? 'border-[#800000] bg-rose-50' : 'border-gray-100'}`}>
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-gray-500 mb-1">{title}</p>
          <div className="flex items-baseline space-x-1">
            <h3 className={`text-4xl font-bold tracking-tight ${alert ? 'text-[#800000]' : 'text-gray-800'}`}>
              {value !== undefined ? value : '--'}
            </h3>
            <span className="text-sm font-semibold text-gray-400">{unit}</span>
          </div>
        </div>
        <div className={`p-3 rounded-xl ${alert ? 'bg-red-100 text-[#800000]' : 'bg-emerald-50 text-emerald-600'}`}>
          {icon}
        </div>
      </div>
    </div>
  );
}
