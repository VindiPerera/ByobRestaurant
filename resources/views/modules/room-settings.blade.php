@extends('layouts.app')

@section('title', 'Room Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-sliders-h text-purple-600"></i>Room Settings
            </h1>
            <p class="text-gray-600 mt-2">Configure the default booking duration and each room's base price</p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-300 text-green-800 rounded-lg px-5 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-300 text-red-800 rounded-lg px-5 py-3">
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('rooms.settings.update') }}" method="POST">
        @csrf

        <!-- Booking Duration -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-hourglass-half text-purple-600"></i>Default Booking Duration
            </h2>
            <p class="text-sm text-gray-600 mb-4">
                When a room is booked, the countdown starts from this duration. Additional time is added per-extension at checkout.
            </p>
            <div class="flex items-center gap-3">
                <input type="number" name="booking_duration_minutes" min="1" required
                       value="{{ old('booking_duration_minutes', $bookingDurationMinutes) }}"
                       class="w-40 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <span class="text-gray-700 font-medium">minutes
                    <span class="text-gray-400 text-sm">(120 = 2 hours)</span>
                </span>
            </div>
        </div>

        <!-- Per-Room Base Prices -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tags text-purple-600"></i>Room Base Prices
            </h2>
            <p class="text-sm text-gray-600 mb-4">Price charged for the base booking block of each room.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rooms as $room)
                    <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 rounded-lg">
                        <label class="text-sm font-semibold text-gray-800 whitespace-nowrap">
                            <i class="fas fa-door-open text-purple-500 mr-1"></i>Room {{ $room->room_number }}
                        </label>
                        <div class="flex items-center gap-1">
                            <span class="text-gray-500 text-sm">Rs.</span>
                            <input type="number" step="0.01" min="0"
                                   name="base_prices[{{ $room->id }}]"
                                   value="{{ old('base_prices.'.$room->id, $room->base_price) }}"
                                   class="w-24 px-2 py-1.5 border border-gray-300 rounded-lg text-right focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
