<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use App\Helpers\CustomHelper;

class ShortUrlResolutionController extends Controller
{
    public function resolve($shortCode)
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found.');
        }

        return redirect()->away($shortUrl->original_url);
    }
}
