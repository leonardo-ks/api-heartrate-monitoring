<?php


namespace App\Actions;


class PushNotification
{
    public static function handle($topics, $title, $description)
    {
        $data = [
            "to" => $topics,
            "notification" =>
                [
                    "title" => $title,
                    "message" => $description,
                ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=AAAA7NpuTF0:APA91bGoZNzN0veBBz6e9dX8BSGlOrbzlsmyoNLVQ4SCm4m_bv7RYswZ38kzSWWi9VCtthYWIxWLaVHHRZmA41ypwt6YOX4AXx2OrKWzR5YZ3ELsy-RBOl4xRax0-80GqP0Yr66J8dPy',
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        curl_exec($ch);
    }
}
