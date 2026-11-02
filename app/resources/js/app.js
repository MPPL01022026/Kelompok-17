const CART_STORAGE_KEY = 'ayamo_cart_v1';

function readCart() {
    try {
        const parsed = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || '{}');

        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch {
        return {};
    }
}

function showCartToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = `fixed right-4 bottom-5 z-[80] rounded-full px-5 py-3 text-sm font-bold text-white shadow-2xl transition ${isError ? 'bg-red-700' : 'bg-sand-900'}`;
    toast.textContent = String(message);
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 1800);
}

if (! window.AyamoCart) {
    window.AyamoCart = {
        STORAGE_KEY: CART_STORAGE_KEY,

        getMap: readCart,

        saveMap(map) {
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(map));
            this.syncUI();
        },

        clear() {
            localStorage.removeItem(this.STORAGE_KEY);
            this.syncUI();
        },

        add(product, delta = 1) {
            const map = this.getMap();
            const stock = Math.max(0, Number.parseInt(product.stock ?? 0, 10));
            const existing = map[product.id] || {
                id: String(product.id),
                name: String(product.name),
                price: Math.max(0, Number.parseInt(product.price ?? 0, 10)),
                stock,
                qty: 0,
            };
            const nextQuantity = Math.max(0, Number.parseInt(existing.qty ?? 0, 10)) + delta;

            if (stock < 1 || nextQuantity > stock) {
                showCartToast(stock < 1 ? 'Produk sedang habis.' : `Stok tersedia hanya ${stock} porsi.`, true);
                return;
            }

            if (nextQuantity <= 0) {
                delete map[product.id];
            } else {
                map[product.id] = { ...existing, ...product, stock, qty: nextQuantity };
            }

            this.saveMap(map);
            showCartToast(`${existing.name} ditambahkan (${nextQuantity}x)`);
        },

        getSummary() {
            const items = Object.values(this.getMap()).filter((item) => Number.parseInt(item.qty ?? 0, 10) > 0);

            return items.reduce((summary, item) => {
                const quantity = Math.max(0, Number.parseInt(item.qty ?? 0, 10));
                summary.count += quantity;
                summary.total += quantity * Math.max(0, Number.parseInt(item.price ?? 0, 10));
                return summary;
            }, { count: 0, total: 0, items });
        },

        syncUI() {
            const map = this.getMap();
            const { count } = this.getSummary();

            document.querySelectorAll('[data-cart-count]').forEach((element) => {
                element.textContent = String(count);
            });

            document.querySelectorAll('[data-product-btn]').forEach((button) => {
                const quantity = Math.max(0, Number.parseInt(map[button.dataset.productBtn]?.qty ?? 0, 10));
                const label = button.querySelector('[data-product-btn-label]');

                if (label) {
                    label.textContent = quantity > 0 ? `Tambah (${quantity})` : 'Tambah';
                }
            });
        },

        async syncServer() {
            const config = window.AyamoWebCartConfig;

            if (! config?.syncUrl || ! config?.csrfToken) {
                return;
            }

            const body = new URLSearchParams();
            Object.values(this.getMap()).forEach((item) => {
                const quantity = Math.max(0, Number.parseInt(item.qty ?? 0, 10));
                if (quantity > 0) {
                    body.append(`quantities[${item.id}]`, String(quantity));
                }
            });

            await fetch(config.syncUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
            });
        },
    };
}

document.addEventListener('submit', (event) => {
    const addForm = event.target.closest('form[data-local-cart]');

    if (addForm) {
        event.preventDefault();
        window.AyamoCart.add({
            id: addForm.dataset.productId,
            name: addForm.dataset.productName,
            price: Number.parseInt(addForm.dataset.productPrice ?? '0', 10),
            stock: Number.parseInt(addForm.dataset.productStock ?? '0', 10),
        });
        return;
    }

    const updateForm = event.target.closest('form[data-cart-update-form]');
    if (updateForm) {
        const map = window.AyamoCart.getMap();
        updateForm.querySelectorAll('input[name^="quantities["]').forEach((input) => {
            const productId = input.name.slice(11, -1);
            const quantity = Math.max(0, Number.parseInt(input.value || '0', 10));
            if (map[productId] && quantity > 0) {
                map[productId].qty = quantity;
            } else {
                delete map[productId];
            }
        });
        window.AyamoCart.saveMap(map);
    }
});

document.addEventListener('click', async (event) => {
    const link = event.target.closest('a[data-cart-sync-link]');
    if (! link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
        return;
    }

    event.preventDefault();
    link.setAttribute('aria-busy', 'true');

    try {
        await window.AyamoCart.syncServer();
    } finally {
        window.location.href = link.href;
    }
});

window.addEventListener('storage', () => window.AyamoCart.syncUI());
document.addEventListener('DOMContentLoaded', () => {
    window.AyamoCart.syncUI();
    document.dispatchEvent(new CustomEvent('ayamo-cart-ready'));
});
