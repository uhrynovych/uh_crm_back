<?php

namespace App\Http\Controllers\Api\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Сontractor;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Media;

class СontractorController extends Controller {
    public function __construct() {
        $this->count = 10;
    }

    /**
     * Get contractor list|item
     */
    public function list(Request $request, $id = false) {
        if(!$id) {
            $request->validate([
                'count' => 'integer',
                'page' => 'integer'
            ]);

            $count = $request->input('count') ?? $this->count;
            $data = array(
                'items' => null,
                'pagination' => null
            );

            if(!$request->has('search') || !$request->input('search')) {
                $contractorList = Сontractor::with('media', 'collaborators')->paginate($count);
            } else {
                // ----------------
                // With search
                // ----------------
                $searcParams = json_decode($request->input('search'), true);

                if(count($searcParams)){
                    $listing = Сontractor::with('media', 'collaborators');

                    foreach ($searcParams as $key => $value) {
                        if($key === 'status') {
                            $listing->where($key, $value);
                        } else {
                            $listing->where($key, 'LIKE', "%{$value}%");
                        }
                    }

                    $contractorList = $listing->paginate($count);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Incorrect search parameters'
                    ], 422);
                }
            }

            if($contractorList->count()) {
                $data['items'] = array();

                foreach ($contractorList->items() as $contractor) {
                    array_push($data['items'], $this->formatItem($contractor));
                }

                $data['pagination'] = array(
                    "count" => $contractorList->count(),
                    "total" => $contractorList->total(),
                    "current_page" => $contractorList->currentPage(),
                    "last_page" => $contractorList->lastPage()
                );
            }
        } else {
            $contractor = Сontractor::with('media')->find($id);
            $data = $contractor ? $this->formatItem($contractor) : null;
        }

        return response()->json([
            'status' => 'Success',
            'data' => $data
        ], 200);
    }

    static function formatItem($item) {
        $itemArray = array(
            'id' => $item['id'],
            'name' => $item['name'],
            'logo' => null,
            'description' => $item['description'],
            'website' => $item['website'],
            'address' => $item['address'],
            'status' => $item['status'],
            'created_at' => strtotime($item['created_at']),
            'updated_at' => strtotime($item['updated_at'])
        );

        if($item['media']) {
            $itemArray['logo'] = array(
               'id' => $item['media']['id'],
               'link' => $item['media']['link']
            );
        }

        if(count($item['collaborators'])) {
            $collaborators = array();

            foreach ($item['collaborators'] as $collaborator) {
                array_push($collaborators, array(
                   'id' => $collaborator['id'],
                   'name' => trim($collaborator['first_name'] . ' ' . $collaborator['last_name']),
                   'role' => $collaborator['role'],
                   'status' => $collaborator['status'],
                   'phone' => $collaborator['phone'],
                   'viber' => $collaborator['viber'],
                   'email' => $collaborator['email'],
                   'telegram' => $collaborator['telegram'],
                   'skype' => $collaborator['skype'],
                   'description' => $collaborator['description']
                ));
            }
            $itemArray['collaborators'] = $collaborators;
        }

        return $itemArray;
    }

    /**
     * Create contractor
     *
     * @param  [string] name
     * @param  [string] description
     * @param  [string] address
     * @param  [string] website
     * @param  [number] logo_id
     * @return [number] status
     */
    public function add(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'string',
            'address' => 'string',
            'website' => 'url|max:255|unique:contractors',
            'logo_id' => 'exists:media,id',
            'status' => 'in:0,1'
        ]);

        $contractor = new Сontractor([
           'name' => $request->name,
           'description' => $request->description ?? '',
           'address' => $request->address ?? '',
           'website' => $request->website ?? '',
           'logo_id' => $request->logo_id,
           'status' => $request->status ?? 1
        ]);
        $contractor->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Successfully created contractor!'
        ], 200);
    }

    /**
     * Edit contractor
     *
     * @param  [string] name
     * @param  [number] logo_id
     * @param  [number] logo_id
     * @param  [number] status
     * @return [object] status
     */
    public function update(Request $request, $id) {
        $contractor = Сontractor::find($id);

        if(!$contractor) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID is not found in database'
            ], 422);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'string',
            'address' => 'string',
            'website' => [
                'url',
                'max:255',
                Rule::unique('contractors')->ignore($id),
            ],
            'logo_id' => 'exists:media,id',
            'status' => 'in:0,1'
        ]);

        $contractor->name = $request->name;
        $contractor->description = $request->description ?? '';
        $contractor->address = $request->address ?? '';
        $contractor->website = $request->website ?? '';
        $contractor->logo_id = $request->logo_id ?? null;
        $contractor->status = $request->status ?? 1;
        $contractor->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data updated',
            'data' => $contractor
        ], 200);
    }

    /**
     * Delete contractor
     *
     * @param  [string] accept
     */
    public function remove(Request $request, $id) {
        $contractor = Сontractor::find($id);

        if(!$contractor) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID is not found in database'
            ], 422);
        }

        $request->validate([
            'accept' => 'accepted'
        ]);

        if($contractor['logo_id']) {
            $logo = Media::find($contractor['logo_id']);
            if($logo) $logo->delete();
        }

        $contractor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Entry removed'
        ], 200);
    }
}
