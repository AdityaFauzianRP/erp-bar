{{-- resources/views/filament/partials/clock.blade.php --}}
<div x-data="clock()" x-init="init()" class="hidden lg:flex items-center group tracking-tight">
    <div class="flex items-center gap-3">

        <div class="flex items-baseline gap-2">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" x-text="dateString"></span>
            
            <span class="text-gray-300 dark:text-gray-600">•</span>
            
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100 font-mono" x-text="timeString"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.data('clock')) {
            Alpine.data('clock', () => ({
                timeString: '',
                dateString: '',
                _timer: null,
                init() {
                    this.tick();
                    this._timer = setInterval(() => this.tick(), 1000);
                },
                tick() {
                    const now = new Date();
                    
                    // Jam: 12:05:01
                    this.timeString = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                    });

                    // Tanggal: Senin, 26 Jan 2026
                    const dateOpts = { weekday: 'short', day: '2-digit', month: 'short' };
                    this.dateString = now.toLocaleDateString('id-ID', dateOpts);
                },
                destroy() {
                    if (this._timer) clearInterval(this._timer);
                }
            }));
        }
    });
</script>
@endpush