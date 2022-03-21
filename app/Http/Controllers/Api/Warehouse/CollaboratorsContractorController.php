<?php

namespace App\Http\Controllers\Api\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Collaborators;
use App\Models\Warehouse\Сontractor;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Media;

class CollaboratorsContractorController extends Controller {
    public function __construct() {
        $this->contractorId = null;
        $this->count = 10;
    }

    /**
     * Get Collaborator list|item
     */
    public function list(Request $request, $contractor_id = false, $id = false) {
        if(!$contractor_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contractor id not passed'
            ], 422);
        }

        $this->contractorId = $contractor_id;
        $contractorData = $this->getContractor();

        if(!$id) {
            $request->validate([
                'count' => 'integer',
                'page' => 'integer'
            ]);

            $count = $request->input('count') ?? $this->count;

            if(!$request->has('search') || !$request->input('search')) {
                $collaboratorsList = Collaborators::with('media')->paginate($count);
            } else {
                // ----------------
                // With search
                // ----------------
                $searcParams = json_decode($request->input('search'), true);

                if(count($searcParams)){
                    $listing = Collaborators::with('media');

                    foreach ($searcParams as $key => $value) {
                        if($key === 'status') {
                            $listing->where($key, $value);
                        } else {
                            $listing->where($key, 'LIKE', "%{$value}%");
                        }
                    }

                    $collaboratorsList = $listing->paginate($count);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Incorrect search parameters'
                    ], 422);
                }
            }

            if($collaboratorsList->count()) {
                $data['items'] = array();

                foreach ($collaboratorsList->items() as $collaborator) {
                    array_push($data['items'], $this->formatItem($collaborator));
                }

                $data['pagination'] = array(
                    "count" => $collaboratorsList->count(),
                    "total" => $collaboratorsList->total(),
                    "current_page" => $collaboratorsList->currentPage(),
                    "last_page" => $collaboratorsList->lastPage()
                );
            }
        } else {
            $collaborator = Сontractor::with('media')->find($id);
            $data = $collaborator ? $this->formatItem($collaborator) : null;
        }

        $data['contractor'] = $contractorData;

        return response()->json([
            'status' => 'Success',
            'data' => $data
        ], 200);
    }

    static function formatItem($item) {
        $itemArray = array(
            'id' => $item['id'],
            'full_name' => trim($item['first_name'] . ' ' . $item['last_name']),
            'first_name' => $item['first_name'],
            'last_name' => $item['last_name'],
            'birthday' => strtotime($item['birthday']),
            'address' => $item['address'],
            'email' => $item['email'],
            'phone' => $item['phone'],
            'viber' => $item['viber'],
            'telegram' => $item['telegram'],
            'skype' => $item['skype'],
            'role' => $item['role'],
            'description' => $item['description'],
            'photo' => null,
            'status' => $item['status'],
            'created_at' => strtotime($item['created_at']),
            'updated_at' => strtotime($item['updated_at'])
        );

        if($item['media']) {
            $itemArray['photo'] = array(
               'id' => $item['media']['id'],
               'link' => $item['media']['link']
            );
        }

        return $itemArray;
    }

    public function getContractor() {
        if($this->contractorId) {
            $data = Сontractor::select('id', 'name', 'website', 'logo_id')
                ->with('media')
                ->find($this->contractorId);

            if($data) {
                $contractor = array(
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'website' => $data['website'],
                    'logo' => null
                );

                if($data['media']) {
                    $contractor['logo'] = array(
                       'id' => $data['media']['id'],
                       'link' => $data['media']['link']
                    );
                }

                return $contractor;
            } else return null;
        } else return false;
    }

    /**
     * Create Collaborator
     *
     * @param  [string] first_name
     * @param  [string] last_name
     * @param  [string] birthday
     * @param  [string] address
     * @param  [string] email
     * @param  [string] phone
     * @param  [string] viber
     * @param  [string] telegram
     * @param  [string] skype
     * @param  [string] role
     * @param  [string] description
     * @param  [number] photo_id
     * @param  [number] contractor_id
     * @param  [number] status
     * @return [object] status
     */
    public function add(Request $request, $contractor_id = false) {
        if(!$contractor_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contractor id not passed'
            ], 422);
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'string|max:100',
            'birthday' => 'date',
            'address' => 'string',
            'email' => 'email',
            'phone' => 'string|max:13',
            'viber' => 'string|max:20',
            'telegram' => 'string|max:50',
            'skype' => 'string|max:50',
            'role' => 'required|string|max:100',
            'description' => 'string',
            'photo_id' => 'exists:media,id',
            'contractor_id' => $contractor_id,
            'status' => 'in:0,1'
        ]);

        $collaborator = new Collaborators([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name ?? '',
            'birthday' => $request->birthday ?? null,
            'address' => $request->address ?? '',
            'email' => $request->email ?? '',
            'phone' => $request->phone ?? '',
            'viber' => $request->viber ?? '',
            'telegram' => $request->telegram ?? '',
            'skype' => $request->skype ?? '',
            'role' => $request->role ?? '',
            'description' => $request->description ?? '',
            'photo_id' => $request->photo_id ?? null,
            'contractor_id' => $contractor_id,
            'status' => $request->status ?? 1
        ]);
        $collaborator->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Successfully created collaborator!'
        ], 200);
    }

    /**
     * Edit Collaborator
     *
     * @param  [string] first_name
     * @param  [string] last_name
     * @param  [string] birthday
     * @param  [string] address
     * @param  [string] email
     * @param  [string] phone
     * @param  [string] viber
     * @param  [string] telegram
     * @param  [string] skype
     * @param  [string] role
     * @param  [string] description
     * @param  [number] photo_id
     * @param  [number] status
     * @return [object] status
     */
    public function update(Request $request, $contractor_id = false, $id = false) {
        if(!$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Collaborator id not passed'
            ], 422);
        }

        $collaborator = Collaborators::find($id);

        if(!$collaborator) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID is not found in database'
            ], 422);
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'string|max:100',
            'birthday' => 'date',
            'address' => 'string',
            'email' => 'email',
            'phone' => 'string|max:13',
            'viber' => 'string|max:20',
            'telegram' => 'string|max:50',
            'skype' => 'string|max:50',
            'role' => 'required|string|max:100',
            'description' => 'string',
            'photo_id' => 'exists:media,id',
            'contractor_id' => $contractor_id,
            'status' => 'in:0,1'
        ]);

        $collaborator->first_name = $request->first_name;
        $collaborator->last_name = $request->last_name ?? '';
        $collaborator->birthday = $request->birthday ?? null;
        $collaborator->address = $request->address ?? '';
        $collaborator->email = $request->email ?? '';
        $collaborator->phone = $request->phone ?? '';
        $collaborator->viber = $request->viber ?? '';
        $collaborator->telegram = $request->telegram ?? '';
        $collaborator->skype = $request->skype ?? '';
        $collaborator->role = $request->role ?? '';
        $collaborator->description = $request->description ?? '';
        $collaborator->photo_id = $request->photo_id ?? null;
        $collaborator->status = $request->status ?? 1;
        $collaborator->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data updated',
            'data' => $collaborator
        ], 200);
    }

    /**
     * Delete Collaborator
     *
     * @param  [string] accept
     */
    public function remove(Request $request, $contractor_id = false, $id = false) {
        if(!$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Collaborator id not passed'
            ], 422);
        }

        $collaborator = Collaborators::find($id);

        if(!$collaborator) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID is not found in database'
            ], 422);
        }

        $request->validate([
            'accept' => 'accepted'
        ]);

        if($collaborator['photo_id']) {
            $logo = Media::find($collaborator['photo_id']);
            if($logo) $logo->delete();
        }

        $collaborator->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Entry removed'
        ], 200);
    }
}
