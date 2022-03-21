<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Magazine;
use App\Models\Media;

class MagazineController extends Controller {

    /**
     * Get magazine by id/list
     */
    public function list(Request $request, $id = false) {
        if(!$id) {
            $request->validate([
                'count' => 'integer',
                'page' => 'integer'
            ]);

            $count = $request->input('count') ?? 10;
            $data = array(
                'items' => null,
                'pagination' => null
            );

            if(!$request->has('search') || !$request->input('search')) {
                $shopsList = Magazine::with('media')->paginate($count);
            } else {
                // ----------------
                // With search
                // ----------------
                $searcParams = json_decode($request->input('search'), true);

                if(count($searcParams)){
                    $listing = Magazine::with('media');

                    foreach ($searcParams as $key => $value) {
                        if($key === 'status') {
                            $listing->where($key, $value);
                        } else {
                            $listing->where($key, 'LIKE', "%{$value}%");
                        }
                    }

                    $shopsList = $listing->paginate($count);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Incorrect search parameters'
                    ], 422);
                }
            }

            if($shopsList->count()) {
                $data['items'] = array();

                foreach ($shopsList as $shop) {
                    array_push($data['items'], $this->formatShop($shop));
                }

                $data['pagination'] = array(
                    "count" => $shopsList->count(),
                    "total" => $shopsList->total(),
                    "current_page" => $shopsList->currentPage(),
                    "last_page" => $shopsList->lastPage()
                );
            }
        } else {
            $shop = Magazine::with('media')->find($id);
            $data = $shop ? $this->formatShop($shop) : null;
        }

        return response()->json([
            'status' => 'Success',
            'data' => $data
        ], 200);
    }

    static function formatShop($shop) {
        $shopArray = array(
            'id' => $shop['id'],
            'name' => $shop['name'],
            'logo' => null,
            'url' => $shop['url'],
            'key' => $shop['key'],
            'status' => $shop['status']
        );

        if($shop['media']) {
            $shopArray['logo'] = array(
               'id' => $shop['media']['id'],
               'link' => $shop['media']['link']
            );
        }

        return $shopArray;
    }

    /**
     * Create magazine
     *
     * @param  [string] name
     * @param  [number] logo_id
     * @param  [string] description
     * @return [number] status
     */
    public function add(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'logo_id' => 'exists:media,id',
            'url' => 'required|url|max:255|unique:magazines',
            'key' => 'required|string|max:50|unique:magazines',
            'status' => 'in:0,1'
        ]);

        $shop = new Magazine([
            'name' => $request->name,
            'logo_id' => $request->logo_id,
            'url' => $request->url,
            'key' => $request->key,
            'status' => $request->status
        ]);

        $shop->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Successfully created shop!'
        ], 200);
    }

    /**
     * Edit magazine
     *
     * @param  [string] name
     * @param  [number] logo_id
     * @return [number] status
     */
    public function update(Request $request, $id) {
        $shop = Magazine::find($id);

        if(!$shop) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID is not found in database'
            ], 422);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'logo_id' => 'exists:media,id',
            'status' => 'in:0,1'
        ]);

        $shop->name = $request->name;
        $shop->logo_id = $request->logo_id;
        $shop->status = $request->status;
        $shop->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data updated',
            'data' => $shop
        ], 200);
    }

    /**
     * Delete magazine
     *
     * @param  [string] accept
     */
    public function remove(Request $request, $id) {
        $shop = Magazine::find($id);

        if(!$shop) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID is not found in database'
            ], 422);
        }

        $request->validate([
            'accept' => 'accepted'
        ]);

        if($shop['logo_id']) {
            $logo = Media::find($shop['logo_id']);
            if($logo) $logo->delete();
        }

        $shop->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Entry removed'
        ], 200);
    }
}
