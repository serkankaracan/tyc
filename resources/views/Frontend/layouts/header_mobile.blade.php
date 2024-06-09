    <!-- Header Mobile -->
    <div class="wrap-header-mobile">
        <!-- Logo moblie -->
        <div class="logo-mobile">
            <a href="{{ route('AnaSayfa') }}"><img src="{{ asset('frontend_assets/images/icons/logo-03.png') }}"
                    alt="IMG-LOGO"></a>
        </div>

        <!-- Icon header -->
        <div class="wrap-icon-header flex-w flex-r-m h-full m-r-15">
            <div class="flex-c-m h-full p-r-10">
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 js-show-modal-search">
                    <i class="zmdi zmdi-search"></i>
                </div>
            </div>

            <div class="flex-c-m h-full p-lr-10 bor5">
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-lr-11 js-show-cart">
                    <i class="zmdi zmdi-filter-list"></i>
                </div>
            </div>
        </div>

        <!-- Button show menu -->
        <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </div>
    </div>


    <!-- Menu Mobile -->
    <div class="menu-mobile">
        <ul class="main-menu-m">
            <li class="{{ request()->routeIs('AnaSayfa') ? ' active-menu' : '' }}">
                <a href="{{ route('AnaSayfa') }}">Ana Sayfa</a>
            </li>

            <li>
                <a href="#">Kategoriler</a>
                <ul class="sub-menu">
                    @foreach ($categories as $category)
                        @if ($category->parent_id === null)
                            <li><a href="{{ route('kategoriFiltresi', $category->slug) }}">{{ $category->name }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </li>

            <li class="{{ request()->routeIs('Blog') ? ' active-menu' : '' }}">
                <a href="{{ route('Blog') }}">Blog</a>
            </li>

            <li class="{{ request()->routeIs('Hakkimizda') ? ' active-menu' : '' }}">
                <a href="{{ route('Hakkimizda') }}">Hakkımızda</a>
            </li>

            <li class="{{ request()->routeIs('Iletisim') ? ' active-menu' : '' }}">
                <a href="{{ route('Iletisim') }}">İletişim</a>
            </li>
        </ul>
    </div>
