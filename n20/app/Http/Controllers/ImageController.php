<?php

namespace App\Http\Controllers;

use App\Image;
use App\ImageCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Imagine\Imagick\Imagine;

class ImageController extends Controller
{
    protected $acceptExt = [
        'png',
        'jpg',
        'webp',
    ];

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

            $fv = pathinfo($filename);
            $name = $fv['filename'];

            /**
             * test start
             */
            $image = Image::where('image_id', $name)->first();

//            header('Content-Type: image/webp');
//            echo $this->convertImage($image);

            return \ImageConvert::make($this->convertImage($image)->response());
//            return response()
//                ->header('Content-Type', 'image/webp');

            /**
             * test end
             */
            return;
//            $ext = $fv['extension'];

//            $file = ImageCache::where('image_id', $name)
//                ->where('extension', $ext)
//                ->firstOrFail();

//            dump($file->toArray());

            return response()
                ->redirectTo($file->url);
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

            $ori_filename = $file->getClientOriginalName();

            $ext = $file->guessExtension();
            $ext = ('jpeg' === $ext) ? 'jpg' : $ext;



            try {
                $storage = 'public';
                $image_id = md5(time());
                $filename = "{$image_id}.{$ext}";
                $path = implode('/', str_split(substr(strrev($image_id), -6), 2));

                \Storage::disk($storage)
                    ->putFileAs($path, $file, $filename);

                $url = \Storage::disk($storage)->url($path . '/' . $filename);

                \DB::beginTransaction();

                $data = [
                    'image_id' => $image_id,
                    'storage' => $storage,
                    'image_name' => $ori_filename,
                    'ident' => "{$path}/{$filename}",
                    'url' => $url,
                    'width' => $width,
                    'height' => $height,
                    'watermark' => 'false',
                    'last_modified' => (string) time(),
                ];

                $image = Image::create($data);

                ImageCache::create([
                    'image_id' => $image_id,
                    'width' => $width,
                    'height' => $height,
                    'extension' => $ext,
                    'filename' => $path . '/' . $filename,
                    'url' => $url,
                ]);

                \DB::commit();

                return response()
                    ->redirectToRoute('viewView')
                    ->withErrors(['message' => 'completed']);
            } catch (\Exception $e) {
                \DB::rollBack();

                \Log::error($e->getMessage(), [
                    'method' => __METHOD__,
                ]);

                return;
                return back()
                    ->withInput()
                    ->withErrors(['message' => '無法存檔']);
            }


            return;
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
//                'trace' => $e->getTrace(),
            ]);
        }
    }

    private function convertImage(Image $image, $extension = '', $storage = 'public', $dimension = [])
    {
        try {
            $file = \Storage::disk($image->storage)
                ->get($image->ident);

            $newImageId = md5(time());
            $newPath = implode('/', str_split(substr(strrev($newImageId), -6), 2));
            $newFilename = "{$newImageId}.{$extension}";

            $filename = "{$image->image_id}.{$extension}";

            switch ($extension) {
                case 'jpg':
                case 'png':
                    $img = new Imagine();
                    $img->load($file)
                        ->show($extension);

                    \Storage::disk($storage)
                        ->putFileAs($newPath, $img, $newFilename);

                    $url = \Storage::disk($storage)
                        ->url("{$newPath}/${newFilename}");

                    $data = [
                        'image_id' => $image->image_id,
                        'width' => $image->width,
                        'height' => $image-height,
                        'filename' => "{$newPath}/{$newFilename}",
                        'url' => $url,
                    ];
                    break;
                case 'webp':
                    $img = new \Imagick();
                    $img->readImageBlob($file);
                    $img->setImageFormat('webp');

                    \Storage::disk($storage)
                        ->putFileAs($newPath, $img, $newFilename);

                    $url = \Storage::disk($storage)
                        ->url("{$newPath}/${newFilename}");

                    $data = [
                        'image_id' => $image->image_id,
                        'width' => $image->width,
                        'height' => $image-height,
                        'filename' => "{$newPath}/{$newFilename}",
                        'url' => $url,
                    ];
                    break;
                default:
                    return false;
            }

            try {
                \DB::beginTransaction();

                ImageCache::create($data);

                \DB::commit();

                return compact('storage', 'newFilename');
            } catch (\Exception $e) {
                \DB::rollBack();

                throw new \Exception('save converted file to DB failed');
            }


        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
            ]);
        }
    }

    private function putCache()
    {
        try {

        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
            ]);
        }
    }
}
