public function index()
{
// return EventResource::collection(Event::all());
return EventResource::collection(
Event::with('user', 'attendees')->paginate()
);
}

<!-- BEFORE TRAITS (CANLOADRELATIONSHIPS) -->

public function index()
{
$query = Event::query();
$relations = ['user', 'attendees', 'attendees.user'];

foreach ($relations as $relation) {
$query->when(
$this->shouldIncludeRelation($relation),
fn ($q) => $q->with($relation)
);
}

// return EventResource::collection(Event::all());
return EventResource::collection(
$query->latest()->paginate()
);
}

/**
* Store a newly created resource in storage.
*/
public function store(Request $request)
{
$event = Event::create([
...$request->validate([
'name' => 'required|string|max:255',
'description' => 'nullable|string',
'start_time' => 'required|date',
'end_time' => 'required|date|after:start_time'
]),
'user_id' => 1
]);

return new EventResource($event);
}

/**
* Display the specified resource.
*/
public function show(Event $event)
{
$event->load('user', 'attendees');
return new EventResource($event);
}

/**
* Update the specified resource in storage.
*/
public function update(Request $request, Event $event)
{
$event->update(
$request->validate([
'name' => 'sometimes|string|max:255',
'description' => 'nullable|string',
'start_time' => 'sometimes|date',
'end_time' => 'sometimes|date|after:start_time'
])
);

return new EventResource($event);
}
/**
* Remove the specified resource from storage.
*/
public function destroy(Event $event)
{
$event->delete();

// return response(status: 204);
return response()->json([
'message' => 'Event deleted successfully'
], 200);
}
}


<!-- ATTENDEE CONTROLLER BEFORE (CANLOADRELATIONSHIP) -->
{

/**
* Display a listing of the resource.
*/
public function index(Event $event)
{
$attendees = $event->attendees()->latest()

return AttendeeResource::collection(
$attendees->paginate()
);
}

/**
* Store a newly created resource in storage.
*/
public function store(Request $request, Event $event)
{
$attendee = $event->attendees()->create([
'user_id' => 1,
]);
return new AttendeeResource($attendee);
}

/**
* Display the specified resource.
*/
public function show(Event $event, Attendee $attendee)
{
return new AttendeeResource($attendee);
}

/**