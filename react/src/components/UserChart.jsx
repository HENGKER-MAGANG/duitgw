import { useEffect, useState } from 'react';
import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid } from 'recharts';
import { formatRupiah, formatMonthLabel } from '../lib/format';

function CustomTooltip({ active, payload, label }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="rounded-lg border border-tinta/10 bg-white px-3 py-2 text-xs shadow-lg">
      <p className="mb-1 font-semibold text-tinta">{formatMonthLabel(label)}</p>
      {payload.map((p) => (
        <p key={p.dataKey} style={{ color: p.color }}>
          {p.dataKey === 'masuk' ? 'Pemasukan' : 'Pengeluaran'}: {formatRupiah(p.value)}
        </p>
      ))}
    </div>
  );
}

export default function UserChart({ apiUrl }) {
  const [data, setData] = useState(null);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetch(apiUrl, { headers: { Accept: 'application/json' } })
      .then((r) => r.json())
      .then((json) => setData(json.trend || []))
      .catch(() => setError(true));
  }, [apiUrl]);

  if (error) return <p className="dw-empty">Gagal memuat grafik.</p>;
  if (!data) return <p className="dw-empty">Memuat grafik...</p>;
  if (data.length === 0) return <p className="dw-empty">Belum ada data transaksi untuk ditampilkan.</p>;

  return (
    <ResponsiveContainer width="100%" height={260}>
      <LineChart data={data} margin={{ top: 5, right: 10, left: -20, bottom: 0 }}>
        <CartesianGrid strokeDasharray="3 3" stroke="#1B1B1B0D" vertical={false} />
        <XAxis dataKey="bulan" tickFormatter={formatMonthLabel} tick={{ fontSize: 11, fill: '#1B1B1B99' }} axisLine={false} tickLine={false} />
        <YAxis tickFormatter={(v) => (v >= 1000000 ? `${(v / 1000000).toFixed(0)}jt` : v)} tick={{ fontSize: 11, fill: '#1B1B1B99' }} axisLine={false} tickLine={false} width={44} />
        <Tooltip content={<CustomTooltip />} />
        <Line type="monotone" dataKey="masuk" stroke="#2D6A4F" strokeWidth={2.5} dot={{ r: 3 }} name="Pemasukan" />
        <Line type="monotone" dataKey="keluar" stroke="#C1121F" strokeWidth={2.5} dot={{ r: 3 }} name="Pengeluaran" />
      </LineChart>
    </ResponsiveContainer>
  );
}
