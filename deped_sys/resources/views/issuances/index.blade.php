@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-primary">Department Issuances</h2>
            <p class="text-muted">Stay updated with the latest orders, memorandums, and advisories.</p>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded">
            <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Categories</option>
                        <option value="advisory" {{ request('type') == 'advisory' ? 'selected' : '' }}>Advisories</option>
                        <option value="memorandum" {{ request('type') == 'memorandum' ? 'selected' : '' }}>Memoranda</option>
                        <option value="hrmpsb" {{ request('type') == 'hrmpsb' ? 'selected' : '' }}>HRMPSB</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        @php $currentYear = date('Y'); @endphp
                        @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search title..." value="{{ request('search') }}">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    <div id="issuances-list">
        @include('issuances.partials.list')
    </div>
</div>
@endsection