<?php 

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Monitor;
use Illuminate\Http\Request;
use InvalidArgumentException;

class MonitorController extends Controller
{

    public function index(Request $request)
    {
    
        $name = $request->query('name');
        $is_active = $request->query('is_active');
        $type = $request->query('type');
        $order = $request->query('order', 'asc');
        $sort = $request->query('sort', 'id');
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);


        $query = Monitor::query();

        $query->when($request->user(), function ($query, $user) {
            return $query->where('user_id', $user->id);
        })
        ->when($name, function ($query, $name) {
            return $query->where('name', 'like', "%$name%");
        })
        ->when($is_active, function ($query, $is_active) {
            return $query->where('is_active', $is_active);
        })
        ->when($type, function ($query, $type) {
            return $query->where('type', $type);
        })
        ->orderBy($sort, $order);

        $paginatedMonitors = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Monitors retrieved successfully',
            'data' => $paginatedMonitors->items(),
            'meta' => [
                'current_page' => $paginatedMonitors->currentPage(),
                'last_page' => $paginatedMonitors->lastPage(),
                'per_page' => $paginatedMonitors->perPage(),
                'total' => $paginatedMonitors->total(),
            ],
        ], 200);

    }

    public function store(Request $request)
    {
     
        try {
        
            $data = [
                'user_id' => $request->user()->id,
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'is_active' => $request->input('is_active', true),
                'interval' => $request->input('interval', 60),
                'timeout' => $request->input('timeout', 30),
            ];

            $configData = $request->input('config', []);          

            $monitor = Monitor::createWithConfig($data, $configData);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create monitor: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create monitor: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Monitor created successfully',
            'data' => [
                'id' => $monitor->id,
                'name' => $monitor->name,
                'type' => $monitor->type,
                'is_active' => $monitor->is_active,
                'interval' => $monitor->interval,
                'timeout' => $monitor->timeout,
                'config' => $monitor->config,
            ],
        ], 201);

    }

    public function update(Request $request, String $id)
    {
        $monitor = Monitor::find($id);
        
        if (!$monitor) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found',
            ], 404);
        }

        if($request->user()->id !== $monitor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        
        try {

            $data = $request->only(['name', 'is_active', 'interval', 'timeout', 'type']);
            $configData = $request->input('config', []);

            $monitor = Monitor::updateWithConfig($monitor, $data, $configData);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update monitor: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update monitor: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Monitor updated successfully',
            'data' => [
                'id' => $monitor->id,
                'name' => $monitor->name,
                'type' => $monitor->type,
                'is_active' => $monitor->is_active,
                'interval' => $monitor->interval,
                'timeout' => $monitor->timeout,
                'config' => $monitor->config,
            ],
        ], 200);
    }


    public function show(String $id)
    {
        $monitor = Monitor::find($id);

        if (!$monitor) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found',
            ], 404);
        }

        if(request()->user()->id !== $monitor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Monitor retrieved successfully',
            'data' => $monitor,
        ], 200);
    }

    public function destroy(String $id)
    {
        $monitor = Monitor::find($id);

        if (!$monitor) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found',
            ], 404);
        }

        if(request()->user()->id !== $monitor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $monitor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Monitor deleted successfully',
        ], 200);
    }
    

}