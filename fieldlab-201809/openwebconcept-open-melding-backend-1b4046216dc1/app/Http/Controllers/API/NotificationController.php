<?php

namespace App\Http\Controllers\API;

use App\Municipality\ForwardRequest;
use Log;
use App\Municipality;
use App\Notification;
use Geocoder\Query\ReverseQuery;
use Geocoder\StatefulGeocoder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotification;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    /**
     * @var StatefulGeocoder
     */
    private $geocoder;

    /**
     * @var ForwardRequest
     */
    private $forward;

    public function __construct(StatefulGeocoder $geocoder, ForwardRequest $forward)
    {
        $this->geocoder = $geocoder;
        $this->forward  = $forward;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        return response()->json(Notification::with('municipality')->get());
    }

    /**
     * Handle incoming notifications.
     *
     * @param StoreNotification $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreNotification $request)
    {
        //$query    = ReverseQuery::fromCoordinates($request->get('lat'), $request->get('lng'));
        //$response = $this->geocoder->reverseQuery($query);

        //Log::debug('response', (array) $response);

        //$location = $response->first();

        //$municipality = Municipality::firstOrCreate([
        //     The municipality is the second admin level (admin level being the province itself).
        //'name' => $location->getAdminLevels()->get(2)->getName()
        //]);
        //
        //$notification = Notification::create($request->all());
        //$municipality->notifications()->save($notification);

        //return response()->json([
        //    'notification' => $notification,
        //    'municipality' => $municipality
        //]);
        try {
            $forward = $this->forward->send('haarlem', $request);
        } catch (\Exception $e) {
            Log::debug('forward_failed', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Kon de notificatie niet doorsturen naar de desbetreffende gemeente.'
            ], 400);
        }

        return response()->json([
            'id' => $forward->id
        ]);
    }

}