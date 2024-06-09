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
                @if ($munitions->isEmpty())
                    <p>Aradığınız kriterlere uygun sonuç bulunamadı.</p>
                @else
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
                                <tr>
                                    <td>Resim</td>
                                    @foreach ($munitions as $munition)
                                    <td>
                                        @if ($munition->images->isNotEmpty())
                                            <a href="{{ route('muhimmatDetay', $munition->slug) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $munition->images->first()->url) }}" alt="Mühimmat Resmi" style="max-width: 100px; max-height: 100px;">
                                            </a>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
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
                                                    @elseif ($attribute->option === 'Doğrulama')
                                                        @if ($attrValue->pivot->value == 1)
                                                            Var
                                                        @else
                                                            Yok
                                                        @endif
                                                    @elseif ($attribute->option === 'Aralık')
                                                        {{ $attrValue->pivot->min }}-{{ $attrValue->pivot->max }}
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
                                        <td>
                                            <strong>
                                                @php
                                                    $totalScore = 0;
                                                @endphp
                                                @foreach ($munition->attributes as $attribute)
                                                    @if ($attribute->pivot->score && $attribute->multiplier)
                                                        @php
                                                            $totalScore +=
                                                                $attribute->multiplier * $attribute->pivot->score;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                {{ $totalScore }}
                                            </strong>
                                        </td>
                                    @endforeach
                                </tr>

                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        <br>

    </div>

@endsection
