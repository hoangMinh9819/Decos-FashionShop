<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;

class DonHangController extends Controller
{
    public function search_don_hang(Request $request)
    {
        $keyword = $request->keyword_submit;
        $search_don_hang = DB::table('don_hang')->where('TEN_NGUOI_NHAN', 'like', '%' . $keyword . '%')
            ->join('nguoi_dung', 'don_hang.ID_NGUOI_DUNG', '=', 'nguoi_dung.ID_NGUOI_DUNG')
            ->orwhere('DIEN_THOAI_NGUOI_NHAN', 'like', '%' . $keyword . '%')
            ->orwhere('HO_TEN', 'like', '%' . $keyword . '%')
            ->orwhere('DIA_CHI_GIAO', 'like', '%' . $keyword . '%')
            ->get();
        return view('nhan_vien.quan_li_don_hang.search_don_hang')->with('search_don_hang', $search_don_hang);
    }

    public function liet_ke_don_hang(){
        $danh_sach_don_hang = DB::table('don_hang')
        ->join('nguoi_dung', 'don_hang.ID_NGUOI_DUNG', '=', 'nguoi_dung.ID_NGUOI_DUNG')
        ->paginate(3);
        $quan_ly_don_hang = view('nhan_vien.quan_li_don_hang.liet_ke_don_hang')->with('liet_ke_don_hang',$danh_sach_don_hang);
        return view('nhan_vien.bo_cuc_nhan_vien')->with('nhan_vien.quan_li_don_hang.liet_ke_don_hang',$quan_ly_don_hang);
    }

    public function them_don_hang(){
        return view('nhan_vien.quan_li_don_hang.them_don_hang');
    }

    public function luu_don_hang(Request $request){
        $data['ID_NGUOI_DUNG'] = $request->ID_NGUOI_DUNG;
        $data['TEN_NGUOI_NHAN'] = $request->TEN_NGUOI_NHAN;
        $data['DIA_CHI_GIAO'] = $request->DIA_CHI_GIAO;
        $data['DIEN_THOAI_NGUOI_NHAN'] = $request->DIEN_THOAI_NGUOI_NHAN;
        $data['TONG_CONG_CUOI_CUNG'] = $request->TONG_CONG_CUOI_CUNG;
        $data['TRANG_THAI_DH'] = $request ->trang_thai_don_hang;
        DB::table('don_hang')->insert($data);
        Session::put('tin_nhan','Thêm đơn hàng thành công!');
        return Redirect::to('liet_ke_don_hang');
    }

    public function sua_don_hang($id){
        $sua_don_hang = DB::table('don_hang')->where('ID_DON_HANG',$id)->first();
        $quan_ly_don_hang = view('nhan_vien.quan_li_don_hang.sua_don_hang')->with('gia_tri',$sua_don_hang);
        return view('nhan_vien.bo_cuc_nhan_vien')->with('nhan_vien.quan_li_san_pham.sua_san_pham',$quan_ly_don_hang);
    }

    public function cap_nhat_don_hang(Request $request, $id){
        $data['TRANG_THAI_DH'] = $request ->trang_thai_don_hang;
        DB::table('don_hang')->where('ID_DON_HANG',$id)->update($data);
        Session::put('tin_nhan','Cập nhật thành công!');
        return Redirect::to('liet_ke_don_hang');
    }
    public function xoa_don_hang($id){
        $sua_san_pham = DB::table('don_hang')->where('ID_DON_HANG',$id)->delete();
        Session::put('tin_nhan','Xóa thành công!');
        return Redirect::to('liet_ke_don_hang');
    }
    
    public function lich_su_don_hang(){
        // $danh_sach_don_hang = DB::table('don_hang')
        // ->join('nguoi_dung', 'don_hang.ID_NGUOI_DUNG', '=', 'nguoi_dung.ID_NGUOI_DUNG')
        // ->paginate(3);
        
        $tat_ca_the_loai = DB::table('the_loai')
            ->where('TRANG_THAI', 'Hiển Thị')->get();
        $tat_ca_don_hang = DB::table('don_hang')->where('ID_NGUOI_DUNG',Session('id'))->get();
        $tat_ca_slide = DB::table('hinh_anh_slide')->get();
        $view = view('khach_hang.lich_su_don_hang')
            ->with('liet_ke_the_loai', $tat_ca_the_loai)
            ->with('tat_ca_don_hang', $tat_ca_don_hang)
            ->with('liet_ke_slide', $tat_ca_slide);
        return $view;
    }

    public function xem_chi_tiet_don_hang($id){   
        $don_hang = DB::table('don_hang')->where('ID_DON_HANG',$id)->first();
        $chi_tiet_don_hang = DB::table('chi_tiet_don_hang')->where('ID_DON_HANG',$id)
        ->join('san_pham', 'san_pham.ID_SAN_PHAM', '=', 'chi_tiet_don_hang.ID_SAN_PHAM')
        ->get();     
        $tat_ca_the_loai = DB::table('the_loai')
            ->where('TRANG_THAI', 'Hiển Thị')->get();
        $tat_ca_slide = DB::table('hinh_anh_slide')->get();
        $view = view('khach_hang.chi_tiet_don_hang')
            ->with('liet_ke_the_loai', $tat_ca_the_loai)
            ->with('chi_tiet_don_hang', $chi_tiet_don_hang)
            ->with('liet_ke_slide', $tat_ca_slide)
            ->with('don_hang', $don_hang);
        return $view;
    }
}
