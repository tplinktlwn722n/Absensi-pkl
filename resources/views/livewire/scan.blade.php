<div class="w-full">
  @php
    use Illuminate\Support\Carbon;
    $now = Carbon::now();
    $currentHour = $now->hour;
    $currentMinute = $now->minute;
    $canCheckIn = ($currentHour == 6 || $currentHour == 7 || ($currentHour == 8 && $currentMinute == 0));
    $canCheckOut = ($currentHour >= 16);
    $canAttend = $canCheckIn || $canCheckOut;
  @endphp
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
      .camera-container { position: relative; width: 100%; max-width: 500px; margin: 0 auto; }
      #camera-preview { width: 100%; border-radius: 8px; }
      .camera-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 200px; height: 200px; border: 3px dashed rgba(255, 255, 255, 0.8); border-radius: 50%; }
      .fade-out { animation: fadeOut 0.5s ease-out forwards; }
      @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
      @keyframes bounce { 0%, 100% { transform: translateY(0) translateX(-50%); } 50% { transform: translateY(-10px) translateX(-50%); } }
      .animate-bounce { animation: bounce 1s infinite; }
    </style>
  @endpushOnce
  @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  @endpushOnce

  <div class="flex flex-col gap-4">
    <!-- Realtime Clock Box -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 text-center">
      <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200" id="realtime-clock">--:--</h2>
    </div>

    <!-- Main Container -->
    <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
      <h4 id="message-error" class="mb-3 text-lg font-semibold text-red-500 dark:text-red-400 sm:text-xl"></h4>
      <h4 id="message-success" class="mb-3 hidden text-lg font-semibold text-green-500 dark:text-green-400 sm:text-xl">{{ __('Attendance Successful') }}</h4>
      
      <div class="mb-4">
        <p class="text-lg font-semibold text-gray-600 dark:text-gray-100">
          {{ __('Date') . ': ' . now()->format('d/m/Y') }}
        </p>
        @if (!is_null($currentLiveCoords))
          <div class="flex justify-between items-center mt-2">
            <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}" target="_blank" class="text-blue-500 hover:text-blue-600 underline text-sm">
              {{ __('Your location') . ': ' . $currentLiveCoords[0] . ', ' . $currentLiveCoords[1] }}
            </a>
            <button class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200" onclick="toggleCurrentMap()" id="toggleCurrentMap">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-200" id="toggleMapIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
        @else
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ __('Your location') . ': -, -' }}</p>
        @endif
        <div class="my-4 h-72 w-full md:h-96 rounded-lg overflow-hidden" id="currentMap" style="display: none;" wire:ignore></div>
      </div>

      <div class="grid grid-cols-2 gap-3 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2">
        <div class="{{ $attendance?->status == 'late' ? 'bg-red-200 dark:bg-red-900' : 'bg-blue-200 dark:bg-blue-900' }} flex items-center justify-between rounded-md px-4 py-2 text-gray-800 dark:text-white">
          <div>
            <h4 class="text-lg font-semibold md:text-xl">Status Absen</h4>
            <div class="flex flex-col sm:flex-row">
              <span>@if ($attendance) {{ __($attendance?->status) ?? '-' }} @else Belum Absen @endif</span>
              @if ($attendance?->status == 'late')
                <span class="mx-1 hidden sm:inline-block">|</span>
                <span>Terlambat: Ya</span>
              @endif
            </div>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>

      <hr class="my-4">

      <div class="mb-4 text-center">
        @if (!$attendance)
          <button wire:click="showAttendanceModal" @if(!$canAttend) disabled @endif
                  class="px-8 py-4 text-lg font-semibold rounded-lg transition-colors duration-200 {{ $canAttend ? 'bg-blue-500 hover:bg-blue-600 text-white cursor-pointer' : 'bg-gray-400 text-gray-200 cursor-not-allowed' }}">
            {{ $canCheckIn ? 'Absen Masuk' : ($canCheckOut ? 'Absen Pulang' : 'Diluar Jam Absen') }}
          </button>
          @if (!$canAttend)
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Absen masuk: 06:00 - 08:00 | Absen pulang: 16:00 ke atas</p>
          @endif
        @else
          <p class="text-lg text-gray-600 dark:text-gray-300">Anda sudah absen hari ini</p>
        @endif
      </div>

      <div class="grid grid-cols-2 gap-3 md:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('apply-leave') }}">
          <div class="flex flex-col-reverse items-center justify-center gap-2 rounded-md bg-amber-500 px-4 py-2 text-center font-medium text-white shadow-md shadow-gray-400 transition duration-100 hover:bg-amber-600 dark:shadow-gray-700 md:flex-row md:gap-3">
            Ajukan Izin
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
        </a>
        <a href="{{ route('attendance-history') }}">
          <div class="flex flex-col-reverse items-center justify-center gap-2 rounded-md bg-blue-500 px-4 py-2 text-center font-medium text-white shadow-md shadow-gray-400 hover:bg-blue-600 dark:shadow-gray-700 md:flex-row md:gap-3">
            Riwayat Absen
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Absen dengan Kamera -->
  <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 text-center">Konfirmasi Absensi</h3>
          <div class="camera-container mb-4">
            <video id="camera-preview" autoplay playsinline></video>
            <div class="camera-overlay"></div>
          </div>
          <canvas id="photo-canvas" style="display: none;"></canvas>
          <p id="camera-error" class="text-red-500 text-sm text-center mb-4 hidden"></p>
          <div class="flex gap-3 justify-center">
            <button type="button" wire:click="capturePhoto" id="capture-button" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Ambil Foto & Absen</button>
            <button type="button" wire:click="closeModal" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@script
<script>
let currentMap = document.getElementById('currentMap');
let map = null;
let cameraStream = null;
let clockInterval = null;

function updateClock() {
  try {
    const clockElement = document.getElementById('realtime-clock');
    if (clockElement) {
      const now = new Date();
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const newTime = `${hours}:${minutes}`;
      
      if (clockElement.textContent !== newTime) {
        clockElement.textContent = newTime;
      }
    }
  } catch (error) {
    console.error('Clock update error:', error);
  }
}

updateClock();
if (clockInterval) clearInterval(clockInterval);
clockInterval = setInterval(updateClock, 1000);

function toggleCurrentMap() {
  const mapElement = document.getElementById('currentMap');
  const iconElement = document.getElementById('toggleMapIcon');
  
  if (mapElement) {
    const isHidden = mapElement.style.display === 'none';
    mapElement.style.display = isHidden ? 'block' : 'none';
    
    if (iconElement) {
      iconElement.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    }
    
    if (isHidden && map) {
      setTimeout(() => {
        map.invalidateSize();
      }, 100);
    }
  }
}

async function getLocation() {
  if (navigator.geolocation) {
    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        });
      });
      
      if (!map) {
        map = L.map('currentMap');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 21 }).addTo(map);
      }
      
      // Set initial position
      $wire.$set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
      map.setView([position.coords.latitude, position.coords.longitude], 13);
      L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
      
      // Watch position for updates
      navigator.geolocation.watchPosition((position) => {
        $wire.$set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
        map.setView([Number(position.coords.latitude), Number(position.coords.longitude)], 13);
      }, (err) => {
        console.error(`ERROR(${err.code}): ${err.message}`);
      });
    } catch (err) {
      console.error(`ERROR(${err.code}): ${err.message}`);
      showPermissionModal();
    }
  } else {
    document.querySelector('#message-error').innerHTML = "Browser tidak mendukung geolokasi";
  }
}

function showPermissionModal() {
  const existingModal = document.getElementById('permission-modal');
  if (existingModal) {
    existingModal.remove();
  }
  
  const modal = document.createElement('div');
  modal.id = 'permission-modal';
  modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4';
  modal.innerHTML = `
    <div class="bg-gray-900 border-2 border-yellow-500 rounded-lg p-6 max-w-md w-full text-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      
      <h3 class="text-xl font-bold text-white mb-4">PERINGATAN</h3>
      
      <p class="text-white text-sm mb-6">
        * Anda harus mengaktifkan akses <strong>Lokasi</strong> dan <strong>Kamera</strong> untuk dapat menggunakan sistem absensi.
      </p>
      
      <p class="text-gray-400 text-xs mb-6">
        Silakan klik icon gembok di address bar browser, lalu izinkan akses Location dan Camera. Setelah itu muat ulang halaman ini.
      </p>
      
      <button onclick="location.reload()" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-6 rounded transition-colors">
        Muat Ulang Halaman
      </button>
    </div>
  `;
  document.body.appendChild(modal);
}

async function startCamera() {
  try {
    const video = document.getElementById('camera-preview');
    cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
    video.srcObject = cameraStream;
  } catch (error) {
    console.error('Error accessing camera:', error);
    document.getElementById('camera-error').textContent = 'Gagal mengakses kamera. Pastikan Anda mengizinkan akses kamera.';
    document.getElementById('camera-error').classList.remove('hidden');
  }
}

function stopCamera() {
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;
  }
}

Livewire.on('modalOpened', () => { setTimeout(() => { startCamera(); }, 300); });
Livewire.on('modalClosed', () => { stopCamera(); });

document.addEventListener('click', function(e) {
  if (e.target && e.target.id === 'capture-button') {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('photo-canvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    canvas.toBlob((blob) => {
      const reader = new FileReader();
      reader.onloadend = () => {
        $wire.photoData = reader.result;
        $wire.submitAttendance();
      };
      reader.readAsDataURL(blob);
    }, 'image/jpeg', 0.8);
  }
});

getLocation();

// Initialize on page load - show modal if permission not granted
async function initializePage() {
  try {
    console.log('Checking location permission...');
    await getLocation();
    console.log('Location permission granted');
  } catch (error) {
    console.log('Location permission needed, showing instruction modal');
    showPermissionModal();
  }
}

initializePage();
</script>
@endscript
