<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        //sử dụng để tìm người dùng trong cơ sở dữ liệu dựa trên địa chỉ email được cung cấp trong yêu cầu đăng nhập.
        $user = User::where('email', $request->email)->first();

        //kiểm tra xem thông tin đăng nhập được cung cấp có chính xác hay không.
        //!$user: Điều kiện này kiểm tra xem biến $user có tồn tại hay không. Nếu biến $user không tồn tại (null) hoặc là giá trị sai (false), điều kiện này sẽ trả về true, tức là không tìm thấy người dùng trong cơ sở dữ liệu.
       // !Hash::check($request->password, $user->password): Điều kiện này sử dụng Hash::check() để so sánh mật khẩu được cung cấp trong yêu cầu với mật khẩu đã lưu trữ trong cơ sở dữ liệu của người dùng.
       // Phương thức Hash::check() sẽ kiểm tra xem hai mật khẩu có khớp nhau hay không. Nếu mật khẩu không khớp, điều kiện này sẽ trả về true.
        if (!$user || !Hash::check($request->password, $user->password)){

            //Nếu một trong hai điều kiện trên trả về true, điều này có nghĩa là thông tin đăng nhập không chính xác. Trong trường hợp này, một ngoại lệ ValidationException được ném ra.
          throw ValidationException::withMessages([
              //Trong trường hợp này, thông báo lỗi được đặt là "The provided credentials are incorrect." và được gắn với trường email. Điều này có nghĩa là khi ngoại lệ được xử lý, người dùng sẽ nhận được thông báo lỗi "The provided credentials are incorrect." hiển thị với trường email.
              'email' => ['The provided credentials are incorrect.'],
          ]);
        }
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken
        ]);


    }
}
