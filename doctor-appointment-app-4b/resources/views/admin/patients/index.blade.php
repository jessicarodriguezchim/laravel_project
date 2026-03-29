<x-admin-layout title="Pacientes | Healthify" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Pacientes'],
]">
    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <x-wire-button href="{{ route('admin.patients.import') }}" blue>
                <i class="fa-solid fa-file-import mr-2"></i>
                Bulk import
            </x-wire-button>
            <form
                action="{{ route('admin.patients.destroy-all') }}"
                method="POST"
                class="delete-form delete-all-patients-form inline"
            >
                @csrf
                @method('DELETE')
                <x-wire-button type="submit" red>
                    <i class="fa-solid fa-trash-can mr-2"></i>
                    Eliminar todos los pacientes
                </x-wire-button>
            </form>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!empty($trackImportProgress))
        <div id="import-progress-wrap" class="mb-4 rounded-lg border border-blue-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Patient import progress') }}</p>
            <div class="h-3 w-full rounded-full bg-gray-200 overflow-hidden">
                <div id="import-progress-bar" class="h-full bg-blue-600 transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="import-progress-text" class="mt-2 text-xs text-gray-600"></p>
            <p id="import-progress-msg" class="mt-1 text-xs font-medium text-gray-800"></p>
            <p class="mt-2 rounded-md bg-amber-50 px-2 py-1.5 text-xs text-amber-900">
                {{ __('Imports run in a background queue. If the counter stays at 0, open a terminal in the app folder and run:') }}
                <code class="rounded bg-white px-1 font-mono text-[11px] text-gray-800">php artisan queue:work</code>.
                {{ __('The jobs table must exist (run migrations if you have not).') }}
            </p>
        </div>
        <script>
            (function () {
                const url = @json(route('admin.patients.import.progress'));
                const wrap = document.getElementById('import-progress-wrap');
                const bar = document.getElementById('import-progress-bar');
                const text = document.getElementById('import-progress-text');
                const msg = document.getElementById('import-progress-msg');
                let timer = setInterval(poll, 2000);
                poll();

                function poll() {
                    fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.status === 'none') {
                                wrap.classList.add('hidden');
                                clearInterval(timer);
                                return;
                            }
                            wrap.classList.remove('hidden');
                            const total = Math.max(1, parseInt(data.total, 10) || 1);
                            const current = parseInt(data.current, 10) || 0;
                            const pct = Math.min(100, Math.round((current / total) * 100));
                            bar.style.width = pct + '%';
                            text.textContent = current + ' / ' + total + ' — ' + (data.imported || 0) + ' {{ __('imported') }}, ' + (data.skipped || 0) + ' {{ __('skipped') }}';
                            if (data.status === 'error') {
                                msg.textContent = data.message || '{{ __('Error') }}';
                                msg.classList.add('text-red-600');
                                clearInterval(timer);
                            } else if (data.status === 'finished') {
                                msg.textContent = '{{ __('Completed.') }}';
                                msg.classList.remove('text-red-600');
                                clearInterval(timer);
                                setTimeout(function () {
                                    window.location.href = @json(route('admin.patients.index')) + '?_=' + Date.now();
                                }, 800);
                            } else {
                                msg.textContent = '{{ __('Processing…') }}';
                                msg.classList.remove('text-red-600');
                            }
                        })
                        .catch(function () { /* ignore transient errors */ });
                }
            })();
        </script>
    @endif

    @livewire('admin.datatables.patient-table')
</x-admin-layout>
