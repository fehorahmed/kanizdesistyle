export function formatCurrency(value) {
  if (isNaN(value)) return value;
  return "৳" + new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 0,
  }).format(value);
}