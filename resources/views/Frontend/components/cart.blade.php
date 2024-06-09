<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>

    <div class="header-cart flex-col-l p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">
                Filtrele
            </span>

            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w js-pscroll">
            <form action="{{ route('Kiyasla') }}" method="GET" class="header-cart-content flex-w js-pscroll">
                <ul class="header-cart-wrapitem w-full">
                    <!-- Hedef Kategorisi -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label for="category-select" class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">
                                Kategori:
                            </label>
                            <select id="category-select" name="category_id" class="form-control m-b-10">
                                <option value="" selected>TÜMÜ</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>

                    <!-- Hedef Bilgisi -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label for="target-select" class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">
                                Hedef Tipi:
                            </label>
                            <select id="target-select" name="target_type" class="form-control m-b-10">
                                <option value="" selected>Hedef seçin...</option>
                                <option value="HARD">SAM-Ana Operasyon Merkezi-Tam Korumalı</option>
                                <option value="HARD">SAM-Mobil SAM</option>
                                <option value="HARD">SAM-Füze Taşıyıcı Araç Sığınağı Tam Korumalı</option>
                                <option value="HARD">SAM-Radar Bölgesi K2B Merkezi</option>
                                <option value="HARD">SAM-Füze Bataryası K2 Merkezi/option>
                                <option value="HARD">SAM-Tespit Radarı Kontrol Merkezi</option>
                                <option value="HARD">SAM-Atış Kontrol Radarı Komuta Merkezi</option>
                                <option value="HARD">SSM- Mobil SSM Lançeri</option>
                                <option value="HARD">SSM- Füze Taşıyıcı Araç Sığınağı Tam Korumalı</option>
                                <option value="HARD">SSM- Füze Başlığı Depo Binası</option>
                                <option value="HARD">SSM- Füze Montaj Atelyesi</option>
                                <option value="HARD">SSM- Ana Operasyon Merkezi Tam Korumalı</option>
                                <option value="HARD">Radar Mevzii- Mobil SAM</option>
                                <option value="HARD">Radar Mevzii- Ana Operasyon Merkezi Tam Korumalı</option>
                                <option value="HARD">Radar Mevzii- Operasyon Binası</option>
                                <option value="HARD">Radar Mevzii- Muhabere Merkezi</option>
                                <option value="HARD">Limanlar- Mobil SAM</option>
                                <option value="HARD">Limanlar- Yüzer Vasıtalar</option>
                                <option value="HARD">Limanlar- Deniz Harp Vasıtası</option>
                                <option value="HARD">Limanlar- Rıhtım/İskele</option>
                                <option value="HARD">Limanlar- Tam Korumalı Mühimmat Deposu</option>
                                <option value="HARD">Limanlar- Yakıt Deposu Tam Korumalı</option>
                                <option value="HARD">Limanlar- Ana Operasyon Merkezi</option>
                                <option value="HARD">Limanlar- Muhabere Merkezi</option>
                                <option value="HARD">Endüstriyel Tesisler- Mobil SAM</option>
                                <option value="HARD">Endüstriyel Tesisler- Üretim Binası</option>
                                <option value="HARD">Endüstriyel Tesisler- Montaj Binası</option>
                                <option value="HARD">Endüstriyel Tesisler- Eritme Fırını</option>
                                <option value="HARD">Endüstriyel Tesisler- Atmosferik Damıtma Kulesi</option>
                                <option value="HARD">Endüstriyel Tesisler- Tam Korumalı Mühimmat Deposu</option>
                                <option value="HARD">Endüstriyel Tesisler- Takat Merkezi</option>
                                <option value="HARD">Takat Kaynakları- Mobil SAM</option>
                                <option value="HARD">Takat Kaynakları- Buhar Kazanı ve Yanma Odası</option>
                                <option value="HARD">Takat Kaynakları- Transformatör Kontrol Binası</option>
                                <option value="HARD">Takat Kaynakları- Yakıt Deposu Tam Korumalı</option>
                                <option value="HARD">Takat Kaynakları- Yakıt Deposu (Toprak içi-Beton)</option>
                                <option value="HARD">Takat Kaynakları- Ana Operasyon Merkezi Tam Korumalı</option>
                                <option value="HARD">Takat Kaynakları- Operasyon Binası</option>
                                <option value="HARD">Meydanlar- Ana Pist </option>
                                <option value="HARD">Meydanlar- Emercensi Pist</option>
                                <option value="HARD">Meydanlar- Uçak Sığınağı</option>
                                <option value="HARD">Meydanlar- Tam Korumalı Mühimmat Deposu</option>
                                <option value="HARD">Meydanlar- Uçak Bakım Hangarı</option>
                                <option value="HARD">Meydanlar- Yakıt Deposu Tam Korumalı</option>
                                <option value="HARD">Meydanlar- Savaş Harekât Merkezi</option>
                                <option value="HARD">Depolar- Mobil SAM </option>
                                <option value="HARD">Depolar- Tam Korumalı Mühimmat Deposu</option>
                                <option value="HARD">Depolar- Ana Operasyon Merkezi Tam Korumalı</option>
                                <option value="SOFT">SAM-Atış Kontrol Radarı</option>
                                <option value="SOFT">SAM-Hedef Tespit Radarı</option>
                                <option value="SOFT">SAM-Füze Bataryası</option>
                                <option value="SOFT">SAM-TEL</option>
                                <option value="SOFT">SAM-TELAR</option>
                                <option value="SOFT">SAM-Sabit SAM Lançeri</option>
                                <option value="SOFT">SAM-Komuta Kontrol/Muhabere Veni</option>
                                <option value="SOFT">SSM-Sabit SSM Lançeri</option>
                                <option value="SOFT">SSM-Toprak Refüjlü Füze Başlığı Deposu</option>
                                <option value="SOFT">SSM-Komuta Kontrol/Muhabere Veni</option>
                                <option value="SOFT">SSM- Meteroloji Radarı</option>
                                <option value="SOFT">Radar Mevzii- Radome</option>
                                <option value="SOFT">Radar Mevzii- Radar Anteni</option>
                                <option value="SOFT">Radar Mevzii-Komuta Kontrol/Muhabere Veni</option>
                                <option value="SOFT">Radar Mevzii-Muhabere Antenleri</option>
                                <option value="SOFT">Limanlar- Muhabere Antenleri</option>
                                <option value="SOFT">Endüstriyel Tesisler- Buhar Üretme Ünitesi</option>
                                <option value="SOFT">Endüstriyel Tesisler- Soğutma Ünitesi(Su)</option>
                                <option value="SOFT">Endüstriyel Tesisler- Soğutma Ünitesi(Hava)</option>
                                <option value="SOFT">Endüstriyel Tesisler- POL Deposu</option>
                                <option value="SOFT">Endüstriyel Tesisler- Transformatör</option>
                                <option value="SOFT">Takat Kaynakları- Türbin Jeneratör Bölümü</option>
                                <option value="SOFT">Takat Kaynakları- Dizel Motor Jeneratör</option>
                                <option value="SOFT">Takat Kaynakları- Batarya Tipi Soğutma Ünitesi</option>
                                <option value="SOFT">Takat Kaynakları- Transformatör</option>
                            </select>
                        </div>
                    </li>

                    <!-- Menzil Bilgisi -->
                    <li class="header-cart-item flex-w flex-t m-b-12">
                        <div class="header-cart-item-txt p-t-8 w-full">
                            <label class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block">
                                Menzil:
                            </label>
                            <div class="m-b-10">
                                <div class="m-b-10">
                                    <label for="range-min" class="header-cart-item-name m-b-5 hov-cl1 trans-04">
                                        Min
                                    </label>
                                    <input type="number" id="range-min" name="min" class="form-control"
                                        placeholder="Min">
                                </div>
                                <div>
                                    <label for="range-max" class="header-cart-item-name m-b-5 hov-cl1 trans-04">
                                        Max
                                    </label>
                                    <input type="number" id="range-max" name="max" class="form-control"
                                        placeholder="Max">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="w-full">
                    <button type="submit"
                        class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                        Uygula
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
