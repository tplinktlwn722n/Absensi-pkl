<x-guest-layout>
  <x-authentication-card>
    <x-slot name="logo">
      <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <div>
        <x-label for="name" value="Nama" />
        <x-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus
          autocomplete="name" />
      </div>

      <div class="mt-4">
        <x-label for="email" value="Email" />
        <x-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required
          autocomplete="username" />
      </div>

      <div class="mt-4">
        <x-label for="phone" value="Nomor Telepon" />
        <x-input id="phone" class="mt-1 block w-full" type="number" name="phone" :value="old('phone')" required
          autocomplete="username" />
      </div>

      <div class="mt-4">
        <x-label for="gender" value="Jenis Kelamin" />
        <x-select id="gender" class="mt-1 block w-full" name="gender" required>
          <option disabled selected>Pilih Jenis Kelamin</option>
          <option value="male">
            Laki-laki
          </option>
          <option value="female">
            Perempuan
          </option>
        </x-select>
      </div>

      <div class="mt-4">
        <x-label for="address" value="Alamat" />
        <x-textarea id="address" class="mt-1 block w-full" name="address" :value="old('address')" required />
      </div>

      <div class="mt-4">
        <x-label for="city" value="Kota" />
        <x-input id="city" class="mt-1 block w-full" type="text" name="city" :value="old('city')" required
          autocomplete="city" />
      </div>

      <div class="mt-4">
        <x-label for="major_id" value="Jurusan" />
        <x-select id="major_id" class="mt-1 block w-full" name="major_id" required>
          <option disabled selected>Pilih Jurusan</option>
          @foreach (App\Models\Division::all() as $major)
            <option value="{{ $major->id }}">{{ $major->name }}</option>
          @endforeach
        </x-select>
      </div>

      <div class="mt-4">
        <x-label for="school" value="Asal Sekolah" />
        <x-input id="school" class="mt-1 block w-full" type="text" name="school" :value="old('school')" required
          list="schools" placeholder="Ketik nama sekolah" />
        <datalist id="schools">
          @foreach (App\Models\Education::all() as $school)
            <option value="{{ $school->name }}">
          @endforeach
        </datalist>
      </div>

      <div class="mt-4">
        <x-label for="password" value="Kata Sandi" />
        <x-input id="password" class="mt-1 block w-full" type="password" name="password" required
          autocomplete="new-password" />
      </div>

      <div class="mt-4">
        <x-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
        <x-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation"
          required autocomplete="new-password" />
      </div>

      @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
        <div class="mt-4">
          <x-label for="terms">
            <div class="flex items-center">
              <x-checkbox name="terms" id="terms" required />

              <div class="ms-2">
                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                    'terms_of_service' =>
                        '<a target="_blank" href="' .
                        route('terms.show') .
                        '" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800">' .
                        __('Terms of Service') .
                        '</a>',
                    'privacy_policy' =>
                        '<a target="_blank" href="' .
                        route('policy.show') .
                        '" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800">' .
                        __('Privacy Policy') .
                        '</a>',
                ]) !!}
              </div>
            </div>
          </x-label>
        </div>
      @endif

      <div class="mt-4 flex items-center justify-end">
        <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
          href="{{ route('login') }}">
          Sudah terdaftar?
        </a>

        <x-button class="ms-4">
          Daftar
        </x-button>
      </div>
    </form>
  </x-authentication-card>
</x-guest-layout>
