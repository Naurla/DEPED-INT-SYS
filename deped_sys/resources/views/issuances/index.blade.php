@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-primary">Department Issuances</h2>
            <p class="text-muted">Stay updated with the latest orders, memorandums, and advisories.</p>
        </div>
        <div class="col-md-6">
            <form action="{{ route('issuances.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by title or number..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    <div id="issuances-list">
        @include('issuances.partials.list')
    </div>
</div>
@endsection