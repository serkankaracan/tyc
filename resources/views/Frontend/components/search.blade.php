<div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
    <div class="container-search-header">
        <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
            <img src="{{ asset('frontend_assets/images/icons/icon-close2.png') }}" alt="KAPAT">
        </button>

        <form action="{{ route('search') }}" method="GET" class="wrap-search-header flex-w p-l-15">
            <button type="submit" class="flex-c-m trans-04">
                <i class="zmdi zmdi-search"></i>
            </button>
            <input class="plh3" type="text" name="q" placeholder="Ara...">
        </form>
    </div>
</div>
