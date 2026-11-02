<div class="js-cart-bar-wrapper {{ ($cartCount ?? 0) > 0 ? '' : 'hidden' }}">
    <a href="{{ route('mobile.cart') }}"
        class="animate-pop flex items-center justify-between rounded-[18px] bg-[#DC2626] px-5 py-3.5 text-white shadow-[0_8px_20px_rgba(220,38,38,0.32)] transition hover:bg-[#B91C1C] active:scale-[0.99]">
        <div class="flex items-center gap-2 text-sm font-extrabold">
            <span class="js-cart-count">{{ $cartCount ?? 0 }} Item</span>
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-white"></span>
            <span class="js-cart-total">Rp. {{ number_format($cartTotal ?? 0, 0, ',', '.') }}</span>
        </div>
        <span class="text-sm font-extrabold tracking-tight">Lihat Keranjang &gt;</span>
    </a>
</div>
