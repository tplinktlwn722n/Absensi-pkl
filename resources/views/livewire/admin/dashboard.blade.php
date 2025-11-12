@php
  $date = Carbon\Carbon::now();
@endphp
<div>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <div class="flex flex-col justify-between sm:flex-row">
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
      Absensi Hari Ini
    </h3>
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
      Jumlah Siswa: {{ $employeesCount }}
    </h3>
  </div>
  <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
    <div class="rounded-md bg-green-200 px-8 py-4 text-gray-800 dark:bg-green-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Hadir: {{ $presentCount }}</span><br>
      <span>Terlambat: {{ $lateCount }}</span>
    </div>
    <div class="rounded-md bg-blue-200 px-8 py-4 text-gray-800 dark:bg-blue-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Izin: {{ $excusedCount }}</span><br>
      <span>Izin/Cuti</span>
    </div>
    <div
      class="rounded-md bg-yellow-200 px-8 py-4 text-gray-800 dark:bg-yellow-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Sakit: {{ $sickCount }}</span>
    </div>
    <div class="rounded-md bg-red-200 px-8 py-4 text-gray-800 dark:bg-red-900 dark:text-white dark:shadow-gray-700">
      <span class="text-2xl font-semibold md:text-3xl">Tidak Hadir: {{ $absentCount }}</span><br>
      <span>Tidak/Belum Hadir</span>
    </div>
  </div>

  <div class="mb-4 overflow-x-scroll">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Name') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('Major') }}
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ __('School') }}
          </th>
          <th scope="col"
            class="text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium text-gray-500 dark:border-gray-600 dark:text-gray-300">
            Status Absen
          </th>
          <th scope="col"
            class="text-nowrap px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300">
            Status Aktif
          </th>
          <th scope="col" class="relative">
            <span class="sr-only">Actions</span>
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @php
          $class = 'px-4 py-3 text-sm font-medium text-gray-900 dark:text-white';
        @endphp
        @foreach ($employees as $employee)
          @php
            $attendance = $employee->attendance;
            $isWeekend = $date->isWeekend();
            $status = ($attendance ?? [
                'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
            ])['status'];
            switch ($status) {
                case 'present':
                    $shortStatus = 'H';
                    $bgColor =
                        'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-300 dark:border-green-600';
                    break;
                case 'late':
                    $shortStatus = 'T';
                    $bgColor =
                        'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-300 dark:border-amber-600';
                    break;
                case 'excused':
                    $shortStatus = 'I';
                    $bgColor =
                        'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-300 dark:border-blue-600';
                    break;
                case 'sick':
                    $shortStatus = 'S';
                    $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                    break;
                case 'absent':
                    $shortStatus = 'A';
                    $bgColor =
                        'bg-red-200 dark:bg-red-800 hover:bg-red-300 dark:hover:bg-red-700 border border-red-300 dark:border-red-600';
                    break;
                default:
                    $shortStatus = '-';
                    $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                    break;
            }
          @endphp
          <tr wire:key="{{ $employee->id }}" class="group">
            {{-- Detail siswa --}}
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->name }}
            </td>
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->major?->name ?? '-' }}
            </td>
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->school?->name ?? '-' }}
            </td>

            {{-- Status Absensi --}}
            <td
              class="{{ $bgColor }} text-nowrap px-1 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
              {{ __($status) }}
            </td>

            {{-- Status Aktif (Online/Offline) --}}
            <td class="{{ $class }} text-center group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $employee->isOnline() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                {{ $employee->isOnline() ? 'Aktif' : 'Tidak Aktif' }}
              </span>
            </td>

            {{-- Action --}}
            <td
              class="cursor-pointer text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:text-white dark:group-hover:bg-gray-700">
              <div class="flex items-center justify-center gap-3">
                <x-button type="button" wire:click="showUserDetail({{ $employee->id }})">
                  {{ __('Detail') }}
                </x-button>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $employees->links() }}

  <!-- User Detail Modal -->
  <div x-data="{ show: @entangle('showUserDetailModal') }" 
       x-show="show" 
       x-cloak
       class="fixed inset-0 z-50 overflow-y-auto" 
       style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div x-show="show" 
           x-transition:enter="ease-out duration-300"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="ease-in duration-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
           @click="$wire.closeUserDetailModal()"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      <div x-show="show"
           x-transition:enter="ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
           x-transition:leave="ease-in duration-200"
           x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
           x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
           class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
        
        @if($selectedUser)
        <div class="mb-4">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
              Detail Siswa
            </h3>
            <button @click="$wire.closeUserDetailModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- User Info -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Nama</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->email }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Asal Sekolah</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->school?->name ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Jurusan</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->major?->name ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">No. Telepon</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->phone ?? '-' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Alamat</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->address ?? '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Attendance History with Photos -->
          <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Riwayat Absensi (Dengan Foto)
            </h4>
            
            @if(count($userAttendances) > 0)
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                @foreach($userAttendances as $attendance)
                  <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-3 border border-gray-200 dark:border-gray-600">
                    @if($attendance->photo)
                      <img src="{{ asset('storage/' . $attendance->photo) }}" 
                           alt="Foto Absensi" 
                           class="w-full h-40 object-cover rounded-lg mb-3">
                    @endif
                    <div class="space-y-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($attendance->date)->format('d F Y') }}
                      </p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">
                        Jam: {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                      </p>
                      <p class="text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                          {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                             ($attendance->status === 'late' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 
                             'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                          {{ __($attendance->status) }}
                        </span>
                      </p>
                      @if($attendance->latitude && $attendance->longitude)
                        <a href="{{ \App\Helpers::getGoogleMapsUrl($attendance->latitude, $attendance->longitude) }}" 
                           target="_blank"
                           class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                          Lihat Lokasi
                        </a>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                Belum ada riwayat absensi dengan foto
              </p>
            @endif
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
