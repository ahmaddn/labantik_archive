@extends('layouts.app')
@section('title', 'Dokumen – ' . $user->name)
@section('page-title', 'Detail Guru')

@section('content')
    @include('admin._partials.user_docs_show', [
        'backRoute'  => 'admin.teachers.index',
        'backLabel'  => 'Data Guru',
        'identifier' => 'nip',
        'identLabel' => 'NIP',
    ])
@endsection
