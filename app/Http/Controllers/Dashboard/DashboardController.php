<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

  public function index()
{
    // Check if user has the ability to view dashboard
    $this->authorize('dashboard.view');
    
    $user = Auth::user();
    $title = 'Store';

    $topProducts = Product::withCount('orderItems')
        ->select('products.*', DB::raw('SUM(order_items.price * order_items.quantity) as total_sales'))
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->groupBy('products.id')
        ->orderByDesc('total_sales')
        ->take(5)
        ->get();

    return view('dashboard.index', [
        'user' => $user,
        'title' => $title,
        'topProducts' => $topProducts
    ]);
}
}
