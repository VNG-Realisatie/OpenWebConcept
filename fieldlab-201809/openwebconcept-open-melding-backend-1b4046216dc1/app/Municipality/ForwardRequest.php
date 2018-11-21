<?php

namespace App\Municipality;

use Intervention\Image\Image;
use Log;
use App\Http\Requests\StoreNotification;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class ForwardRequest
{

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Forward the request to the given municipality.
     *
     * @param                   $municipality
     * @param StoreNotification $request
     *
     * @return string
     * @throws \Exception
     */
    public function send($municipality, StoreNotification $request)
    {
        $endpoint = config('municipalities.'.Str::snake($municipality));

        if ($endpoint == null) {
            throw new \Exception('Municipality is not been configured.');
        }

        $file     = $request->file('picture');
//dd($file->getfile());
//        $img = (new Image)->make($file->getPath());
//         now you are able to resize the instance
        //$img->resize(320, 240);
        //dd($img);

        $filedata = $file ? [
            'file'     => $file->get(),
            'filename' => $file->getClientOriginalName(),
            'filetype' => $file->getMimeType()
        ] : [];

        $response = $this->client->post($endpoint, [
            'form_params' => array_merge(
                $request->only([ 'lat', 'lng', 'message', 'time' ]),
                $filedata
            )
        ]);

        return json_decode($response->getBody()->getContents());
    }

}