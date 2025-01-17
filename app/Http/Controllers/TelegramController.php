<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function index()
    {
        return view('telegram.index');
    }

    public function store(Request $request)
    {
        $token = '7821313216:AAFMtKTd_RNfubpopDQq2WWkmLgwtNFQnis';
        $url = "https://api.telegram.org/bot$token";

        $data = $request->validate([
            'text' => 'nullable|string|max:255',
            'file' => 'nullable',
        ]);


        if ($request->has('text')) {
            Http::post("$url/sendMessage", [
                'chat_id' => '5122685168',
                'parse_mode' => 'HTML',
                'text' => "<i>" . ($data['text'] ?? 'Matn mavjud emas') . "</i>",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Qabul qilish✅', 'callback_data' => 'button_1'],
                            ['text' => 'Bekor qilish🚫', 'callback_data' => 'button_2']
                        ],
                    ],
                ]),
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
    
        return back()->with('success', 'Xabar yuborildi');
    }
}
