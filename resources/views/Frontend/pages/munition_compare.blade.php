@extends('Frontend.index')

@section('title', '| Mühimmat Karşılaştırma')

@section('content')

    <div class="container">

        <br>

        <div class="row">
            <div class="col-md-12">
                <h2>Mühimmat Karşılaştırma</h2>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                @foreach ($munitions as $munition)
                                    <th>{{ $munition->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attributes as $attribute)
                                <tr>
                                    <td>{{ $attribute->name }}</td>
                                    @foreach ($munitions as $munition)
                                        <td>
                                            @php
                                                $attrValue = $munition->attributes
                                                    ->where('id', $attribute->id)
                                                    ->first();
                                            @endphp
                                            @if ($attrValue)
                                                @if ($attribute->option === 'Liste')
                                                    {{ $attribute->listValues->where('id', $attrValue->pivot->value)->first()->value ?? '' }}
                                                @else
                                                    {{ $attrValue->pivot->value ?? '' }}
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>Puan</strong></td>
                                @foreach ($munitions as $munition)
                                    <td><strong>{{ $munition->score }}</strong></td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <br>

    </div>

@endsection
