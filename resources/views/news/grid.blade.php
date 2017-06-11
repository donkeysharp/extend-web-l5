@extends('master')

@section('content')
<div id="grid-root"></div>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('assets/vendors/js/bootstrap-datepicker.es.min.js')}}"></script>
<script src="{{asset('assets/js/grid.bundle.js')}}"></script>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/bootstrap-datepicker.min.css')}}">
@endsection
