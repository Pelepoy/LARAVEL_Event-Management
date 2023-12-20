<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'event'];

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $attendees = $this->loadRelationships(
            $event->attendees()->latest()
        );

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $this->loadRelationships(
            $event->attendees()->create([
                'user_id' => 1,
            ])
        );
        return new AttendeeResource($attendee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource(
            $this->loadRelationships($attendee)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        $this->authorize('delete-attendee', [$event, $attendee]); // gate
        $attendee->delete();

        return response()->json([
            'message' => 'Attendee deleted successfully.'
        ], 200);
        // return response(status: 204);
    }
}
