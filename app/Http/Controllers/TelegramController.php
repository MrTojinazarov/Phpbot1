<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function index()
    {
        $models = User::all();

        return view('telegram.index', ['models' => $models]);
    }

    public function store(Request $request)
    {
        $token = '7821313216:AAFMtKTd_RNfubpopDQq2WWkmLgwtNFQnis';
        $url = "https://api.telegram.org/bot$token";

        $data = $request->validate([
            'user' => 'nullable',
            'text' => 'nullable|string|max:255',
            'file' => 'nullable',
        ]);


        if ($request->has('text')) {
            Http::post("$url/sendMessage", [
                'chat_id' => '5122685168',
                'parse_mode' => 'HTML',
                'text' => "<i>" . ($data['text'] ?? 'Matn mavjud emas') . "</i>",
            ]);
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->extension();
        
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                Http::attach(
                    'photo', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
                )->post("$url/sendPhoto", [
                    'chat_id' => '5122685168',
                    'caption' => $data['text'] ?? null,
                ]);
            } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])) {
                Http::attach(
                    'video', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
                )->post("$url/sendVideo", [
                    'chat_id' => '5122685168',
                    'caption' => $data['text'] ?? null,
                ]);
            } else {
                Http::attach(
                    'document', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
                )->post("$url/sendDocument", [
                    'chat_id' => '5122685168',
                    'caption' => $data['text'] ?? null,
                ]);
            }
            }
            if ($request->has('user')) {
                $user = User::find($data['user']);
    
                if ($user) {
                    $userData = "<b>Foydalanuvchi ma'lumotlari:</b>\n";
                    $userData .= "Ismi: " . ($user->name ?? 'Noma\'lum') . "\n";
                    $userData .= "Email: " . ($user->email ?? 'Noma\'lum') . "\n";
    
                    Http::post("$url/sendMessage", [
                        'chat_id' => '5122685168',
                        'parse_mode' => 'HTML',
                        'text' => $userData,
                    ]);
                } else {
                    Http::post("$url/sendMessage", [
                        'chat_id' => '5122685168',
                        'parse_mode' => 'HTML',
                        'text' => "<i>Foydalanuvchi topilmadi</i>",
                    ]);
                }
            }
    
    
        return back()->with('success', 'Xabar yuborildi');
    }
}
