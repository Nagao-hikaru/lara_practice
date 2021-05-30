@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <a href="{{ route('contact.create') }}">新規作成</a>
                    <br>
                    @foreach($contacts as $contact)
                        {{ $contact->id }}
                        {{ $contact->your_name }}
                        {{ $contact->title }}
                        {{ $contact->created_at }}
                    @endforeach
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">id</th>
                            <th scope="col">your_name</th>
                            <th scope="col">title</th>
                            <th scope="col">created_at</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($contacts as $contact)
                              
                          @endforeach
                          <tr>
                            <th scope="row">{{ $contact->id }}</th>
                            <td>{{ $contact->your_name }}</td>
                            <td>{{ $contact->title }}</td>
                            <td>{{ $contact->created_at }}</td>
                          </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
