<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\AdminHelper;
use App\Helpers\CustomHelper;
use App\Models\ShortUrl;

class ShortUrlController extends Controller
{
    public function getIndex()
    {
        if (!AdminHelper::isView()) {
            return redirect()->route('dashboard')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        $urls = ShortUrl::with(['company', 'creator'])->applyVisibilityConstraints(auth()->user())->latest()->get();
        return view('admin.urls.index', compact('urls'));
    }

    public function getAdd()
    {
        if ((int) auth()->user()->role_id === 1) {
            return redirect()->route('urls.index')->with('error', 'SuperAdmin cannot create short URLs.');
        }

        if (!AdminHelper::isCreate()) {
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        return view('admin.urls.add');
    }

    public function postAddSave(Request $request)
    {
        if ((int) auth()->user()->role_id === 1) {
            if ($request->expectsJson()) {
                return CustomHelper::ErrorResponse('SuperAdmin cannot create short URLs.');
            }
            return redirect()->route('urls.index')->with('error', 'SuperAdmin cannot create short URLs.');
        }

        if (!AdminHelper::isCreate()) {
            if ($request->expectsJson()) {
                return CustomHelper::ErrorResponse('Sorry you do not have privilege to access this area !');
            }
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        $request->validate([
            'original_url' => 'required|url|max:2048'
        ]);

        $shortUrl = ShortUrl::create([
            'original_url' => $request->original_url,
            'short_code' => Str::random(8),
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->user()->id
        ]);

        if ($request->expectsJson()) {
            return CustomHelper::SuccessResponse('URL Shortened Successfully', CustomHelper::CapitalizeArray($shortUrl->toArray()));
        }

        return redirect()->route('urls.index')->with('success', 'URL Shortened Successfully');
    }

    public function getDetail($id)
    {
        if (!AdminHelper::isRead()) {
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        $urlData = ShortUrl::where('id', $id)->first();
        if (!$urlData) {
            return CustomHelper::ErrorResponse('Data Not found');
        }

        return CustomHelper::SuccessResponse('URL details fetched successfully', CustomHelper::CapitalizeArray($urlData->toArray()));
    }

    public function getEdit($id)
    {
        if (!AdminHelper::isUpdate()) {
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        $url = ShortUrl::find($id);
        if (!$url) {
            return redirect()->route('urls.index')->with('error', 'URL not found.');
        }

        return view('admin.urls.edit', compact('url'));
    }

    public function postUpdateSave($id, Request $request)
    {
        if (!AdminHelper::isUpdate()) {
            if ($request->expectsJson()) {
                return CustomHelper::ErrorResponse('Sorry you do not have privilege to access this area !');
            }
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        $url = ShortUrl::find($id);
        if (!$url) {
            if ($request->expectsJson()) {
                return CustomHelper::ErrorResponse('Data Not found');
            }
            return redirect()->route('urls.index')->with('error', 'Data Not found');
        }

        $url->update($request->only('original_url'));

        if ($request->expectsJson()) {
            return CustomHelper::SuccessResponse('URL updated successfully', CustomHelper::CapitalizeArray($url->toArray()));
        }

        return redirect()->route('urls.index')->with('success', 'URL updated successfully');
    }

    public function getDelete($id)
    {
        if (!AdminHelper::isDelete()) {
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        ShortUrl::destroy($id);
        return redirect()->route('urls.index')->with('success', 'Deleted successfully');
    }

    public function postActionSelected(Request $request)
    {
        if (!AdminHelper::isDelete()) {
            return redirect()->route('urls.index')->with('error', 'Sorry you do not have privilege to access this area !');
        }

        ShortUrl::whereIn('id', $request->ids)->delete();
        return redirect()->route('urls.index')->with('success', 'Bulk action completed');
    }
}
