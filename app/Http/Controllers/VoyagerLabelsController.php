<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VoyagerLabelsController extends Controller
{
    public const INDEX_PATH = '/labels';

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if (Session::has('labels_status')) {
            Session::flash('message', Session::get('labels_status'));
            Session::forget('labels_status');
        }

        $locales = json_decode(file_get_contents(storage_path() . "/labels/labels.json"), true);


        return view('vendor.voyager.labels.index', ['locales' => $locales]);
    }

    /**
     * @param Request $request
     * Request is coming as array in 2 elements:
     * key->stands for the corresponding key element in json
     * value->stands for the corresponding value in json
     * @example
     * first level always contains 'first_level' keyword as the inner key of the array
     * @example   First level =>
     *          "some_first_level_locale" => array:1 [▼
     *              "first_level" => "some_locale_value"
     *            ]
     *          "some_second_first_level_locale" => array:1 [▼
     *               "first_level" => "some_locale_value"
     *          ]
     *
     * Second level contains the actual key which is in  the label
     * @example meta_title => 'Super good meta title'
     * Second Level
     * "METAS" => array:2 [▼
     * "meta_title" => "meta"
     * "meta_keywords" => "meta"
     * ]
     * We loop through them and if they are changes we replace them
     */
    public function editLabel(Request $request)
    {
        $locales = $this->forceNoSpaceKeys(json_decode(file_get_contents(storage_path() . '/labels/labels.json'), true));

        $request->request->remove('submit');
        $request->request->remove('_token');

        $previous_url = url()->previous();
        $correct_url = url('') . self::INDEX_PATH;

        if ($previous_url !== $correct_url)
            return response()->view('partials/404_page', [], 403);

        $changes = $request->all();

        foreach ($changes as $first_level => $request_change) {

            if (isset($locales[$first_level]) || array_key_exists($first_level, $locales)) {
                //WE check if it has flag first level , if does not , its second level
                if (!isset($request_change['first_level'])) {
                    //we cycle the second level and check if key from request matches the ones from labels file
                    foreach ($request_change as $second_level => $item) {

                        if (isset($locales[$first_level][$second_level])) {
                            $locales[$first_level][$second_level] = $item ?? "";
                        }
                    }
                } else {
                    //this is for first level labels
                    if (isset($locales[$first_level])) {
                        $locales[$first_level] = $request_change['first_level'] ?? "";
                    }
                }

            }
        }

        $newJsonLocales = json_encode($locales, JSON_PRETTY_PRINT);

        try {
            file_put_contents(storage_path() . '/labels/labels.json', stripslashes($newJsonLocales));
            Session::put('labels_status', 'Labels saved successfully!');

            return redirect()->route('voyager_labels');

        } catch (\Throwable $e) {
            Session::put('labels_status', 'Something went wrong, check log...');
            Log::error($e->getMessage());

        }

    }

    /**
     * @param $labels
     * @return array
     * If key has empty spaces transform it
     */
    private function forceNoSpaceKeys($labels)
    {
        $new_locales = [];

        array_walk($labels, function ($second_level, $first_level) use (&$new_locales) {
            if (strpbrk($first_level, ' ') !== false) {
                $new_locales[str_replace(" ", "_", $first_level)] = $second_level;
            } else {
                $new_locales[$first_level] = $second_level;
            }
        });

        return $new_locales;

    }

}
