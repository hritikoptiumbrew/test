<?php

namespace App\Http\Middleware;

use Closure;

use Log;
use DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class TokenEntrustAbility extends BaseMiddleware
{
    public function handle($request, Closure $next, $roles, $permissions, $validateAll = false)
    {

        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return Response::json(array('code' => 201, 'message' => 'Required field token is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));

        }

        try {
            $user = $this->auth->authenticate($token);
            //Log::info("Token", ["token :" => $token, "time" => date('H:m:s')]);

            if(!$user){
                return Response::json(array('code' => 404, 'message' => 'User not found.', 'cause' => '', 'data' => json_decode("{}")));
            }
            else
            {
                if($user->id == 1){
                    $is_exist = DB::table('user_session')->where('token', $token)->exists();
                    if (!$is_exist) {
                        return Response::json(array('code' => 400, 'message' => 'Your session is expired. Please login.', 'cause' => '', 'data' => json_decode("{}")));
                    }
                }
            }

        } catch (TokenInvalidException $e) {
            return Response::json(array('code' => $e->getStatusCode(), 'message' => 'Invalid token.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (TokenExpiredException $e) {
            try {
                $new_token = JWTAuth::refresh($token);
                //Log::info("Refreshed Token", ["token :" => $new_token, "time" => date('H:m:s')]);

                DB::beginTransaction();
                DB::update('UPDATE user_session
                                SET token = ?
                                WHERE token = ?', [$new_token, $token]);
                DB::commit();

                $result = [];
                $response = (new \App\Http\Controllers\UserController())->getAllRedisKeys("*");
                foreach ($response as $i => $item) {
                    if (strlen($item) <= 20 && !(str_contains($item, "getJsonData") || str_contains($item, "getAllCategory") || str_contains($request->getPathInfo(), "/api/logs/"))) {
                        $result[]['key'] = $item;
                    }
                }
                ($result) ? Log::info('DeleteCacheKey Middleware : ', ['ip' => request()->ip(), 'userAgent' => request()->userAgent(), 'result' => $result, 'api' => $request->getPathInfo()]) : "";

            } catch (TokenExpiredException $e) {
                //Log::debug('TokenExpiredException Can not be Refresh', ['status_code' => $e->getStatusCode()]);

                DB::beginTransaction();
                DB::delete('DELETE FROM user_session WHERE token = ?', [$token]);
                DB::commit();

                return Response::json(array('code' => $e->getStatusCode(), 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')));
            } catch (TokenBlacklistedException $e) {
                //Log::debug('The token has been blacklisted.', ['status_code' => $e->getStatusCode()]);

                DB::beginTransaction();
                DB::delete('DELETE FROM user_session WHERE token = ?', [$token]);
                DB::commit();

//                return Response::json(array('code' => 400, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
                return Response::json(array('code' => 400, 'message' => "Your session has been expired.", 'cause' => '', 'data' => json_decode("{}")));
            } catch (JWTException $e) {
                return Response::json(array('code' => $e->getStatusCode(), 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            }

            //10-Aug-2022 : Android handle the refresh token in the cause field. So we have to pass the refresh token in the cause field as well.
            return Response::json(array('code' => $e->getStatusCode(), 'message' => 'Token expired.', 'cause' => $new_token, 'data' => ['new_token' => $new_token]));
            //return Response::json(array('code' => $e->getStatusCode(), 'message' => 'Token expired.', 'cause' => '', 'data' => ['new_token' => $new_token]));

        } catch (JWTException $e) {
            return Response::json(array('code' => $e->getStatusCode(), 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        if (!$user) {
            //return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            return Response::json(array('code' => 404, 'message' => 'User not found.', 'cause' => '', 'data' => json_decode("{}")));
        }

        if (!$request->user()->ability(explode('|', $roles), explode('|', $permissions), array('validate_all' => $validateAll))) {
            return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
            //return $this->respond('tymon.jwt.invalid', 'token_invalid', 401, 'Unauthorized');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
