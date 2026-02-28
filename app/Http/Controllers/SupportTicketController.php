<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportTicketController extends Controller
{
    /**
     * List all tickets (admin)
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'admin']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $tickets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tickets->items(),
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
            'stats' => [
                'total' => SupportTicket::count(),
                'open' => SupportTicket::where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved' => SupportTicket::where('status', 'resolved')->count(),
                'closed' => SupportTicket::where('status', 'closed')->count(),
            ]
        ]);
    }

    /**
     * Store a new ticket (public - from support page)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'order_code' => 'nullable|string|max:100',
        ]);

        // Get authenticated user (works even without auth middleware)
        $user = auth('sanctum')->user();

        $ticket = SupportTicket::create([
            'ticket_code' => SupportTicket::generateTicketCode(),
            'user_id' => $user?->id,
            'name' => $request->name,
            'email' => $request->email,
            'category' => $request->category,
            'subject' => $request->subject,
            'message' => $request->message,
            'order_code' => $request->order_code,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        // Send automatic message from admin to user (if user is logged in)
        $this->sendAutoMessageToUser($ticket, $user);

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu hỗ trợ đã được gửi thành công!',
            'data' => $ticket,
        ], 201);
    }

    /**
     * Send automatic message from admin to user when ticket is created
     */
    private function sendAutoMessageToUser(SupportTicket $ticket, ?User $user)
    {
        // Only send if user is logged in
        if (!$user) {
            return;
        }

        // Find the first admin to send message from (system admin)
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            return;
        }

        // Category labels in Vietnamese
        $categoryLabels = [
            'account' => 'Vấn đề tài khoản',
            'payment' => 'Thanh toán & nạp tiền',
            'order' => 'Đơn hàng & sản phẩm',
            'refund' => 'Hoàn tiền',
            'technical' => 'Lỗi kĩ thuật',
            'other' => 'Khác',
        ];

        $categoryName = $categoryLabels[$ticket->category] ?? $ticket->category;

        // Compose professional auto-reply message
        $message = "🎫 Yêu cầu hỗ trợ đã được tiếp nhận\n\n"
            . "Xin chào {$ticket->name},\n\n"
            . "Chúng tôi đã nhận được yêu cầu hỗ trợ của bạn.\n\n"
            . "📋 Chi tiết ticket\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "Mã: {$ticket->ticket_code}\n"
            . "Danh mục: {$categoryName}\n"
            . "Tiêu đề: {$ticket->subject}\n"
            . "━━━━━━━━━━━━━━━━━━━━\n\n"
            . "Đội ngũ hỗ trợ sẽ phản hồi trong thời gian sớm nhất. Mọi cập nhật sẽ được gửi qua tin nhắn này.\n\n"
            . "Cảm ơn bạn đã tin tưởng GameTech! 💜";

        // Insert message into database
        DB::table('messages')->insert([
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'content' => $message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Show a single ticket
     */
    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'admin'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    /**
     * Update ticket (admin - reply, change status/priority)
     */
    public function update(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $data = [];
        $hasNewReply = false;
        $newReplyContent = null;

        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        if ($request->has('priority')) {
            $data['priority'] = $request->priority;
        }

        if ($request->has('admin_reply')) {
            $data['admin_reply'] = $request->admin_reply;
            $data['admin_id'] = $request->user()->id;
            $data['replied_at'] = now();
            $hasNewReply = true;
            $newReplyContent = $request->admin_reply;
        }

        $ticket->update($data);

        // Send message to user when admin replies (if user exists)
        if ($hasNewReply && $ticket->user_id) {
            $this->sendReplyMessageToUser($ticket, $request->user(), $newReplyContent);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công!',
            'data' => $ticket->fresh(['user', 'admin']),
        ]);
    }

    /**
     * Send message to user when admin replies to ticket
     */
    private function sendReplyMessageToUser(SupportTicket $ticket, User $admin, string $replyContent)
    {
        if (!$ticket->user_id) {
            return;
        }

        // Status labels
        $statusLabels = [
            'open' => 'Mở',
            'in_progress' => 'Đang xử lý',
            'resolved' => 'Đã giải quyết',
            'closed' => 'Đã đóng',
        ];

        $statusName = $statusLabels[$ticket->status] ?? $ticket->status;

        // Compose reply notification message
        $message = "📬 Phản hồi ticket #{$ticket->ticket_code}\n\n"
            . "📋 {$ticket->subject}\n"
            . "Trạng thái: {$statusName}\n\n"
            . "━━━━━━━━━━━━━━━━━━━━\n\n"
            . "{$replyContent}\n\n"
            . "━━━━━━━━━━━━━━━━━━━━\n\n"
            . "Nếu cần hỗ trợ thêm, hãy phản hồi tin nhắn này.\n\n"
            . "— Đội ngũ GameTech 💜";

        // Insert message into database
        DB::table('messages')->insert([
            'sender_id' => $admin->id,
            'receiver_id' => $ticket->user_id,
            'content' => $message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Delete ticket
     */
    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa yêu cầu hỗ trợ!',
        ]);
    }
}
