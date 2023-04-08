<?php

namespace Ezegyfa\LaravelHelperMethods;

use Illuminate\Support\Facades\Route;

class ServerCommandMethods
{
    public static function registerServerCommandRoutes() {
        Route::get('/git-pull', function () {
            $wrongPasswordCountFilePath = base_path('storage/wrongPasswordCount');
            if (!file_exists($wrongPasswordCountFilePath)) {
                $wrongPasswordCountFile = fopen($wrongPasswordCountFilePath, 'w');
                fwrite($wrongPasswordCountFile, 0);
                fclose($wrongPasswordCountFile);
            }
            $wrongPasswordCount = intval(file_get_contents($wrongPasswordCountFilePath));
            if ($wrongPasswordCount > 4) {
                $message = 'Too many wrong password';
            }
            else if (request()->get('password') == static::getPassword()) {
                file_put_contents($wrongPasswordCountFilePath, 0);
                //$message = file_get_contents('http://127.0.0.1:8222/dynamic_web/command.php?command=git_pull');
                $message = 'success';
            }
            else {
                ++$wrongPasswordCount;
                file_put_contents($wrongPasswordCountFilePath, $wrongPasswordCount);
                $message = 'Wrong password ' . (5 - $wrongPasswordCount) . ' try remaining.';
            }
            return response()->json([
                'message' => $message,
            ]);
        });
    }

    public static function getPassword() {
        $date = new \DateTimeImmutable();
        return (int)($date->getTimestamp() / 100) * 12345;
    }
}