export function formatRupiah(value) {
  const n = Number(value) || 0;
  return 'Rp' + n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
}

export function parseAmountInput(raw) {
  // Strip anything that isn't a digit (handles "150.000" style typing)
  const digits = String(raw).replace(/[^\d]/g, '');
  return digits === '' ? '' : digits;
}

export function formatMonthLabel(yyyyMm) {
  if (!yyyyMm) return '';
  const [y, m] = yyyyMm.split('-');
  const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  return `${bulan[parseInt(m, 10) - 1]} '${y.slice(2)}`;
}
