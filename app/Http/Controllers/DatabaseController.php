<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProductSimple;
use App\Models\News;
use App\Models\Order;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\SteamAccount;
use App\Models\ProductDiscussion;
use App\Models\PaymentMethod;
use App\Models\SupportTicket;
use App\Models\CommunityPost;
use App\Models\Promotion;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{

    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'products_count' => ProductSimple::count(),
            'news_count' => News::count(),
            'orders_count' => Order::count(),
            'reviews_count' => Review::count(),
            'transactions_count' => Transaction::count(),
            'steam_accounts_count' => SteamAccount::count(),
            'discussions_count' => ProductDiscussion::count(),
            'payment_methods_count' => PaymentMethod::count(),
            'support_tickets_count' => SupportTicket::count(),
            'community_posts_count' => CommunityPost::count(),
            'promotions_count' => Promotion::count(),
            'coupons_count' => Coupon::count(),
            'tables' => $this->getTablesList()
        ];
        
        return view('database.index', compact('stats'));
    }

    public function users()
    {
        return view('database.user.users');
    }

    public function createUser()
    {
        return view('database.user.create-user');
    }

    public function products()
    {
        return view('database.product.products');
    }

    public function editUser($id)
    {
        return view('database.user.edit-user');
    }

    public function editProduct($id)
    {
        return view('database.product.edit-product');
    }

    public function createProduct()
    {
        return view('database.product.create-product');
    }

    public function news()
    {
        return view('database.news.news');
    }

    public function createNews()
    {
        return view('database.news.create-news');
    }

    public function editNews($id)
    {
        return view('database.news.edit-news', ['id' => $id]);
    }

    // Orders Management
    public function orders()
    {
        return view('database.order.orders');
    }

    // Reviews Management
    public function reviews()
    {
        return view('database.review.reviews');
    }

    // Transactions Management
    public function transactions()
    {
        return view('database.transaction.transactions');
    }

    // Steam Accounts Management
    public function steamAccounts()
    {
        return view('database.steam.steam-accounts');
    }

    // Discussions Management
    public function discussions()
    {
        return view('database.discussion.discussions');
    }

    #chỉnh sửa danh sách quản lý bảng
    private function getTablesList()
    {
        $tables = DB::select('SHOW TABLES');
        $tableNames = [];
        
        $excludedTables = [
            'failed_jobs',
            'migrations',
            'personal_access_tokens',
            'password_resets',
        ];
        
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            
            if (!in_array($tableName, $excludedTables)) {
                $tableNames[] = $tableName;
            }
        }
        
        return $tableNames;
    }

    public function tableStructure($tableName)
    {
        $columns = DB::select("DESCRIBE {$tableName}");
        $data = DB::table($tableName)->limit(5)->get();
        
        return view('database.table-structure', compact('tableName', 'columns', 'data'));
    }

    // Support Tickets Management
    public function supportTickets()
    {
        return view('database.support.tickets');
    }

    // Community Posts Management
    public function communityPosts()
    {
        return view('database.community.posts');
    }

    // Promotions Management
    public function promotions()
    {
        return view('database.promotion.list');
    }

    public function createPromotion()
    {
        return view('database.promotion.create');
    }

    public function editPromotion($id)
    {
        return view('database.promotion.edit', ['id' => $id]);
    }

    // Coupons Management
    public function coupons()
    {
        return view('database.coupon.list');
    }

    public function createCoupon()
    {
        return view('database.coupon.create');
    }

    public function editCoupon($id)
    {
        return view('database.coupon.edit', ['id' => $id]);
    }
} 