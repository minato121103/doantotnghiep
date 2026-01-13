<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * Create VNPay payment URL for deposit
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPayment(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10000|max:100000000', // Min 10k, Max 100M VND
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $amount = $request->amount;
        $vnp_TxnRef = Transaction::generateTransactionCode('VNP');
        $vnp_OrderInfo = "Nap tien vao tai khoan - " . $user->name;
        $vnp_OrderType = 'other';
        $vnp_Amount = $amount * 100; // VNPay uses cents
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->bank_code ?? '';
        $vnp_IpAddr = $request->ip();

        // VNPay configuration
        $vnp_Url = config('services.vnpay.url');
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        
        // Use return URL from .env if available, otherwise generate from route
        $vnp_ReturnUrl = config('services.vnpay.return_url');
        if (empty($vnp_ReturnUrl)) {
            $vnp_ReturnUrl = url('/wallet/vnpay/callback');
        }

        // Validate config
        if (empty($vnp_TmnCode) || empty($vnp_HashSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'VNPay configuration is missing. Please check your .env file.'
            ], 500);
        }

        // Create pending transaction
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => $vnp_TxnRef,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $user->balance,
                'balance_after' => $user->balance, // Will be updated after payment success
                'status' => 'pending',
                'payment_method' => 'vnpay',
                'description' => $vnp_OrderInfo,
            ]);

            DB::commit();

            // Build VNPay payment URL
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_ReturnUrl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            // Only add vnp_BankCode if not empty
            if (!empty($vnp_BankCode)) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            // Remove empty values before sorting
            $inputData = array_filter($inputData, function($value) {
                return $value !== '' && $value !== null;
            });

            // Sort array by key
            ksort($inputData);
            
            // Build query string and hash data (exactly like VNPay sample code)
            $query = "";
            $hashdata = "";
            $i = 0;
            
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            
            // Build URL with query (keep the trailing '&')
            $vnp_Url = $vnp_Url . "?" . $query;
            
            // Add secure hash (VNPay sample code style - no '&' before vnp_SecureHash)
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment URL created successfully',
                'data' => [
                    'payment_url' => $vnp_Url,
                    'transaction_code' => $vnp_TxnRef,
                    'amount' => $amount,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VNPay payment creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle VNPay callback (return URL)
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? '';
        $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? '';
        $vnp_TransactionStatus = $inputData['vnp_TransactionStatus'] ?? '';
        $vnp_Amount = ($inputData['vnp_Amount'] ?? 0) / 100; // Convert from cents to VND

        $transaction = Transaction::where('transaction_code', $vnp_TxnRef)->first();

        if (!$transaction) {
            return redirect()->route('wallet')->with('error', 'Giao dịch không tồn tại.');
        }

        // Verify hash
        if ($secureHash !== $vnp_SecureHash) {
            $transaction->update(['status' => 'failed', 'description' => 'Invalid hash signature']);
            
            // Try to get user and create token to maintain login
            try {
                $user = $transaction->user;
                $token = $user->createToken('auth_token')->plainTextToken;
                $errorMessage = 'Chữ ký không hợp lệ.';
                return redirect(url('/wallet/payment/callback') . '?token=' . $token . '&user=' . urlencode(json_encode($user)) . '&message=' . urlencode($errorMessage) . '&message_type=error');
            } catch (\Exception $e) {
                return redirect()->route('wallet')->with('error', 'Chữ ký không hợp lệ.');
            }
        }

        // Check if already processed
        if ($transaction->status === 'completed') {
            // Get user and create token to maintain login
            try {
                $user = $transaction->user;
                $token = $user->createToken('auth_token')->plainTextToken;
                $successMessage = 'Giao dịch đã được xử lý thành công.';
                return redirect(url('/wallet/payment/callback') . '?token=' . $token . '&user=' . urlencode(json_encode($user)) . '&message=' . urlencode($successMessage) . '&message_type=success');
            } catch (\Exception $e) {
                return redirect()->route('wallet')->with('success', 'Giao dịch đã được xử lý thành công.');
            }
        }

        DB::beginTransaction();
        try {
            // Check payment status
            if ($vnp_ResponseCode == '00' && $vnp_TransactionStatus == '00') {
                // Payment successful
                $user = $transaction->user;
                $balanceBefore = $user->balance;
                $balanceAfter = $balanceBefore + $vnp_Amount;

                $transaction->update([
                    'status' => 'completed',
                    'balance_after' => $balanceAfter,
                    'description' => 'Nạp tiền thành công qua VNPay'
                ]);

                $user->update(['balance' => $balanceAfter]);
                
                // Refresh user to get updated balance
                $user->refresh();

                DB::commit();

                // Create token for user to maintain authentication
                $token = $user->createToken('auth_token')->plainTextToken;
                
                // Prepare success message
                $successMessage = 'Nạp tiền thành công! Số tiền ' . number_format($vnp_Amount, 0, ',', '.') . 'đ đã được cộng vào tài khoản.';
                
                // Redirect to payment callback page with token and user data
                return redirect(url('/wallet/payment/callback') . '?token=' . $token . '&user=' . urlencode(json_encode($user)) . '&message=' . urlencode($successMessage) . '&message_type=success');
            } else {
                // Payment failed
                $transaction->update([
                    'status' => 'failed',
                    'description' => 'Thanh toán thất bại. Mã lỗi: ' . $vnp_ResponseCode
                ]);

                DB::commit();
                
                // Get user to create token even for failed payment (to maintain login)
                $user = $transaction->user;
                $token = $user->createToken('auth_token')->plainTextToken;
                
                $errorMessage = 'Thanh toán thất bại. Vui lòng thử lại.';
                
                // Redirect to payment callback page with token and error message
                return redirect(url('/wallet/payment/callback') . '?token=' . $token . '&user=' . urlencode(json_encode($user)) . '&message=' . urlencode($errorMessage) . '&message_type=error');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VNPay callback error: ' . $e->getMessage());
            
            // Try to get user and create token even on error
            try {
                $user = $transaction->user;
                $token = $user->createToken('auth_token')->plainTextToken;
                $errorMessage = 'Đã xảy ra lỗi khi xử lý giao dịch.';
                return redirect(url('/wallet/payment/callback') . '?token=' . $token . '&user=' . urlencode(json_encode($user)) . '&message=' . urlencode($errorMessage) . '&message_type=error');
            } catch (\Exception $e2) {
                // If we can't get user, redirect to wallet without token
                return redirect()->route('wallet')->with('error', 'Đã xảy ra lỗi khi xử lý giao dịch.');
            }
        }
    }

    /**
     * Handle VNPay IPN (Instant Payment Notification)
     * Payment Notify - Ghi nhận kết quả thanh toán từ VNPAY
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ipn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $returnData = array();
        
        // Only get parameters starting with "vnp_"
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        
        // Sort array by key
        ksort($inputData);
        
        // Build hash data
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        // Verify checksum
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // Extract data
        $vnpTranId = $inputData['vnp_TransactionNo'] ?? ''; // Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode'] ?? ''; // Ngân hàng thanh toán
        $vnp_Amount = ($inputData['vnp_Amount'] ?? 0) / 100; // Số tiền thanh toán VNPAY phản hồi
        $orderId = $inputData['vnp_TxnRef'] ?? '';
        $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? '';
        $vnp_TransactionStatus = $inputData['vnp_TransactionStatus'] ?? '';
        
        try {
            // Check Orderid - Tìm giao dịch trong database
            $transaction = Transaction::where('transaction_code', $orderId)->first();
            
            // Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                if ($transaction != NULL) {
                    // Kiểm tra số tiền giữa hai hệ thống
                    if ($transaction->amount == $vnp_Amount) {
                        // Kiểm tra tình trạng của giao dịch trước khi cập nhật
                        // Chỉ xử lý nếu trạng thái là 'pending' (Status = 0)
                        if ($transaction->status == 'pending') {
                            DB::beginTransaction();
                            try {
                                if ($vnp_ResponseCode == '00' || $vnp_TransactionStatus == '00') {
                                    // Trạng thái thanh toán thành công
                                    $user = $transaction->user;
                                    $balanceBefore = $user->balance;
                                    $balanceAfter = $balanceBefore + $vnp_Amount;
                                    
                                    // Cập nhật kết quả vào Database
                                    $transaction->update([
                                        'status' => 'completed',
                                        'balance_after' => $balanceAfter,
                                        'description' => 'Nạp tiền thành công qua VNPay (IPN). Mã GD: ' . $vnpTranId . ', Ngân hàng: ' . $vnp_BankCode
                                    ]);
                                    
                                    $user->update(['balance' => $balanceAfter]);
                                    
                                    DB::commit();
                                    
                                    // Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công
                                    $returnData['RspCode'] = '00';
                                    $returnData['Message'] = 'Confirm Success';
                                } else {
                                    // Trạng thái thanh toán thất bại / lỗi
                                    $transaction->update([
                                        'status' => 'failed',
                                        'description' => 'Thanh toán thất bại qua VNPay (IPN). Mã lỗi: ' . $vnp_ResponseCode
                                    ]);
                                    
                                    DB::commit();
                                    
                                    $returnData['RspCode'] = '00';
                                    $returnData['Message'] = 'Confirm Success';
                                }
                            } catch (\Exception $e) {
                                DB::rollBack();
                                Log::error('VNPay IPN update error: ' . $e->getMessage());
                                $returnData['RspCode'] = '99';
                                $returnData['Message'] = 'Unknow error';
                            }
                        } else {
                            // Order already confirmed
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    } else {
                        // Invalid amount
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                    }
                } else {
                    // Order not found
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                // Invalid signature
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (\Exception $e) {
            Log::error('VNPay IPN error: ' . $e->getMessage());
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
        
        // Trả lại VNPAY theo định dạng JSON
        return response()->json($returnData);
    }
}

