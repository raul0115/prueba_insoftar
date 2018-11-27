@extends('layouts.app')

@section('content')
    <user-component></user-component>
@endsection
@push('scripts')
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
@endpush