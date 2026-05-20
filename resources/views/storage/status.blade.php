@extends('layouts.app')

@section('title', 'Storage Usage')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;

    function formatMB($bytes) {
        return number_format($bytes / 1048576, 2);
    }

    // Ambil semua file di storage Laravel (public)
    $files = Storage::allFiles('public');

    // Hitung total size semua file Laravel
    $totalUsed = collect($files)
        ->sum(fn ($file) => Storage::size($file));

    // Estimasi total (tidak real disk, hanya referensi)
    $estimatedLimitMB = 1024; // misal 1GB limit untuk project kamu

    $usedMB = $totalUsed;
    $freeMB = max(($estimatedLimitMB * 1048576) - $totalUsed, 0);

    $percent = $estimatedLimitMB > 0
        ? ($totalUsed / ($estimatedLimitMB * 1048576)) * 100
        : 0;
@endphp

<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            Storage Usage (Laravel Project Only)
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">
                    <div class="p-3 border rounded">
                        <h6>Total Used</h6>
                        <h3>{{ formatMB($usedMB) }} MB</h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border rounded">
                        <h6>Estimated Limit</h6>
                        <h3>{{ $estimatedLimitMB }} MB</h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border rounded">
                        <h6>Free Space</h6>
                        <h3>{{ formatMB($freeMB) }} MB</h3>
                    </div>
                </div>

            </div>

            <hr>

            <div class="mt-3">
                <h6>Usage Progress (Laravel Storage)</h6>

                <div class="progress" style="height: 22px;">
                    <div class="progress-bar"
                         role="progressbar"
                         style="width: {{ $percent }}%;">
                        {{ number_format($percent, 2) }}%
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection