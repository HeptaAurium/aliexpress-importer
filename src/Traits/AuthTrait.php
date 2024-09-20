<?php

namespace Heptaaurium\AliexpressImporter\Traits;

use Heptaaurium\AliexpressImporter\Models\AliExpressImporterToken;

trait AuthTrait
{
    public function _verify_token($token)
    {

        $check = AliExpressImporterToken::where('token', $token)
            ->first();

        if (!$check || $check->expires_at < now()) {
            return response()->json([
                'status' => false,
                'message' => 'Token is invalid or expired'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Token is valid',
            'token' => $token
        ]);
    }
}
