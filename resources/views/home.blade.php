@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Ajout rapide :</div>

                    <div class="card-body">
                        <form action="headache" method="post">
                            @csrf

                            <div class="form-group row">
                                <label for="date" class="col-md-4 col-form-label text-md-right">Date :</label>

                                <div class="col-md-6">
                                    <input id="date" type="date" class="form-control " name="date" required
                                           value="{{ \Carbon\Carbon::now()->format("Y-m-d") }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="time" class="col-md-4 col-form-label text-md-right">Heure :</label>

                                <div class="col-md-6">
                                    <input id="time" type="time" class="form-control " name="time" required
                                           value="{{ \Carbon\Carbon::now()->format("H:i") }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="strength" class="col-md-4 col-form-label text-md-right">Force :</label>

                                <div class="col-md-6">
                                    <input id="strength" type="range" class="form-control " name="strength" required
                                           value="5"
                                           min="0"
                                           max="10">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="comment" class="col-md-4 col-form-label text-md-right">Commentaire :</label>

                                <div class="col-md-6">
                                    <input id="comment" type="text" class="form-control " name="comment">
                                </div>
                            </div>

                            <div class="col-10 text-center">
                                <input type="submit" value="Ajouter" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">Tableau de bord :</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <b><u>Mes dernières migraines :</u></b>
                        <ul>
                            @foreach($lastHeadaches as $ha)
                                <li>Le {{ $ha->date->format('d/m/Y') }} à {{ $ha->time }} ({{ $ha->strength  }}/10) <a href="/headache/{{ $ha->id }}/delete">Effacer</a></li>
                            @endforeach
                        </ul>

                        <a href="/export">Exporter</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
