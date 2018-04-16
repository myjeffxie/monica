<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Contact;
use App\Relationship;
use App\RelationshipType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Relationship\Relationship as RelationshipResource;

class ApiRelationshipController extends ApiController
{
    /**
     * Get the detail of a given relationship.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $relationship = Relationship::where('account_id', auth()->user()->account_id)
                            ->where('id', $id)
                            ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound();
        }

        return new RelationshipResource($relationship);
    }

    /**
     * Create a new relationship.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validParameters = $this->validateParameters($request);
        if ($validParameters !== true) {
            return $validParameters;
        }

        $relationshipType = RelationshipType::find($request->get('relationship_type_id'));

        $contact = Contact::findOrFail($request->get('contact_is'));
        $partner = Contact::find($request->get('of_contact'));

        try {
            $contact->setRelationship($partner, $relationshipType->id);
        } catch (QueryException $e) {
            return $this->respondNotTheRightParameters();
        }

        $relationship = $contact->getRelationshipNatureWith($partner);

        return new RelationshipResource($relationship);
    }

    /**
     * Validate the parameters.
     *
     * @param  Request $request
     * @return mixed
     */
    private function validateParameters(Request $request)
    {
        // Validates basic fields to create the entry
        $validator = Validator::make($request->all(), [
            'contact_is' => 'integer|required',
            'relationship_type_id' => 'required|integer',
            'of_contact' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return $this->setErrorCode(41)
                        ->respondWithError($validator->errors()->all());
        }

        if ($request->get('relationship_type_id')) {
            try {
                RelationshipType::where('account_id', auth()->user()->account_id)
                    ->where('id', $request->input('relationship_type_id'))
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return $this->respondNotFound();
            }
        }

        if ($request->get('contact_is')) {
            try {
                Contact::where('account_id', auth()->user()->account_id)
                    ->where('id', $request->input('contact_is'))
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return $this->respondNotFound();
            }
        }

        if ($request->get('of_contact')) {
            try {
                Contact::where('account_id', auth()->user()->account_id)
                    ->where('id', $request->input('of_contact'))
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return $this->respondNotFound();
            }
        }

        return true;
    }
}
