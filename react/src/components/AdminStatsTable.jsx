import { useEffect, useMemo, useState } from 'react';
import { formatRupiah } from '../lib/format';

export default function AdminStatsTable({ apiUrl, userUrl }) {
  const [users, setUsers] = useState(null);
  const [error, setError] = useState(false);
  const [search, setSearch] = useState('');
  const [sortKey, setSortKey] = useState('saldo');
  const [sortDir, setSortDir] = useState('desc');

  useEffect(() => {
    fetch(apiUrl, { headers: { Accept: 'application/json' } })
      .then((r) => r.json())
      .then((json) => setUsers(json.users || []))
      .catch(() => setError(true));
  }, [apiUrl]);

  const rows = useMemo(() => {
    if (!users) return [];
    let filtered = users.filter(
      (u) => u.name.toLowerCase().includes(search.toLowerCase()) || u.email.toLowerCase().includes(search.toLowerCase())
    );
    filtered.sort((a, b) => {
      const av = a[sortKey];
      const bv = b[sortKey];
      if (typeof av === 'string') {
        return sortDir === 'asc' ? av.localeCompare(bv) : bv.localeCompare(av);
      }
      return sortDir === 'asc' ? av - bv : bv - av;
    });
    return filtered;
  }, [users, search, sortKey, sortDir]);

  function toggleSort(key) {
    if (sortKey === key) {
      setSortDir((d) => (d === 'asc' ? 'desc' : 'asc'));
    } else {
      setSortKey(key);
      setSortDir('desc');
    }
  }

  if (error) return <p className="dw-empty">Gagal memuat data pengguna.</p>;
  if (!users) return <p className="dw-empty">Memuat data pengguna...</p>;

  const sortIcon = (key) => (sortKey === key ? (sortDir === 'asc' ? '▲' : '▼') : '');

  return (
    <div>
      <input
        type="text"
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        placeholder="Cari nama atau email..."
        className="dw-input mb-3"
      />

      {rows.length === 0 ? (
        <p className="dw-empty">Tidak ada pengguna yang cocok.</p>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full border-collapse text-sm">
            <thead>
              <tr className="border-b border-tinta/10 text-left text-xs uppercase tracking-wide text-tinta/45">
                <th className="cursor-pointer select-none py-2 pr-3" onClick={() => toggleSort('name')}>Pengguna {sortIcon('name')}</th>
                <th className="cursor-pointer select-none py-2 pr-3 text-right" onClick={() => toggleSort('total_masuk')}>Pemasukan {sortIcon('total_masuk')}</th>
                <th className="cursor-pointer select-none py-2 pr-3 text-right" onClick={() => toggleSort('total_keluar')}>Pengeluaran {sortIcon('total_keluar')}</th>
                <th className="cursor-pointer select-none py-2 pr-3 text-right" onClick={() => toggleSort('saldo')}>Saldo {sortIcon('saldo')}</th>
                <th className="cursor-pointer select-none py-2 text-right" onClick={() => toggleSort('total_transaksi')}>Transaksi {sortIcon('total_transaksi')}</th>
              </tr>
            </thead>
            <tbody>
              {rows.map((u) => (
                <tr key={u.id} className="border-b border-tinta/5 hover:bg-kertas-off">
                  <td className="py-2.5 pr-3">
                    <a href={userUrl + u.id} className="font-medium text-tinta no-underline hover:text-merah-600">{u.name}</a>
                    <div className="text-xs text-tinta/40">{u.email}</div>
                  </td>
                  <td className="py-2.5 pr-3 text-right font-mono tabular-nums text-hijau">{formatRupiah(u.total_masuk)}</td>
                  <td className="py-2.5 pr-3 text-right font-mono tabular-nums text-merah-600">{formatRupiah(u.total_keluar)}</td>
                  <td className="py-2.5 pr-3 text-right font-mono font-semibold tabular-nums">{formatRupiah(u.saldo)}</td>
                  <td className="py-2.5 text-right font-mono tabular-nums text-tinta/60">{u.total_transaksi}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
