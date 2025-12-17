<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProductSimple;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{

    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'products_count' => ProductSimple::count(),
            'tables' => $this->getTablesList()
        ];
        
        return view('database.index', compact('stats'));
    }

    public function users()
    {
        return view('database.users');
    }

    public function createUser()
    {
        return view('database.create-user');
    }

    public function products()
    {
        return view('database.products');
    }

    public function editUser($id)
    {
        return view('database.edit-user');
    }

    public function editProduct($id)
    {
        return view('database.edit-product');
    }

    public function createProduct()
    {
        return view('database.create-product');
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
} 