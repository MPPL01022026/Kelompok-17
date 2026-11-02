@php
    $errorMsg = session('error') ?? ($errors->has('cart') ? $errors->first('cart') : ($errors->has('stock') ? $errors->first('stock') : null));
@endphp

@if ($errorMsg)
    <div id="modal-notification" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs">
        <div class="w-full max-w-sm rounded-[20px] border border-[#3D312A] bg-[#FFFDF9] p-6 text-center shadow-2xl">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="font-['Outfit'] text-lg font-extrabold text-[#1E1915]">Perhatian</h3>
            <p class="mt-1.5 text-sm text-[#645943]">{{ $errorMsg }}</p>
            <button type="button" onclick="document.getElementById('modal-notification').remove()"
                class="mt-5 w-full rounded-xl bg-[#B83809] py-2.5 text-sm font-bold text-white transition hover:bg-[#9A2E05]">
                Mengerti
            </button>
        </div>
    </div>
@endif

