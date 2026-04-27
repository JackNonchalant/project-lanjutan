@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow">

    <h2 class="text-2xl font-bold mb-6">Profile Saya</h2>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- PROFILE INFO --}}
    <div class="mb-6">
        <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
    </div>

    {{-- FORM UPDATE PROFILE --}}
    <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
        @csrf

        {{-- NAME --}}
        <div>
            <label class="block text-sm mb-1">Nama</label>
            <input type="text" name="name"
                   value="{{ auth()->user()->name }}"
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- EMAIL --}}
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email"
                   value="{{ auth()->user()->email }}"
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- PASSWORD --}}
        <div>
            <label class="block text-sm mb-1">Password Baru (opsional)</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- BUTTON --}}
        <button type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
            Update Profile
        </button>
    </form>

</div>
@endsection