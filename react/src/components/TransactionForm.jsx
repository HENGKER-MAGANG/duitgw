import { useRef, useState } from 'react';
import { formatRupiah, parseAmountInput } from '../lib/format';

export default function TransactionForm({ action, redirect, mode = 'create', categories = [], initial = null }) {
  const isEdit = mode === 'edit';
  const fileInputRef = useRef(null);

  const [type, setType] = useState(initial?.type || 'keluar');
  const [amount, setAmount] = useState(initial?.amount ? String(Math.round(initial.amount)) : '');
  const [description, setDescription] = useState(initial?.description || '');
  const [date, setDate] = useState(initial?.transaction_date || new Date().toISOString().slice(0, 10));
  const [categoryName, setCategoryName] = useState('');
  const [preview, setPreview] = useState(initial?.proof_url || null);
  const [dragOver, setDragOver] = useState(false);
  const [errors, setErrors] = useState({});
  const [submitting, setSubmitting] = useState(false);

  const filteredCategories = categories.filter((c) => c.type === type);

  function handleFile(file) {
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      setErrors((e) => ({ ...e, proof_photo: 'File harus berupa gambar.' }));
      return;
    }
    setErrors((e) => ({ ...e, proof_photo: null }));
    const reader = new FileReader();
    reader.onload = () => setPreview(reader.result);
    reader.readAsDataURL(file);
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setErrors({});

    if (!isEdit && !fileInputRef.current?.files?.[0]) {
      setErrors({ proof_photo: 'Bukti foto wajib dilampirkan.' });
      return;
    }

    setSubmitting(true);
    const formData = new FormData();
    formData.append('type', type);
    formData.append('amount', amount || '0');
    formData.append('description', description);
    formData.append('transaction_date', date);
    if (categoryName) formData.append('category_name', categoryName);
    if (fileInputRef.current?.files?.[0]) {
      formData.append('proof_photo', fileInputRef.current.files[0]);
    }

    try {
      const res = await fetch(action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
      });
      const data = await res.json();

      if (!res.ok || !data.success) {
        setErrors(data.errors || { general: data.message || 'Terjadi kesalahan.' });
        setSubmitting(false);
        return;
      }

      window.location.href = data.redirect || redirect;
    } catch (err) {
      setErrors({ general: 'Gagal terhubung ke server. Coba lagi.' });
      setSubmitting(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} className="dw-form" encType="multipart/form-data">
      {errors.general && <div className="dw-alert dw-alert-error">{errors.general}</div>}

      <div className="grid grid-cols-2 gap-2.5">
        <button
          type="button"
          onClick={() => setType('keluar')}
          className={`rounded-lg border px-4 py-3 text-sm font-semibold transition ${
            type === 'keluar' ? 'border-merah-500 bg-merah-50 text-merah-600' : 'border-tinta/15 text-tinta/50'
          }`}
        >
          ↑ Pengeluaran
        </button>
        <button
          type="button"
          onClick={() => setType('masuk')}
          className={`rounded-lg border px-4 py-3 text-sm font-semibold transition ${
            type === 'masuk' ? 'border-hijau bg-hijau/5 text-hijau' : 'border-tinta/15 text-tinta/50'
          }`}
        >
          ↓ Pemasukan
        </button>
      </div>

      <label className="dw-field">
        <span>Jumlah</span>
        <div className="relative">
          <span className="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 font-mono text-sm text-tinta/40">Rp</span>
          <input
            type="text"
            inputMode="numeric"
            required
            value={amount ? Number(amount).toLocaleString('id-ID') : ''}
            onChange={(e) => setAmount(parseAmountInput(e.target.value))}
            placeholder="0"
            className="!pl-10 font-mono tabular-nums"
            style={{ width: '100%', borderRadius: '0.5rem', border: '1px solid rgba(27,27,27,0.15)', padding: '0.625rem 0.875rem 0.625rem 2.5rem', fontSize: '0.875rem' }}
          />
        </div>
        {errors.amount && <span className="text-xs text-merah-600">{errors.amount}</span>}
      </label>

      <label className="dw-field">
        <span>Keterangan</span>
        <input
          type="text"
          required
          value={description}
          onChange={(e) => setDescription(e.target.value)}
          placeholder={type === 'masuk' ? 'Contoh: Gaji bulan ini' : 'Contoh: Makan siang'}
        />
        {errors.description && <span className="text-xs text-merah-600">{errors.description}</span>}
      </label>

      <label className="dw-field">
        <span>Kategori (opsional)</span>
        <input
          type="text"
          list="dw-category-list"
          value={categoryName}
          onChange={(e) => setCategoryName(e.target.value)}
          placeholder="Contoh: Transportasi"
        />
        <datalist id="dw-category-list">
          {filteredCategories.map((c) => (
            <option key={c.name} value={c.name} />
          ))}
        </datalist>
      </label>

      <label className="dw-field">
        <span>Tanggal</span>
        <input type="date" required value={date} onChange={(e) => setDate(e.target.value)} />
        {errors.transaction_date && <span className="text-xs text-merah-600">{errors.transaction_date}</span>}
      </label>

      <div className="dw-field">
        <span>Bukti Foto {!isEdit && <em className="text-merah-600 not-italic">*wajib</em>}</span>
        <div
          onDragOver={(e) => { e.preventDefault(); setDragOver(true); }}
          onDragLeave={() => setDragOver(false)}
          onDrop={(e) => {
            e.preventDefault();
            setDragOver(false);
            const file = e.dataTransfer.files?.[0];
            if (file && fileInputRef.current) {
              const dt = new DataTransfer();
              dt.items.add(file);
              fileInputRef.current.files = dt.files;
              handleFile(file);
            }
          }}
          onClick={() => fileInputRef.current?.click()}
          className={`flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed p-6 text-center transition ${
            dragOver ? 'border-merah-500 bg-merah-50' : 'border-tinta/15 hover:border-merah-300'
          }`}
        >
          {preview ? (
            <img src={preview} alt="Pratinjau bukti" className="h-32 w-32 rounded-lg object-cover shadow" />
          ) : (
            <>
              <span className="text-2xl">📎</span>
              <span className="text-sm text-tinta/50">Klik atau seret foto struk/nota ke sini</span>
            </>
          )}
          <input
            ref={fileInputRef}
            type="file"
            accept="image/jpeg,image/png,image/webp"
            className="hidden"
            onChange={(e) => handleFile(e.target.files?.[0])}
          />
        </div>
        {errors.proof_photo && <span className="text-xs text-merah-600">{errors.proof_photo}</span>}
      </div>

      <button type="submit" disabled={submitting} className="dw-btn dw-btn-primary dw-btn-block">
        {submitting ? 'Menyimpan...' : isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi'}
      </button>
    </form>
  );
}
