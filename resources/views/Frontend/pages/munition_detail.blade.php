@extends('Frontend.index')

@section('title', '| Mühimmat Detay')

@section('content')

    <!-- breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="{{ route('AnaSayfa') }}" class="stext-109 cl8 hov-cl1 trans-04">
                Ana Sayfa
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            @php
                $categories = []; // Mühimmata ait kategorileri saklamak için boş bir dizi oluştur
                $topCategory = $munition->category; // Mühimmatın kategorisini al

                // Mühimmatın kategorisi ve üst kategorilerini diziye ekle
                while ($topCategory) {
                    array_unshift($categories, $topCategory); // Diziye üst kategorileri ekleyerek son üst kategori en başa gelecek
                    $topCategory = $topCategory->parent; // Üst kategoriye geç
                }
            @endphp

            @foreach ($categories as $category)
                <a href="{{ route('kategoriFiltresi', $category->slug) }}" class="stext-109 cl8 hov-cl1 trans-04">
                    {{ $category->name }}
                    <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                </a>
            @endforeach

            <span class="stext-109 cl4">
                {{ $munition->name }}
            </span>
        </div>
    </div>

    <!-- Product Detail -->
    <section class="sec-product-detail bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                            <div class="slick3 gallery-lb">
                                @if ($munition->images->isNotEmpty())
                                    @foreach ($munition->images as $image)
                                        <div class="item-slick3" data-thumb="{{ asset('storage/' . $image->url) }}">
                                            <div class="wrap-pic-w pos-relative">
                                                <img src="{{ asset('storage/' . $image->url) }}" alt="IMG-PRODUCT">

                                                <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                    href="{{ asset('storage/' . $image->url) }}">
                                                    <i class="fa fa-expand"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="item-slick3"
                                        data-thumb="{{ asset('frontend_assets/images/product-detail-03.jpg') }}">
                                        <div class="wrap-pic-w pos-relative">
                                            <img src="{{ asset('frontend_assets/images/product-detail-03.jpg') }}"
                                                alt="IMG-PRODUCT">

                                            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                                href="{{ asset('frontend_assets/images/product-detail-03.jpg') }}">
                                                <i class="fa fa-expand"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            {{ $munition->name }}
                        </h4>

                        <span class="mtext-106 cl2">
                            ${{ number_format($munition->price, 2, ',', '.') }}
                        </span>

                        <p class="stext-102 cl3 p-t-23">
                            {{ $munition->summary }}
                        </p>
                    </div>

                    <br>

                    <div class="p-r-50 p-t-5 p-lr-0-lg">

                        <ul class="p-lr-28 p-lr-15-sm">
                            @foreach ($munition->attributes as $attribute)
                                <li class="flex-w flex-t p-b-7">
                                    <span class="stext-102 cl3 size-205">
                                        <strong>{{ $attribute->name }}</strong>
                                    </span>
                                    <span class="stext-102 cl6 size-206">
                                        @php
                                            $attrValue = $attribute;
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
                                                {{ $attrValue->pivot->min ?? '' }} - {{ $attrValue->pivot->max ?? '' }}
                                            @else
                                                {{ $attrValue->pivot->value ?? '' }}
                                            @endif
                                        @endif
                                    </span>
                                </li>
                            @endforeach

                        </ul>

                    </div>
                </div>
            </div>

            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <!-- Tab01 -->
                <div class="tab01">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Açıklama</a>
                        </li>

                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab">Ek Bilgi</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-t-43">

                        <!-- description -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">
                                    {!! nl2br(e($munition->description)) !!}
                                </p>
                            </div>
                        </div>

                        <!-- information -->
                        <div class="tab-pane fade" id="information" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        @foreach ($munition->attributes as $attribute)
                                            <li class="flex-w flex-t p-b-7">
                                                <span class="stext-102 cl3 size-205">
                                                    <strong>{{ $attribute->name }}:</strong>
                                                </span>
                                                <span class="stext-102 cl6 size-206">
                                                    @php
                                                        $attrValue = $attribute;
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
                                                            {{ $attrValue->pivot->min ?? '' }} -
                                                            {{ $attrValue->pivot->max ?? '' }}
                                                        @else
                                                            {{ $attrValue->pivot->value ?? '' }}
                                                        @endif
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg6 flex-c-m flex-w size-302 m-t-73 p-tb-15">
            <span class="stext-107 cl6 p-lr-25">
                SKU: A2G-01
            </span>

            <span class="stext-107 cl6 p-lr-25">
                Kategori: {{ $munition->category->name }}
            </span>
        </div>
    </section>



@endsection
