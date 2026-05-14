<?php
// api/line/flex_message.php

/**
 * สร้าง Flex Message สำหรับแจ้งเตือนเคสใหม่
 */

function sendFlexMessage($channel_token, $user_id, $flex_content) {
    $data = [
        'to' => $user_id,
        'messages' => [$flex_content]
    ];

    $ch = curl_init('https://api.line.me/v2/bot/message/push');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $channel_token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code === 200;
}

function buildNewCaseFlex($case) {
    return [
        "type" => "flex",
        "altText" => "📋 มีเคสใหม่! ลูกค้า: {$case['customer_name']}",
        "contents" => [
            "type" => "bubble",
            "hero" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    [
                        "type" => "text",
                        "text" => "📋 มีเคสใหม่!",
                        "size" => "xl",
                        "weight" => "bold",
                        "color" => "#ffffff"
                    ]
                ],
                "backgroundColor" => "#667eea",
                "paddingAll" => "20px"
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    ["type" => "text", "text" => $case['customer_name'], "size" => "lg", "weight" => "bold"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "เบอร์โทร", "color" => "#aaaaaa", "flex" => 2],
                        ["type" => "text", "text" => $case['phone'], "flex" => 5, "align" => "end"]
                    ], "margin" => "sm"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "โครงการ", "color" => "#aaaaaa", "flex" => 2],
                        ["type" => "text", "text" => $case['project_name'] ?? '-', "flex" => 5, "align" => "end"]
                    ], "margin" => "sm"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "ราคา", "color" => "#aaaaaa", "flex" => 2],
                        ["type" => "text", "text" => number_format($case['price'] ?? 0) . " บาท", "flex" => 5, "align" => "end", "color" => "#e74a3b"]
                    ], "margin" => "sm"]
                ],
                "paddingAll" => "20px"
            ],
            "footer" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    [
                        "type" => "button",
                        "action" => [
                            "type" => "uri",
                            "label" => "🔍 ดูรายละเอียด",
                            "uri" => "https://your-domain.com/views/case_detail.php?id={$case['id']}"
                        ],
                        "style" => "primary",
                        "color" => "#667eea"
                    ]
                ],
                "paddingAll" => "15px"
            ]
        ]
    ];
}

/**
 * สร้าง Flex Message สำหรับแจ้งเตือนผลอนุมัติ
 */
function buildApprovalFlex($data) {
    return [
        "type" => "flex",
        "altText" => "💰 ผลอนุมัติ Case #{$data['case_id']}",
        "contents" => [
            "type" => "bubble",
            "hero" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    ["type" => "text", "text" => "💰 ผลอนุมัติ", "size" => "xl", "weight" => "bold", "color" => "#ffffff"]
                ],
                "backgroundColor" => "#1cc88a",
                "paddingAll" => "20px"
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    ["type" => "text", "text" => "Case #{$data['case_id']}", "size" => "sm", "color" => "#aaaaaa"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "วงเงินห้อง", "flex" => 3],
                        ["type" => "text", "text" => number_format($data['room_amount'] ?? 0) . " บาท", "flex" => 4, "align" => "end"]
                    ], "margin" => "md"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "วงเงินประกัน", "flex" => 3],
                        ["type" => "text", "text" => number_format($data['insurance_amount'] ?? 0) . " บาท", "flex" => 4, "align" => "end"]
                    ], "margin" => "sm"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "วงเงินเฟอร์นิเจอร์", "flex" => 3],
                        ["type" => "text", "text" => number_format($data['furniture_amount'] ?? 0) . " บาท", "flex" => 4, "align" => "end"]
                    ], "margin" => "sm"],
                    ["type" => "separator", "margin" => "sm"],
                    ["type" => "box", "layout" => "baseline", "contents" => [
                        ["type" => "text", "text" => "รวม", "flex" => 3, "weight" => "bold", "color" => "#1cc88a"],
                        ["type" => "text", "text" => number_format($data['total_amount'] ?? 0) . " บาท", "flex" => 4, "align" => "end", "weight" => "bold", "color" => "#1cc88a"]
                    ], "margin" => "sm"]
                ],
                "paddingAll" => "20px"
            ]
        ]
    ];
}
?>