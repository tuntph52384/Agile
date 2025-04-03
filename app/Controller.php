<?php

namespace App;

class Controller
{
    public function validate($validator, $data, $rules)
    {
        $validation = $validator->make($data, $rules);

        // thêm validate
        $validation->validate();

        if($validation->fails())
        {
            return $validation->errors()->firstOfAll();
        }
        return [];
    }

    public function logError($message)
    {
        $data = date('d-m-Y');

        $message = date('d-m-Y H:i:s') . ' - ' . $message . PHP_EOL;

        // Type: 3 ghi vào file
        error_log($message, 3, "storage/logs/{$data}.log");
    }

    public function uploadFile(array $file, $folder = null)
    {
        // thông tin về file
        $fileTmpPath = $file['tmp_name'];  // Đường dẫn tạm thời của file
        $fileName = time() .'-'. $file['name']; // tên file chống trùng bằng timestamp

        $uploadDir = 'storage/uploads/' . $folder . '/';

        // tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir,0755, true);
        }

        // đường dẫn đầy đủ để lưu file
        $destPath = $uploadDir . $fileName;

        // di chuyển file từ thư mục tạm thời vào thư mục chính
        if(move_uploaded_file($fileTmpPath, $destPath)){
            return $destPath;
        }

        throw new \Exception('Lỗi upload file!');
    }
}