<?php
    $dsMaTrongGioHang = array();
    $dsSoLuongTheoMa = array();
    $soLuongTrongGioHang = 0;
    $hasGioHang = false;
    if(Auth::check())
        // KHI ĐÃ ĐĂNG NHẬP
    {
        $maTK = Auth::user()->Ma_tai_khoan;
        $gioHang = App\TaiKhoan::find($maTK)->ToGioHang->last();

        // Nếu giỏ hàng tồn tại
        if($gioHang !== null)
        {
            // Kiểm tra giỏ hàng đã thanh toán chưa
            if($gioHang->Da_thanh_toan == 0)
            {
                $hasGioHang = true;
            }
        }

        if( $hasGioHang )
        {
            $dsChiTietGioHang = $gioHang->ToChiTietGioHang;
            $count = 0;
            foreach ($dsChiTietGioHang as $chiTiet) {
                $count = count($dsMaTrongGioHang);
                $dsMaTrongGioHang[$count] = $chiTiet->Ma_dien_thoai;
                $dsSoLuongTheoMa[$count] = $chiTiet->So_luong;

                // Số lượng điện thoại trong giỏ hàng
                $soLuongTrongGioHang += $chiTiet->So_luong;
            }
        }
    }
    else
        // KHI CHƯA ĐĂNG NHẬP
    {
        //Nếu biến count chưa được tạo
        if( session()->has('count') )
        {
            $soLuongDT = session()->get('count');

            // Đưa các điện thoại trong giỏ hàng vào dsMaDienThoai để hiện ra màn hình
            for ($i=0; $i < $soLuongDT; $i++) { 
                $maDT = session()->get('dt'.$i);
                $soLuong = session()->get('sl'.$i);

                $count = count($dsMaTrongGioHang);
                $dsMaTrongGioHang[$count] = $maDT;
                $dsSoLuongTheoMa[$count] = $soLuong;

                // Số lượng điện thoại trong giỏ hàng
                $soLuongTrongGioHang += $soLuong;
            }
        }            
    }
?>

<div class="container nav-top">
    <!-- Rounded switch -->
    <label class="switch">
        <input type="checkbox" id="switch1">
        <span class="slider round"></span>
    </label>
    <img id="show_company" src="DiDongZin/assets/img/menu_100px.png" alt="menu" onclick="show_company()">
    <div class="row">
        
        <div class="col-4" onclick="loadPage('TrangChu')">
            <img src="DiDongZin/assets/img/logo-min.png" alt="logo" />
            <h2 class="animated infinite pulse">DIDONGZIN</h2>
        </div>
        <div class="search-bar col-4">
            <input id ="text_search" type="text" placeholder="Tìm sản phẩm" />
            <img src="DiDongZin/assets/img/search_30px.png" alt="icon-search">
            <div id="search-results">
                
                {{-- <div class="phone-results">
                    <img src="DiDongZin/imagePhone/iphone11-black-1.png" alt="iphone 11"/>
                    <h2 class="name">iPhone11 64Gb Mới Chính Hãng</h2>
                    <span class="price">19.190.000 VND</span>
                </div> --}}
                
            </div>
        </div>
        <div class="col-4 right-nav-bar">
            <a id="cart-btn" href="ThanhToanGioHang" style="text-decoration: none;">
                <img src="DiDongZin/assets/img/shopping_cart_100px.png" alt="cart" />
                <span class="badge" id="iconGioHangTren">{{ $soLuongTrongGioHang }}</span>
                <p>Giỏ hàng</p>
                <div id="cart">
                    @for ($i = 0; $i < count($dsMaTrongGioHang); $i++)
                        {{-- Kiểm tra điện thoại có khuyến mãi không? ------}}
                        <?php
                            $dt = App\DienThoaiDiDong::find($dsMaTrongGioHang[$i]);

                            //Lấy khuyến mãi cuối cùng
                            $km = $dt->ToKhuyenMai->last();
                            $hasKM = false;
                            $phanTramKM;
                            $gia;
                            if($km !== null)
                            {
                                //Lấy ngày hiện tại
                                date_default_timezone_set('Asia/Ho_Chi_Minh');
                                $today = date('Y-m-d');

                                if($km->Tu_ngay<=$today && $today<=$km->Den_ngay)
                                {
                                    $hasKM = true;
                                    $phanTramKM = $km->Phan_tram_khuyen_mai;
                                }
                            }
                            if( $hasKM )
                            {
                                $gia = $dt->ToGiaBan->last()->Gia * (1- ($phanTramKM/100) );
                            }
                            else 
                            {
                                $gia = $dt->ToGiaBan->last()->Gia;
                            }                                
                        ?>
                        <div class="phone-results" id="{{ 'rowIconGioHang'.$dt->Ma_dien_thoai }}">
                            <img src="DiDongZin/imagePhone/{{ $dt->Hinh_anh }}" alt="iphone 11" />
                            <h2 class="name">{{ $dt->Ten_dien_thoai }}</h2>
                            <span class="price">{{ ShowPrice($gia) }}</span>
                            <span class="count" id="{{ 'soLuongIconGioHang'.$dt->Ma_dien_thoai }}">X {{ $dsSoLuongTheoMa[$i] }}</span>
                        </div>
                    @endfor
                </div>
            </a>
            <a id="myBtn">
                @if (Auth::check())
                    <img 
                        @if (Auth::user()->URL_Avatar == null)
                            src="DiDongZin/assets/img/male_user_100px.png"
                        @else
                            src="DiDongZin/avatar/{{ Auth::user()->URL_Avatar }}"
                        @endif
                    alt="user" />
                    <p>{{ Auth::user()->Ho_va_ten_lot }} {{ Auth::user()->Ten }}</p>
                    <div class="user-menu">
                        <img 
                            @if (Auth::user()->URL_Avatar == null)
                                src="DiDongZin/assets/img/male_user_100px.png"
                            @else
                                src="DiDongZin/avatar/{{ Auth::user()->URL_Avatar }}"
                            @endif
                        alt="user" />
                        <img src="DiDongZin/assets/img/camera_50px.png" alt="update-avatar" id="update_avatar">
                        <h2>{{ Auth::user()->Ho_va_ten_lot }} {{ Auth::user()->Ten }}</h2>
                        <p>{{ Auth::user()->Username }}</p>
                        <button onclick="loadPage('taikhoan/ThongTinCaNhan')">Quản lý tài khoản</button>
                        <button onclick="DangXuat()">Đăng Xuất</button>
                    </div>
                @else
                    <img src="DiDongZin/assets/img/male_user_100px.png" alt="user" />
                    <p>Đăng nhập</p>
                @endif
            </a>
        </div>
    </div>

</div>
<div id="company">
    <img src="DiDongZin/assets/img/back_30px.png" alt="back" onclick="hide_company()">
    <?php
        $dsHangDT = App\HangDienThoaiDiDong::all();
        $count = 0;
        foreach ($dsHangDT as $hang) { 
            echo '<a href="ChonHangDienThoai/'. $hang->Ma_hang_dien_thoai .'">'. $hang->Ten_hang .'</a>';

            // Cho phép hiện tối đa 8 cái hãng điện thoại
            $count++;
            if( $count == 8 ){
                break;
            }         
        }
    ?>
</div>

<script>    
    function DangXuat()
    {
        window.location.href = "logout";
    }

    window.onload = function(){
        count = document.getElementById('iconGioHangTren').innerHTML;
        document.getElementById('iconGioHangDuoi').innerHTML = count;
    }

    document.getElementById('text_search').onkeyup = function(e){
        noiDung = document.getElementById('text_search').value;
        // Cắt khoảng trắng thừa ở đầu và cuối chuỗi
        noiDung = noiDung.trim(noiDung);
        if(noiDung.length > 0)
        {
            document.getElementById('search-results').style.display = 'block';
        }
        else
        {
            document.getElementById('search-results').style.display = 'none';
        }
        if(e.keyCode == 13)
        {
            if(noiDung != '')
            {
                window.location.href = 'TimKiemTuKhoaDienThoai/'+noiDung;
            }            
        }
        else
        {
            $.get('TimDienThoaiAjax/'+noiDung, function(data){
                document.getElementById('search-results').innerHTML = data;
            });
        }
    }
</script>
