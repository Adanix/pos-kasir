<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PrintLog;
use App\Services\Simulators\ThermalPrinterSimulatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosReceiptController extends Controller
{
    public function __construct(
        protected ThermalPrinterSimulatorService $printerSimulator
    ) {}

    public function show(Request $request, Order $order): View
    {
        $paperWidth = $request->input('width', '58');
        if (!in_array($paperWidth, ['58', '80'])) {
            $paperWidth = '58';
        }

        $order->load(['items.product', 'cashier', 'posShift']);

        // Record print log and increment order print count
        $order->increment('print_count');

        PrintLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'paper_size' => $paperWidth . 'mm',
            'printer_mode' => 'browser_print',
            'status' => 'success',
            'raw_payload' => "HTML preview thermal {$paperWidth}mm rendered",
            'printed_at' => now(),
        ]);

        return view('pos.receipt', compact('order', 'paperWidth'));
    }

    public function printBridge(Request $request, Order $order): JsonResponse
    {
        $paperSize = $request->input('paper_size', '58mm');
        $simulateError = $request->boolean('simulate_error', false);

        $order->load(['items.product', 'cashier']);
        $result = $this->printerSimulator->generateEscPosBridge($order, $paperSize, Auth::user(), $simulateError);

        return response()->json($result);
    }
}
