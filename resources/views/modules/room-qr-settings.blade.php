@extends('layouts.app')

@section('title', 'Room QR Code Settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-qrcode text-purple-600"></i>Room QR Codes
                </h1>
                <p class="text-gray-600 mt-2">Generate and download QR codes for each room</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-4 mb-8">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-purple-600 mt-1"></i>
            <div>
                <h3 class="font-semibold text-purple-900">How to Use</h3>
                <p class="text-purple-800 text-sm mt-1">
                    Download each room's QR code, print and place it inside the room. Guests scan it to browse the menu and order —
                    items are added to that room's bill (not the POS). Ordering only works while the room has an active booking.
                </p>
            </div>
        </div>
    </div>

    <!-- Room QR Codes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($qrCodes as $qr)
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition">
                <div class="bg-gray-100 rounded-lg p-6 mb-6 flex items-center justify-center min-h-[280px]">
                    <img src="{{ $qr['qr_image_url'] }}" alt="Room {{ $qr['room_number'] }} QR Code" style="max-width: 200px; width: 100%; height: auto;">
                </div>

                <div class="space-y-3 mb-6">
                    <div>
                        <div class="text-3xl font-black text-gray-900">Room {{ $qr['room_number'] }}</div>
                        <p class="text-sm text-gray-600">{{ $qr['room_name'] ?? 'Room' }}</p>
                    </div>

                    <div class="flex gap-2">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                            <i class="fas fa-door-open" style="margin-right: 4px;"></i>Room
                        </span>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                            <i class="fas fa-users" style="margin-right: 4px;"></i>{{ $qr['capacity'] }} seats
                        </span>
                    </div>

                    <div class="pt-2 border-t border-gray-200">
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-link"></i>
                            <span class="text-gray-900 font-mono text-xs">/room/{{ $qr['room_id'] }}/order</span>
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('settings.room.qr.download', $qr['room_id']) }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition text-sm">
                        <i class="fas fa-download"></i> Download PNG
                    </a>
                    <a href="{{ $qr['qr_url'] }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition text-sm"
                       title="Test scanning">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-600 text-lg">No rooms found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
