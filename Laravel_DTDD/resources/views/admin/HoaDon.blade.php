@extends('admin.layout.index')

@section('content')

<?php
    //Hiển thị giá theo 1 định dạng khác
    function ShowPrice($price)
    {
        $price = $price."";
        $strPrice = "";
        while(strlen($price) >= 3)
        {
            $temp = substr($price, strlen($price)-3, strlen($price));
            if($strPrice == "") {
                $strPrice .= $temp;
            }else {
                $strPrice = $temp .'.'. $strPrice;    
            }
            $price = substr($price, 0, strlen($price)-3);
        }
        if(strlen($price) != 0)
        {
            $strPrice = $price .'.'. $strPrice;
        }

        return $strPrice;
    }
?>

<!-- NOI DUNG CHINH ..................................................................................-->
<div class="content">
    <div id="hoadon" class="tabcontent">
        <h2>QUẢN LÝ HÓA ĐƠN</h2>
        <input type="text" placeholder="Nhập mã hóa đơn" id="timKiem">
        <button class="btnThemdienthoai" id="btnTimHoaDon"><img src="DiDongZin/assets/img/search_30_ligghtpx.png">Tìm hóa đơn</button>
        <div id="hoaDon">
            @foreach ($hoaDon as $hd)
                <div class="hoadon">
                    
                    <table class="tb1">
                        <tr>
                            <th>Mã đơn hàng:</th>
                            <td>{{ $hd->Ma_hoa_don }}</td>
                            <th>Ngày tạo:</th>
                            <td>{{ $hd->Ngay_tao }}</td>
                        </tr>
                        <tr>
                            <th>Khách hàng:</th>
                            <td>{{ $hd->ToGioHang->ToTaiKhoan->Ho_va_ten_lot }} {{ $hd->ToGioHang->ToTaiKhoan->Ten }}</td>
                            <th>Mã khách hàng:</th>
                            <td>{{ $hd->ToGioHang->ToTaiKhoan->Ma_tai_khoan }}</td>
                        </tr>
                        <tr>
                            <th>Số điện thoại:</th>
                            <td>{{ $hd->ToGioHang->ToTaiKhoan->So_dien_thoai }}</td>
                            <th>Hình thức thanh toán:</th>
                            <td>{{ $hd->Hinh_thuc_thanh_toan }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ nhận hàng:</th>
                            <td colspan="3">{{ $hd->Dia_chi_nhan_hang }}</td>

                        </tr>
                    </table>
                    <div class="dropdown-btn">
                        <img src="DiDongZin/assets/img/xemthem.png">
                    </div>
                    <div class="dropdown-cnt">
                        <table class="tb2">
                            <tr>
                                <th>
                                    STT
                                </th>
                                <th>
                                    Điện thoại
                                </th>
                                <th>
                                    Số lượng
                                </th>
                                <th>
                                    Đơn giá
                                </th>
                                <th>
                                    Thành tiền
                                </th>
                            </tr>
                            <?php  
                                $count = 1;
                                $tongCong = 0;
                            ?>
                            @foreach ($hd->ToGioHang->ToChiTietGioHang as $chiTiet)
                                <tr>
                                    <td>
                                        {{ $count }}
                                        <?php
                                            $count++;
                                        ?>
                                    </td>
                                    <td>
                                    <img src="DiDongZin/imagePhone/{{ $chiTiet->ToDienThoaiDiDong->Hinh_anh }}" width="50px">
                                        <span>{{ $chiTiet->ToDienThoaiDiDong->Ten_dien_thoai }}</span>
                                    </td>
                                    <td>
                                        {{ $chiTiet->So_luong }}
                                    </td>
                                    <td>
                                        {{ ShowPrice($chiTiet->ToGiaBan->Gia) }} VND
                                    </td>
                                    <td>
                                        {{ ShowPrice($chiTiet->So_luong *  $chiTiet->ToGiaBan->Gia) }} VND
                                        <?php  
                                            $tongCong +=  $chiTiet->So_luong *  $chiTiet->ToGiaBan->Gia; 
                                        ?>
                                    </td>
                                </tr>
                            @endforeach
                            
                            <tr>
                                <th colspan="4">
                                    Tổng cộng:
                                </th>
                                <td>
                                    {{ ShowPrice($tongCong) }} VND
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    Thuế VAT:
                                </th>
                                <td>
                                    {{ ShowPrice(($hd->Thue_VAT / 100) * $tongCong) }} VND
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    Tổng thanh toán:
                                </th>
                                <td>
                                    {{ ShowPrice($tongCong + ($hd->Thue_VAT / 100) * $tongCong) }} VND
                                </td>
                            </tr>
                        </table>
                        
                        <div class="g-btn-xacnhan">
                            <button class="btnThemdienthoai" onclick="InHoaDon({{ $hd->Ma_hoa_don }})"><img src="DiDongZin/assets/img/print_30px.png">In hóa đơn</button>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="pagination">
                {!! $hoaDon->links() !!}
            </div>

        </div>
        {{-- ----END id="hoaDon" -------------------------------}}
    </div>
</div>

@endsection

@section('script')
    <script src="DiDongZin/assets/js/_hoadon.js"></script>
    <script>
        window.onload = function(){
            document.getElementById("hoaDonMenu").classList.add('active');
        }

        function InHoaDon(ma)
        {
            if(confirm('Bạn sẽ in hóa đơn có mã là: '+ ma))
            {
                window.location.href = "admin/hoadon/InHoaDon/"+ma;
            }
        }

        $(document).ready(function(){
            $('#btnTimHoaDon').click(function(){
                $id = document.getElementById("timKiem").value;

                $.get('admin/hoadon/TimHoaDonAjax/'+$id, function($data){
                    if($data == '0')
                    {
                        alert('Không tìm thấy mã hóa đơn: '+ $id);
                        document.getElementById("timKiem").value = "";
                    }
                    else
                    {
                        $("#hoaDon").html($data);
                    }                    
                });
            });
        });
    </script>  
@endsection