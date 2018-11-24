<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/5/18
 * Time: 22:43
 */
return [
    'WebService'             => [
        'url'           => 'xxx',
        'method'        => 'xxx',
        'prefix'        => 'xxx',
        'private_token' => 'xxx',
        'shortcode'     => 'xxx'
    ],
    'SMPPVersion1'           => [
        'url'       => 'xxx',
        'method'    => 'xxx',
        'username'  => 'xxx',
        'password'  => 'xxx',
        'shortcode' => 'xxx'
    ],
    'SMPPVersion2'           => [
        'url'         => 'xxx',
        'method'      => 'xxx',
        'shortcode'   => 'xxx',
        'callbackUrl' => 'xxx'
    ],
    'SmsGatewayWithMo'       => [
        'method'        => 'POST',
        'url'           => 'xxx',
        'private_token' => 'xxx',
        'prefix'        => '',
        'pnId'          => 'xxx',
        'npId'          => 'xxx',
        'type'          => 'xxx',
        'msgindex'      => 1,
        'totalMT'       => 1,
        'responseMap'   => [
            0 => 'Nhận dữ liệu thành công',
            1 => 'Nhận dữ liệu không thành công (lỗi hệ thống)',
            2 => 'Tham số không đúng định dạng, nhận dữ liệu không thành công',
            3 => 'Sai chữ ký xác thực',
            4 => 'Không tồn tại MOID'
        ]
    ],
    'SmsGatewayWithoutMo'    => [
        // config mặc định lưu vào db
        'companyId'     => 'TDM',
        'partnerId'     => 0,
        // config gửi tin fw
        'method'        => 'POST',
        'url'           => 'xxxx',
        'type'          => 'xxxx',
        'api'           => 'push',
        'msgindex'      => 1,
        'totalMT'       => 1,
        'gatePnid'      => 1, // Gateway cấp
        'gateNpid'      => 1, // Gateway cấp
        'pnId'          => 1, // Gateway cấp
        'private_token' => 'xxx', // Gateway cấp
        'prefix'        => 'xxx', // Gateway cấp
        'responseMap'   => [
            0 => 'Thành công',
            1 => 'Không thành công',
            2 => 'Sai tham số',
            3 => 'Sai chữ ký',
            4 => 'Quá hạn quota gửi tin trong ngày'
        ]
    ],
    'SmsGateway'             => [
        // Chuẩn tài liệu Thủ Đô
        'sentMtWithMo'    => [
            'method'        => 'POST',
            'url'           => 'xxx',
            'private_token' => 'xxx',
            'prefix'        => '',
            'pnId'          => 'xxx',
            'npId'          => 'xxx',
            'type'          => 'xxx',
            'msgindex'      => 1,
            'totalMT'       => 1,
            'responseMap'   => [
                0 => 'Nhận dữ liệu thành công',
                1 => 'Nhận dữ liệu không thành công (lỗi hệ thống)',
                2 => 'Tham số không đúng định dạng, nhận dữ liệu không thành công',
                3 => 'Sai chữ ký xác thực',
                4 => 'Không tồn tại MOID'
            ]
        ],
        // Chuẩn tài liệu Thủ Đô
        'sentMtWithoutMo' => [
            // config mặc định lưu vào db
            'companyId'     => 'TDM',
            'partnerId'     => 0,
            // config gửi tin fw
            'method'        => 'POST',
            'url'           => 'xxxx',
            'type'          => 'xxxx',
            'api'           => 'push',
            'msgindex'      => 1,
            'totalMT'       => 1,
            'gatePnid'      => 1, // Gateway cấp
            'gateNpid'      => 1, // Gateway cấp
            'pnId'          => 1, // Gateway cấp
            'private_token' => 'xxx', // Gateway cấp
            'prefix'        => 'xxx', // Gateway cấp
            'responseMap'   => [
                0 => 'Thành công',
                1 => 'Không thành công',
                2 => 'Sai tham số',
                3 => 'Sai chữ ký',
                4 => 'Quá hạn quota gửi tin trong ngày'
            ]
        ]
    ],
    'SmsGatewayVinaVasCloud' => [
        'method'               => 'POST',
        'shortcode'            => 1234,
        'brandname'            => 'xxx',
        'content_type'         => 'TEXT',
        'url'                  => 'xxxx',
        'timeout'              => 60,
        'username_cp'          => 'xxx',
        'account'              => 'xxx',
        'account_authenticate' => 'xxx',
        'authenticate'         => 'xxx',
        'cp_code'              => 'xxx',
        'cp_charge'            => 'xxx',
        'default_moid'         => 0,
        'service_code'         => 'xxx', // Sử dụng gói này khi push tin truyền thông
        'default_package'      => 'xxx', // Sử dụng gói này khi push tin truyền thông
        'package'              => 'xxx', // Sử dụng gói này khi trả tin cú pháp
        'default_price'        => 3000,
        'status'               => [
            0 => 'Success',
            1 => 'Failed'
        ],
        'responseIsSuccess'    => '<ACCESSGW><MODULE>SMSGW</MODULE><MESSAGE_TYPE>RESPONSE</MESSAGE_TYPE><COMMAND><error_id>0</error_id><error_desc>Success</error_desc></COMMAND></ACCESSGW>'
    ],
    'SmsGatewayViettel'      => [
        'private_token' => 'xxx',
        'prefix'        => '|',
        'method'        => 'GET',
        'timeout'       => 60,
        'api_link'      => 'xxx',
        'api_real'      => 'xxx',
        'api_test'      => 'xxx',
        'username'      => 'xxx',
        'password'      => 'xxx',
        'shortcode'     => 1234, // đầu thật: 9656, đầu test: 5407
        'alias'         => 1234,
        'params'        => 'TEXT',
        'responseMap'   => [
            '- 1' => 'Lỗi hệ thống',
            0     => 'Success',
            1     => 'Failed',
            2     => 'Không nhận biết được webservice',
            200   => 'Sai tham số',
            201   => 'Sai tên đăng nhập hoặc mật khẩu',
            202   => 'Request không hợp lệ',
            203   => 'Tham số request không hợp lệ',
            204   => 'Sai Shortcode',
            205   => 'Không cho phép gửi sms xuống thuê bao',
            206   => 'Sai tham số MOID',
            207   => 'Sai tham số datasign',
            400   => 'Server bận'
        ],
    ],
    'SmsServiceViettel'      => [
        'private_token' => 'xxx',
        'prefix'        => '|',
        'method'        => 'GET',
        'timeout'       => 60,
        'api_link'      => 'xxx',
        'api_real'      => 'xxx',
        'api_test'      => 'xxx',
        'username'      => 'xxx',
        'password'      => 'xxx',
        'shortcode'     => 1234, // đầu thật: 9656, đầu test: 5407
        'alias'         => 1234,
        'params'        => 'TEXT',
        'responseMap'   => [
            '- 1' => 'Lỗi hệ thống',
            0     => 'Success',
            1     => 'Failed',
            2     => 'Không nhận biết được webservice',
            200   => 'Sai tham số',
            201   => 'Sai tên đăng nhập hoặc mật khẩu',
            202   => 'Request không hợp lệ',
            203   => 'Tham số request không hợp lệ',
            204   => 'Sai Shortcode',
            205   => 'Không cho phép gửi sms xuống thuê bao',
            206   => 'Sai tham số MOID',
            207   => 'Sai tham số datasign',
            400   => 'Server bận'
        ],
    ],
];
