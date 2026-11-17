<div id="ayamo-modal-confirmation" role="dialog" aria-modal="true" aria-labelledby="ayamo-confirm-title"
    aria-describedby="ayamo-confirm-message"
    class="fixed inset-0 z-[90] hidden items-center justify-center bg-[#1E1915]/55 p-4 backdrop-blur-xs transition-opacity">
    <div
        class="w-full max-w-md rounded-[28px] border border-[#EBE1D0] bg-[#FFFDF9] p-6 text-center shadow-[0_28px_70px_-20px_rgba(30,25,21,0.35)] sm:p-7">
        <div id="ayamo-confirm-icon-wrap"
            class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FEE2E2] text-[#DC2626]">
            <svg id="ayamo-confirm-icon" aria-hidden="true" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>

        <h3 id="ayamo-confirm-title" class="font-['Outfit'] text-xl font-black text-[#1E1915]">
            Konfirmasi Tindakan
        </h3>
        <p id="ayamo-confirm-message" class="mt-2 text-sm font-medium leading-relaxed text-[#6B5E50]">
            Apakah Anda yakin ingin melanjutkan tindakan ini?
        </p>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" id="ayamo-confirm-cancel-btn"
                class="cursor-pointer rounded-2xl border border-[#DFD2BD] bg-[#FAF4EA] px-4 py-2.5 font-['Outfit'] text-sm font-extrabold text-[#4A3B31] transition hover:border-[#C5B399] hover:bg-white active:scale-[0.98]">
                Batal
            </button>
            <button type="button" id="ayamo-confirm-ok-btn"
                class="cursor-pointer rounded-2xl bg-[#DC2626] px-4 py-2.5 font-['Outfit'] text-sm font-black text-white shadow-xs transition hover:bg-[#B91C1C] active:scale-[0.98]">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

@once
    <script>
        (function () {
            if (window.AyamoConfirm) return;

            const modal = document.getElementById('ayamo-modal-confirmation');
            const titleEl = document.getElementById('ayamo-confirm-title');
            const msgEl = document.getElementById('ayamo-confirm-message');
            const iconWrap = document.getElementById('ayamo-confirm-icon-wrap');
            const okBtn = document.getElementById('ayamo-confirm-ok-btn');
            const cancelBtn = document.getElementById('ayamo-confirm-cancel-btn');

            if (!modal || !titleEl || !msgEl || !iconWrap || !okBtn || !cancelBtn) return;

            const VARIANTS = {
                danger: {
                    iconWrap: 'mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FEE2E2] text-[#DC2626]',
                    okBtn: "cursor-pointer rounded-2xl bg-[#DC2626] px-4 py-2.5 font-['Outfit'] text-sm font-black text-white shadow-xs transition hover:bg-[#B91C1C] active:scale-[0.98]",
                },
                warning: {
                    iconWrap: 'mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FEF3C7] text-[#D97706]',
                    okBtn: "cursor-pointer rounded-2xl bg-[#F59E0B] px-4 py-2.5 font-['Outfit'] text-sm font-black text-[#1E1915] shadow-xs transition hover:bg-[#D97706] active:scale-[0.98]",
                },
                primary: {
                    iconWrap: 'mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFEDD5] text-[#B83809]',
                    okBtn: "cursor-pointer rounded-2xl bg-[#B83809] px-4 py-2.5 font-['Outfit'] text-sm font-black text-white shadow-xs transition hover:bg-[#992D06] active:scale-[0.98]",
                },
            };

            let activeResolver = null;
            let lastFocused = null;

            const closeModal = (confirmed) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                const resolve = activeResolver;
                activeResolver = null;
                if (lastFocused && typeof lastFocused.focus === 'function') {
                    lastFocused.focus();
                }
                lastFocused = null;
                if (resolve) resolve(Boolean(confirmed));
            };

            okBtn.addEventListener('click', () => closeModal(true));
            cancelBtn.addEventListener('click', () => closeModal(false));
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal(false);
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    event.preventDefault();
                    closeModal(false);
                }
            });

            window.AyamoConfirm = function (options) {
                const cfg = typeof options === 'string' ? { message: options } : (options || {});
                const variant = VARIANTS[cfg.variant] || VARIANTS.danger;

                if (activeResolver) {
                    closeModal(false);
                }

                lastFocused = document.activeElement;
                titleEl.textContent = cfg.title || 'Konfirmasi Tindakan';
                msgEl.textContent = cfg.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                okBtn.textContent = cfg.confirmText || 'Ya, Lanjutkan';
                cancelBtn.textContent = cfg.cancelText || 'Batal';
                iconWrap.className = variant.iconWrap;
                okBtn.className = variant.okBtn;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                cancelBtn.focus();

                return new Promise((resolve) => {
                    activeResolver = resolve;
                });
            };

            // Intercept <form data-confirm="..."> submissions
            document.addEventListener('submit', async (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;
                const message = form.dataset.confirm;
                if (!message || form.dataset.confirmBypass === '1') {
                    if (form.dataset.confirmBypass === '1') {
                        delete form.dataset.confirmBypass;
                    }
                    return;
                }

                event.preventDefault();
                const confirmed = await window.AyamoConfirm({
                    title: form.dataset.confirmTitle || 'Konfirmasi Tindakan',
                    message,
                    confirmText: form.dataset.confirmOk || 'Ya, Lanjutkan',
                    cancelText: form.dataset.confirmCancel || 'Batal',
                    variant: form.dataset.confirmVariant || 'danger',
                });

                if (confirmed) {
                    form.dataset.confirmBypass = '1';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                }
            });

            // Intercept <button data-confirm="..."> or <a data-confirm="..."> clicks
            document.addEventListener('click', async (event) => {
                const trigger = event.target.closest('[data-confirm]');
                if (!trigger || trigger instanceof HTMLFormElement) return;
                if (trigger.dataset.confirmBypass === '1') {
                    delete trigger.dataset.confirmBypass;
                    return;
                }

                event.preventDefault();
                const confirmed = await window.AyamoConfirm({
                    title: trigger.dataset.confirmTitle || 'Konfirmasi Tindakan',
                    message: trigger.dataset.confirm,
                    confirmText: trigger.dataset.confirmOk || 'Ya, Lanjutkan',
                    cancelText: trigger.dataset.confirmCancel || 'Batal',
                    variant: trigger.dataset.confirmVariant || 'danger',
                });

                if (!confirmed) return;

                if (trigger instanceof HTMLAnchorElement && trigger.href) {
                    window.location.href = trigger.href;
                    return;
                }

                trigger.dataset.confirmBypass = '1';
                trigger.click();
            });
        })();
    </script>
@endonce
