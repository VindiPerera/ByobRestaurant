@extends('layouts.app')

@section('title', 'Room History')

@section('content')
<div class="max-w-screen-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-clock-rotate-left text-purple-600"></i>Room History
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Past room bookings and their final bills</p>
        </div>
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('rooms.history') }}" class="bg-white rounded-lg p-5 mb-6 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Booking #, customer, phone, room #"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All</option>
                    <option value="completed" @selected($status === 'completed')>Completed</option>
                    <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="{{ route('rooms.history') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <tr class="text-gray-600">
                        <th class="text-left font-semibold px-4 py-3">Booking #</th>
                        <th class="text-center font-semibold px-4 py-3">Room</th>
                        <th class="text-left font-semibold px-4 py-3">Customer</th>
                        <th class="text-left font-semibold px-4 py-3">Started</th>
                        <th class="text-center font-semibold px-4 py-3">Duration</th>
                        <th class="text-right font-semibold px-4 py-3">Room</th>
                        <th class="text-right font-semibold px-4 py-3">Food</th>
                        <th class="text-right font-semibold px-4 py-3">Total</th>
                        <th class="text-center font-semibold px-4 py-3">Payment</th>
                        <th class="text-center font-semibold px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        @php
                            $mins = ($b->started_at && $b->checked_out_at) ? (int) $b->started_at->diffInMinutes($b->checked_out_at) : null;
                            $duration = is_null($mins) ? '—' : (intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm');
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $b->booking_number }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                    {{ $b->room?->room_number ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $b->customer_name ?: 'Walk-in' }}
                                @if($b->customer_phone)<div class="text-xs text-gray-400">{{ $b->customer_phone }}</div>@endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $b->started_at?->format('M d, Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $duration }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">Rs. {{ number_format($b->room_charge, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">Rs. {{ number_format($b->food_total, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">Rs. {{ number_format($b->total, 2) }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 capitalize">{{ $b->payment_method ? str_replace('_', ' ', $b->payment_method) : '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($b->status === 'completed')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Completed</span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-gray-400 py-12">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>No room bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
