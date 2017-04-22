<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ImageController extends Controller
{
    public function display(Request $request, $filename)
    {
        try {

            $ext = $request->attributes->get('ext');

            if ($request->attributes->has('width') || $request->attributes->has('height')) {
                $width = (int) $request->attributes->get('width') ?: 1;
                $height = (int) $request->attributes->get('height') ?: 1;

                $size = $width * $height;

                dump(compact('size', 'ext'));
            }

            dump($filename);
            return;
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
                'trace' => $e->getTrace(),
            ]);

            return response()
                ->redirectToRoute('index');
        }
    }

    public function view()
    {
        try {
            return view('view');
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
                'trace' => $e->getTrace(),
            ]);
        }
    }

    public function uploadFile(Request $request)
    {
        try {
            $files = $request->file();

            $rules = [
                'image' => 'required|image|mimetypes:image/png,image/jpeg,image/webp',
            ];

            $messages = [
                'image.required' => '沒有提供任何資料',
                'image.image' => '無效的格式',
                'image.mimetypes' => '無效的格式',
            ];

            $validator = \Validator::make($files, $rules, $messages);

            if ($validator->fails()) {
                return back()
                    ->withInput()
                    ->withErrors(['message' => '無效的格式']);
            }

            $file = $files['image'];

            list($width, $height) = getimagesize($file);

            try {
                \DB::beginTransaction();

                $image = Image::create([

                ]);

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();

                return back()
                    ->withInput()
                    ->withErrors(['message' => '無法存檔']);
            }


            return;
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
                'trace' => $e->getTrace(),
            ]);
        }
    }
}
