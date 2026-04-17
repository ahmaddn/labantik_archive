@extends('layouts.app')
@section('title', 'Dokumen – ' . $user->name)
@section('page-title', 'Detail Siswa')

@section('content')
    @include('admin._partials.user_docs_show', [
        'backRoute'  => 'admin.students.index',
        'backLabel'  => 'Data Siswa',
        'identifier' => 'nis',
        'identLabel' => 'NIS',
    ])
@endsection
