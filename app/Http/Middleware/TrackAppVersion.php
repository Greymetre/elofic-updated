<?php

namespace App\Http\Middleware;

use App\Models\MobileUserLoginDetails;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Keeps mobile_user_login_details.app_version in sync with the app build the
 * user is actually running. The app sends X-App-Version on every API call;
 * previously the version was only stored at login, so users who updated the
 * app without logging out kept showing their old version.
 */
class TrackAppVersion
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $version = trim((string) $request->header('X-App-Version', ''));
            $user = $request->user();

            if ($version !== '' && strlen($version) <= 20 && $user instanceof User) {
                // Write at most once per user+version per day.
                $cacheKey = 'app_version_seen:' . $user->id . ':' . $version;
                if (!Cache::has($cacheKey)) {
                    $updates = ['app_version' => $version];
                    $deviceType = trim((string) $request->header('X-Device-Type', ''));
                    if ($deviceType !== '') {
                        $updates['device_type'] = $deviceType;
                    }

                    MobileUserLoginDetails::where('user_id', $user->id)
                        ->where('app', '2')
                        ->where(function ($q) use ($version) {
                            $q->whereNull('app_version')->orWhere('app_version', '!=', $version);
                        })
                        ->update($updates);

                    Cache::put($cacheKey, true, now()->addDay());
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }
}
