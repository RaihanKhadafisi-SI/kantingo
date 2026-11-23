@extends('layouts.admin')

@section('title', 'Dashboard')
@section('judul', 'Dashboard')

@section('content')
    <div class="callout callout-info">
        <h5>Selamat datang, {{ auth()->user()->name }}!</h5>
        <p class="mb-0">
            Anda masuk sebagai <strong>{{ ucfirst(auth()->user()->role) }}</strong>
            di aplikasi Kantin Go.
        </p>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Total Pengguna</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('role', 'pembeli')->count() }}</h3>
                    <p>Pembeli</p>
                </div>
                <div class="icon"><i class="fas fa-shopping-basket"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('role', 'penjual')->count() }}</h3>
                    <p>Penjual</p>
                </div>
                <div class="icon"><i class="fas fa-store"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('role', 'admin')->count() }}</h3>
                    <p>Admin</p>
                </div>
                <div class="icon"><i class="fas fa-user-shield"></i></div>
            </div>
        </div>
    </div>
@endsection