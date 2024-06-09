@extends('Frontend.index')

@section('title', '| Ana Sayfa')

@section('content')

    <!-- Banner -->
    @include('Frontend.layouts.banner')
    {{-- @include('Frontend.layouts.banner2') --}}

    <section class="bg0 p-t-23 p-b-130">
        <div class="container">
            <div class="p-b-10">
                <h3 class="ltext-103 cl5">
                    Mühimmatlara Bakış
                </h3>
            </div>
            <div class="flex-w flex-sb-m p-b-52">
                <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                    <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                        Tümü
                    </button>
                    @foreach ($categories as $category)
                        @if ($category->parent_id === null)
                            <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                                data-filter=".{{ $category->slug }}">
                                {{ $category->name }}
                            </button>
                        @endif
                    @endforeach
                </div>

                <div class="flex-w flex-c-m m-tb-10">
                    <div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4">
                        Kıyasla
                    </div>
                    <div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
                        <i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
                        <i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                        Filtre
                    </div>
                    <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
                        <i class="icon-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                        <i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                        Ara
                    </div>
                </div>

                <!-- Search product -->
                <div class="dis-none panel-search w-full p-t-10 p-b-15">
                    <form action="{{ route('search') }}" method="GET" role="search">
                        @csrf
                        <div class="bor8 dis-flex p-l-15">
                            <button type="submit" class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                            <input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="q"
                                placeholder="Ara">
                        </div>
                    </form>
                </div>

                <!-- Filter -->
                @include('Frontend.components.filter')

            </div>

            <div class="row isotope-grid">
                @forelse ($munitions as $munition)
                    @php
                        $topCategory = $munition->category; // Mühimmata ait kategoriyi başlangıçta üst kategori olarak kabul ediyoruz
                        while ($topCategory->parent_id !== null) {
                            $topCategory = $topCategory->parent; // Üst kategoriyi bul
                        }
                    @endphp
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 p-b-35 isotope-item {{ $topCategory->slug }}">
                        <!-- Block2 -->
                        <div class="block2">
                            <div class="block2-pic hov-img0">
                                @if ($munition->images->count() > 0)
                                    <a href="{{ route('muhimmatDetay', $munition->slug) }}">
                                        <img src="{{ asset('storage/' . $munition->images[0]->url) }}" alt="IMG-PRODUCT">
                                    </a>
                                @else
                                    <img src="{{ asset('backend_assets/compiled/jpg/origami.jpg') }}" alt="IMG-PRODUCT">
                                @endif

                                <a href="#"
                                    class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1"
                                    data-id="{{ $munition->id }}">
                                    Önizle
                                </a>
                            </div>
                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l ">
                                    <a href="{{ route('muhimmatDetay', $munition->slug) }}"
                                        class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                        {{ $munition->name }}
                                    </a>
                                    <span class="stext-105 cl3">
                                        ${{ number_format($munition->price, 2, ',', '.') }}
                                    </span>
                                </div>
                                <div class="block2-txt-child2 flex-r p-t-3">
                                    <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                        <img class="icon-heart1 dis-block trans-04"
                                            src="{{ asset('frontend_assets/images/icons/icon-heart-01.png') }}"
                                            alt="ICON">
                                        <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                            src="{{ asset('frontend_assets/images/icons/icon-heart-02.png') }}"
                                            alt="ICON">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    @include('Frontend.components.modal')
                @empty
                    <div class="col-12">
                        <p class="text-center">Mühimmat bulunamadı.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($munitions->hasPages())
                <div class="flex-c-m flex-w w-full p-t-38">
                    @if ($munitions->previousPageUrl())
                        <a href="{{ $munitions->previousPageUrl() }}" class="flex-c-m how-pagination1 trans-04 m-all-7">
                            < </a>
                    @endif
                    @for ($i = 1; $i <= $munitions->lastPage(); $i++)
                        <a href="{{ $munitions->url($i) }}"
                            class="flex-c-m how-pagination1 trans-04 m-all-7{{ $i == $munitions->currentPage() ? ' active-pagination1' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    @if ($munitions->nextPageUrl())
                        <a href="{{ $munitions->nextPageUrl() }}" class="flex-c-m how-pagination1 trans-04 m-all-7">
                            >
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </section>


@endsection
