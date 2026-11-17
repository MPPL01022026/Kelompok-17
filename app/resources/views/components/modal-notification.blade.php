@props(['position' => 'responsive'])

@php
    $errorMsg = session('error')
        ?? ($errors->has('checkout_token') ? $errors->first('checkout_token')
        : ($errors->has('login') ? $errors->first('login')
        : ($errors->has('cart') ? $errors->first('cart')
        : ($errors->has('stock') ? $errors->first('stock') : null))));

    $flashNotice = null;
    $flashType = 'success';

    if (session('success')) {
        $flashNotice = session('success');
        $flashType = 'success';
    } elseif (session('status')) {
        $flashNotice = session('status');
        $flashType = 'success';
    } elseif (session('warning')) {
        $flashNotice = session('warning');
        $flashType = 'warning';
    } elseif (session('info')) {
        $flashNotice = session('info');
        $flashType = 'info';
    }

    $toastPosClass = $position === 'mobile'
        ? 'pointer-events-none fixed top-4 left-1/2 z-[80] flex w-full max-w-[390px] -translate-x-1/2 flex-col items-center gap-2 px-4'
        : 'pointer-events-none fixed top-5 left-1/2 z-[80] flex w-full max-w-sm -translate-x-1/2 flex-col items-center gap-2 px-4 sm:left-auto sm:right-5 sm:translate-x-0 sm:items-end sm:px-0';
@endphp

{{-- Blocking Error / Alert Dialog --}}
@if ($errorMsg)
    <div id="modal-notification" role="alertdialog" aria-modal="true" aria-labelledby="modal-notification-title"
        class="fixed inset-0 z-[95] flex items-center justify-center bg-[#1E1915]/50 p-4 backdrop-blur-xs">
        <div class="w-full max-w-sm rounded-[26px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 text-center shadow-2xl">
            <div class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FEE2E2] text-[#DC2626]">
                <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <h3 id="modal-notification-title" class="font-['Outfit'] text-lg font-black text-[#1E1915]">Perhatian</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-[#6B5E50]">{{ $errorMsg }}</p>
            <button type="button" onclick="document.getElementById('modal-notification')?.remove()"
                class="mt-5 w-full cursor-pointer rounded-2xl bg-[#B83809] py-2.5 font-['Outfit'] text-sm font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.98]">
                Mengerti
            </button>
        </div>
    </div>
@endif

{{-- Reusable Dynamic Alert Modal Container (Replaces native window.alert) --}}
<div id="ayamo-dynamic-alert" role="alertdialog" aria-modal="true" aria-labelledby="ayamo-alert-title" aria-describedby="ayamo-alert-message"
    class="fixed inset-0 z-[95] hidden items-center justify-center bg-[#1E1915]/50 p-4 backdrop-blur-xs">
    <div class="w-full max-w-sm rounded-[26px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 text-center shadow-2xl">
        <div id="ayamo-alert-icon-wrap" class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FEE2E2] text-[#DC2626]">
            <svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h3 id="ayamo-alert-title" class="font-['Outfit'] text-lg font-black text-[#1E1915]">Informasi</h3>
        <p id="ayamo-alert-message" class="mt-1.5 text-sm leading-relaxed text-[#6B5E50]"></p>
        <button type="button" id="ayamo-alert-close-btn"
            class="mt-5 w-full cursor-pointer rounded-2xl bg-[#B83809] py-2.5 font-['Outfit'] text-sm font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.98]">
            Mengerti
        </button>
    </div>
</div>

{{-- Reusable Toast Container --}}
<div id="ayamo-toast-container" aria-live="polite" aria-atomic="true" class="{{ $toastPosClass }}">
    @if ($flashNotice)
        @php
            $ssrToast = match ($flashType) {
                'error' => [
                    'card' => 'border-[#FCA5A5] bg-[#FEF2F2]/95 text-[#991B1B]',
                    'badge' => 'bg-[#DC2626] text-white',
                    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />',
                    'btn' => 'text-[#991B1B]/70 hover:bg-[#FEE2E2] hover:text-[#991B1B]',
                ],
                'warning' => [
                    'card' => 'border-[#FDE68A] bg-[#FFFBEB]/95 text-[#92400E]',
                    'badge' => 'bg-[#F59E0B] text-[#1E1915]',
                    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />',
                    'btn' => 'text-[#92400E]/70 hover:bg-[#FEF3C7] hover:text-[#92400E]',
                ],
                'info' => [
                    'card' => 'border-[#BAE6FD] bg-[#F0F9FF]/95 text-[#075985]',
                    'badge' => 'bg-[#0284C7] text-white',
                    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />',
                    'btn' => 'text-[#075985]/70 hover:bg-[#E0F2FE] hover:text-[#075985]',
                ],
                default => [
                    'card' => 'border-[#86EFAC] bg-[#F0FDF4]/95 text-[#166534]',
                    'badge' => 'bg-[#22C55E] text-white',
                    'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />',
                    'btn' => 'text-[#15803D]/70 hover:bg-[#DCFCE7] hover:text-[#166534]',
                ],
            };
        @endphp
        <div id="ayamo-ssr-flash" data-ayamo-toast="1" role="status" class="pointer-events-auto flex w-full items-center gap-3 rounded-2xl border px-4 py-3 text-xs font-extrabold shadow-xl backdrop-blur-md transition duration-200 {{ $ssrToast['card'] }}">
            <span aria-hidden="true" class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl {{ $ssrToast['badge'] }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">{!! $ssrToast['svg'] !!}</svg>
            </span>
            <span class="flex-1 leading-snug">{{ $flashNotice }}</span>
            <button type="button" aria-label="Tutup notifikasi" onclick="document.getElementById('ayamo-ssr-flash')?.remove()" class="cursor-pointer rounded-lg p-1 transition {{ $ssrToast['btn'] }}">
                <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif
</div>

@once
<script>
(function () {
    const MAX_NOTIFICATIONS = 5;
    const CLOSE_SVG = '<svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
    const TOAST_STYLES = {
        success: {
            card: 'pointer-events-auto flex w-full items-center gap-3 rounded-2xl border border-[#86EFAC] bg-[#F0FDF4]/95 px-4 py-3 text-xs font-extrabold text-[#166534] shadow-xl backdrop-blur-md transition duration-200',
            badge: 'flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-[#22C55E] text-white',
            iconSvg: '<svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>',
            closeBtn: 'cursor-pointer rounded-lg p-1 text-[#15803D]/70 transition hover:bg-[#DCFCE7] hover:text-[#166534]',
        },
        error: {
            card: 'pointer-events-auto flex w-full items-center gap-3 rounded-2xl border border-[#FCA5A5] bg-[#FEF2F2]/95 px-4 py-3 text-xs font-extrabold text-[#991B1B] shadow-xl backdrop-blur-md transition duration-200',
            badge: 'flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-[#DC2626] text-white',
            iconSvg: '<svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>',
            closeBtn: 'cursor-pointer rounded-lg p-1 text-[#991B1B]/70 transition hover:bg-[#FEE2E2] hover:text-[#991B1B]',
        },
        warning: {
            card: 'pointer-events-auto flex w-full items-center gap-3 rounded-2xl border border-[#FDE68A] bg-[#FFFBEB]/95 px-4 py-3 text-xs font-extrabold text-[#92400E] shadow-xl backdrop-blur-md transition duration-200',
            badge: 'flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-[#F59E0B] text-[#1E1915]',
            iconSvg: '<svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>',
            closeBtn: 'cursor-pointer rounded-lg p-1 text-[#92400E]/70 transition hover:bg-[#FEF3C7] hover:text-[#92400E]',
        },
        info: {
            card: 'pointer-events-auto flex w-full items-center gap-3 rounded-2xl border border-[#BAE6FD] bg-[#F0F9FF]/95 px-4 py-3 text-xs font-extrabold text-[#075985] shadow-xl backdrop-blur-md transition duration-200',
            badge: 'flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-[#0284C7] text-white',
            iconSvg: '<svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>',
            closeBtn: 'cursor-pointer rounded-lg p-1 text-[#075985]/70 transition hover:bg-[#E0F2FE] hover:text-[#075985]',
        },
    };

    window.AyamoNotify = function (message, type = 'success', duration = 3600) {
        const container = document.getElementById('ayamo-toast-container');
        if (!container || !message) return;

        const style = TOAST_STYLES[type] || TOAST_STYLES.success;
        const activeToasts = container.querySelectorAll('[data-ayamo-toast="1"]');
        const excess = activeToasts.length - (MAX_NOTIFICATIONS - 1);
        for (let i = 0; i < excess; i++) {
            activeToasts[i].remove();
        }

        const toast = document.createElement('div');
        toast.dataset.ayamoToast = '1';
        toast.setAttribute('role', 'status');
        toast.className = style.card;

        const badge = document.createElement('span');
        badge.className = style.badge;
        badge.setAttribute('aria-hidden', 'true');
        badge.innerHTML = style.iconSvg;

        const text = document.createElement('span');
        text.className = 'flex-1 leading-snug';
        text.textContent = String(message);

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
        closeBtn.className = style.closeBtn;
        closeBtn.innerHTML = CLOSE_SVG;

        const removeToast = () => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 220);
        };
        closeBtn.addEventListener('click', removeToast);

        toast.append(badge, text, closeBtn);
        container.appendChild(toast);

        if (duration > 0) {
            setTimeout(removeToast, duration);
        }
    };

    const alertModal = document.getElementById('ayamo-dynamic-alert');
    const alertTitle = document.getElementById('ayamo-alert-title');
    const alertMsg = document.getElementById('ayamo-alert-message');
    const alertIconWrap = document.getElementById('ayamo-alert-icon-wrap');
    const alertClose = document.getElementById('ayamo-alert-close-btn');
    let alertResolver = null;

    if (alertModal && alertClose) {
        const closeAlert = () => {
            alertModal.classList.add('hidden');
            alertModal.classList.remove('flex');
            const resolve = alertResolver;
            alertResolver = null;
            if (resolve) resolve();
        };
        alertClose.addEventListener('click', closeAlert);
        alertModal.addEventListener('click', (e) => {
            if (e.target === alertModal) closeAlert();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !alertModal.classList.contains('hidden')) {
                e.preventDefault();
                closeAlert();
            }
        });
    }

    window.AyamoAlert = function (options) {
        const cfg = typeof options === 'string' ? { message: options } : (options || {});
        if (!alertModal || !alertTitle || !alertMsg || !alertIconWrap) return Promise.resolve();

        const isError = (cfg.variant || 'error') === 'error';
        alertIconWrap.className = isError
            ? 'mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FEE2E2] text-[#DC2626]'
            : 'mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FEF3C7] text-[#D97706]';

        alertTitle.textContent = cfg.title || (isError ? 'Perhatian' : 'Informasi');
        alertMsg.textContent = cfg.message || '';
        alertClose.textContent = cfg.buttonText || 'Mengerti';
        alertModal.classList.remove('hidden');
        alertModal.classList.add('flex');
        alertClose.focus();

        return new Promise((resolve) => {
            alertResolver = resolve;
        });
    };
})();
</script>
@endonce

@if ($flashNotice)
<script>
setTimeout(function () {
    const el = document.getElementById('ayamo-ssr-flash');
    if (el) {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 220);
    }
}, 4200);
</script>
@endif
