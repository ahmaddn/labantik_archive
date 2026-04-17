@extends('layouts.app')
@section('title', 'Dokumen – ' . $user->name)
@section('page-title', 'Detail Guru TU')

@section('content')
    @include('admin._partials.user_docs_show', [
        'backRoute'  => 'admin.piket.index',
        'backLabel'  => 'Data Guru TU',
        'identifier' => 'nip',
        'identLabel' => 'NIP',
    ])
@endsection
